<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';
    protected $primaryKey = 'id_pendaftaran';

    protected $fillable = [
        'id_turnamen',
        'id_tim',
        'bukti_pembayaran',
        'status_pendaftaran',
        'keterangan_penolakan',
    ];

    public function turnamen(): BelongsTo
    {
        return $this->belongsTo(Turnamen::class, 'id_turnamen', 'id_turnamen');
    }

    public function tim(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'id_tim', 'id_tim');
    }

    public function rosterTurnamen(): HasMany
    {
        return $this->hasMany(RosterTurnamen::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    /**
     * Daftar pemain (master data) yang didaftarkan lewat pivot roster_turnamen.
     */
    public function pemain(): HasManyThrough
    {
        return $this->hasManyThrough(
            Pemain::class,
            RosterTurnamen::class,
            'id_pendaftaran', // FK di roster_turnamen -> pendaftaran
            'id_pemain',      // FK di pemain -> id_pemain (local key pemain)
            'id_pendaftaran', // local key di pendaftaran
            'id_pemain'       // local key di roster_turnamen
        );
    }
}
