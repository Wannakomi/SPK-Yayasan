<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'calon_penerima_id',
        'periode',
        'hasil_normalisasi',
        'hasil_terbobot',
        'mode_hitung',
        'skor_akhir',
        'ranking',
        'status_kelayakan',
    ];

    protected $casts = [
        'hasil_normalisasi' => 'array',
        'hasil_terbobot'    => 'array',
        'mode_hitung'       => 'integer',
        'skor_akhir'        => 'float',
        'ranking'           => 'integer',
    ];

    public function calonPenerima(): BelongsTo
    {
        return $this->belongsTo(CalonPenerima::class);
    }

    public function kriteria(): BelongsToMany
    {
        return $this->belongsToMany(Kriteria::class, 'penilaian_kriteria')
            ->withPivot('nilai_raw', 'nilai_normalisasi', 'nilai_terbobot')
            ->withTimestamps();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_kelayakan === 'layak' ? 'Layak' : 'Tidak Layak';
    }

    public function getIsLayakAttribute(): bool
    {
        return $this->status_kelayakan === 'layak';
    }

    public function getNormalisasi(string $kode): float
    {
        return (float) ($this->hasil_normalisasi[$kode] ?? 0);
    }

    public function getTerbobot(string $kode): float
    {
        return (float) ($this->hasil_terbobot[$kode] ?? 0);
    }
}
