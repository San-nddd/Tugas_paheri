<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tim extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tim';

    protected $primaryKey = 'id_tim';

    protected $fillable = [
        'id_kapten',
        'nama_tim',
        'foto_logo',
    ];

    public function kapten(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_kapten', 'id_pengguna');
    }

    public function pemain(): HasMany
    {
        return $this->hasMany(Pemain::class, 'id_tim', 'id_tim');
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'id_tim', 'id_tim');
    }
}
