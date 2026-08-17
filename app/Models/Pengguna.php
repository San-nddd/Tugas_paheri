<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'no_telepon',
        'peran',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }

    public function turnamenDiselenggarakan(): HasMany
    {
        return $this->hasMany(Turnamen::class, 'id_penyelenggara', 'id_pengguna');
    }

    public function timDikapteni(): HasMany
    {
        return $this->hasMany(Tim::class, 'id_kapten', 'id_pengguna');
    }

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isPenyelenggara(): bool
    {
        return $this->peran === 'penyelenggara';
    }

    public function isKaptenTim(): bool
    {
        return $this->peran === 'kapten_tim';
    }
}
