<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CalonPenerima extends Model
{
    protected $table = 'calon_penerima';

    protected $fillable = [
        'kode_anak',
        'nama',
        'nilai_kriteria',
        'jenjang',
        'kelas',
        'catatan',
        'periode',
        'created_by',
    ];

    protected $casts = [
        'nilai_kriteria' => 'array',
    ];

    public function getNilai(string $kode): mixed
    {
        return $this->nilai_kriteria[$kode] ?? null;
    }

    public function nilaiLengkapSampai($kriteria, int $n): bool
    {
        $kodeList = $kriteria->take($n)->pluck('kode_kriteria');
        foreach ($kodeList as $kode) {
            $val = $this->getNilai($kode);
            if ($val === null || $val === '') return false;
        }
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */
    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function penilaianAktif(): HasOne
    {
        $periode = Setting::get('periode_aktif', '2025/2026');
        return $this->hasOne(Penilaian::class)->where('periode', $periode);
    }

    public function kriteria(): BelongsToMany
    {
        return $this->belongsToMany(Kriteria::class, 'nilai_kriteria')
            ->withPivot('nilai')
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Generate kode otomatis
    |--------------------------------------------------------------------------
    */
    public static function generateKode(string $periode = null): string
    {
        $periode = $periode ?? Setting::get('periode_aktif', '2025/2026');

        $existing = self::where('periode', $periode)
            ->pluck('kode_anak')
            ->map(fn($k) => (int) substr($k, 1))
            ->sort()
            ->values()
            ->toArray();

        $next = 1;
        foreach ($existing as $num) {
            if ($num == $next) $next++;
            else break;
        }

        return 'A' . $next;
    }
}
