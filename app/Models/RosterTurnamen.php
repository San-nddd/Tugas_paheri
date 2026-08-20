<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RosterTurnamen extends Model
{
    use HasFactory;

    protected $table = 'roster_turnamen';

    protected $primaryKey = 'id_roster';

    protected $fillable = [
        'id_pendaftaran',
        'id_pemain',
        'peran_game',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function pemain(): BelongsTo
    {
        return $this->belongsTo(Pemain::class, 'id_pemain', 'id_pemain');
    }
}
