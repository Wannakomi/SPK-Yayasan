<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'atribut',
        'bobot',
        'satuan',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'bobot' => 'float',
    ];

    public function penilaian(): BelongsToMany
    {
        return $this->belongsToMany(Penilaian::class, 'penilaian_kriteria')
            ->withPivot('nilai_raw', 'nilai_normalisasi', 'nilai_terbobot')
            ->withTimestamps();
    }

    public function calonPenerima(): BelongsToMany
    {
        return $this->belongsToMany(CalonPenerima::class, 'nilai_kriteria')
            ->withPivot('nilai')
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAtributLabelAttribute(): string
    {
        return strtoupper($this->atribut);
    }

    public function getBobotPersenAttribute(): string
    {
        return ($this->bobot * 100) . '%';
    }

    public static function totalBobot(): float
    {
        return (float) self::sum('bobot');
    }

    public static function bobotValid(): bool
    {
        return abs(self::totalBobot() - 1.0) < 0.001;
    }
}
