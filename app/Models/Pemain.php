<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemain extends Model
{
    use HasFactory;

    protected $table = 'pemain';

    protected $primaryKey = 'id_pemain';

    protected $fillable = [
        'id_tim',
        'nama_game',
        'id_mlbb',
        'id_server',
    ];

    public function tim(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'id_tim', 'id_tim');
    }

    public function rosterTurnamen(): HasMany
    {
        return $this->hasMany(RosterTurnamen::class, 'id_pemain', 'id_pemain');
    }
}
