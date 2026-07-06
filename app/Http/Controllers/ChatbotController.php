<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\CalonPenerima;
use App\Models\Kriteria;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = $request->message;

        // =========================
        // DATA SISTEM
        // =========================
        $periode = Setting::get('periode_aktif', '2025/2026');

        $ranking = Penilaian::with('calonPenerima')
            ->where('periode', $periode)
            ->orderBy('ranking')
            ->get()
            ->map(function ($p) {

                return "#{$p->ranking} {$p->calonPenerima->nama} "
                    . "({$p->calonPenerima->kode_anak}) "
                    . "- Skor: " . round($p->skor_akhir, 4)
                    . " - " . strtoupper($p->status_kelayakan);

            })
            ->join("\n");

        $kriteria = Kriteria::orderBy('kode_kriteria')
            ->get()
            ->map(function ($k) {

                return "- {$k->kode_kriteria}: "
                    . "{$k->nama_kriteria} "
                    . "({$k->atribut}, bobot "
                    . ($k->bobot * 100)
                    . "%)";

            })
            ->join("\n");

        $totalCalon = CalonPenerima::where('periode', $periode)->count();

        $layak = Penilaian::where('periode', $periode)
            ->where('status_kelayakan', 'layak')
            ->count();

        $threshold = Setting::get('threshold_layak', '0.75');

        // =========================
        // SYSTEM PROMPT
        // =========================
$systemPrompt = "
Kamu adalah AI assistant bernama Butterflies AI.

PERSONALITAS:
- Santai
- Natural
- Tidak terlalu formal
- Bisa bercanda ringan
- Ngobrol seperti teman
- Pakai bahasa Indonesia yang fleksibel
- Jangan terlalu kaku atau seperti robot
- Kalau user bercanda, balas santai
- Kalau user bahas anime, game, teknologi, cinta, sekolah, atau hal random, ikut ngobrol normal
- Jangan selalu membawa topik ke SPK

KAMU TETAP PUNYA AKSES DATA SISTEM:

DATA PERIODE {$periode}
- Total calon: {$totalCalon}
- Dinyatakan layak: {$layak}
- Threshold: Vi >= {$threshold}

KRITERIA:
{$kriteria}

RANKING:
{$ranking}

ATURAN:
- Kalau user tanya data SPK → jawab berdasarkan data di atas
- Kalau user ngobrol random → balas normal seperti AI assistant umum
- Jangan halu atau mengarang data sistem
- Jawaban jangan terlalu panjang
- Gunakan gaya bahasa yang natural dan manusiawi
";

        try {

            $response = Http::timeout(30)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                    'Content-Type'  => 'application/json',
                ])
                ->post(
                    'https://api.groq.com/openai/v1/chat/completions',
                    [
                        'model' => 'llama-3.3-70b-versatile',

                        'messages' => [
                            [
                                'role'    => 'system',
                                'content' => $systemPrompt
                            ],
                            [
                                'role'    => 'user',
                                'content' => $message
                            ],
                        ],


                        'temperature' => 1,
                        'max_tokens'  => 700,


                    ]
                );

            // =========================
            // ERROR API
            // =========================
            if ($response->failed()) {

                \Log::error(
                    'Groq API Error: ' . $response->body()
                );

                return response()->json([
                    'reply' => 'Maaf, AI sedang tidak tersedia.'
                ]);
            }

            // =========================
            // AMBIL JAWABAN AI
            // =========================
            $reply = $response->json(
                'choices.0.message.content'
            );

            if (!$reply) {

                $reply = 'Maaf, AI tidak memberikan jawaban.';
            }

            return response()->json([
                'reply' => trim($reply)
            ]);

        } catch (\Exception $e) {

            \Log::error(
                'Chatbot Error: ' . $e->getMessage()
            );

            return response()->json([
                'reply' => 'Maaf, terjadi kesalahan koneksi ke AI.'
            ]);
        }
    }
}
