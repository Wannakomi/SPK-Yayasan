<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active'         => 'boolean',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isKetua(): bool
    {
        return $this->role === 'ketua';
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'superadmin' => 'Super Admin',
            'admin'      => 'Admin',
            'viewer'     => 'Viewer',
            'ketua'      => 'Ketua Yayasan',
            default      => ucfirst($this->role),
        };
    }

    public function calonPenerima(): HasMany
    {
        return $this->hasMany(CalonPenerima::class, 'created_by');
    }

    public function kriteria(): HasMany
    {
        return $this->hasMany(Kriteria::class, 'created_by');
    }
}
