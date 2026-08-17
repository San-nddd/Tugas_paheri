<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turnamen extends Model
{
    use HasFactory;

    protected $table = 'turnamen';
    protected $primaryKey = 'id_turnamen';

    protected $fillable = [
        'id_penyelenggara',
        'nama_turnamen',
        'deskripsi',
        'kuota_maksimal',
        'biaya',
        'kode_akses',
        'status_turnamen',
        'tanggal',
    ];

    protected $casts = [
        'tanggal'         => 'date',
        'biaya'           => 'decimal:2',
        'kuota_maksimal'  => 'integer',
    ];

    public function penyelenggara(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_penyelenggara', 'id_pengguna');
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'id_turnamen', 'id_turnamen');
    }

    public function pertandingan(): HasMany
    {
        return $this->hasMany(Pertandingan::class, 'id_turnamen', 'id_turnamen');
    }

    /**
     * Jumlah tim yang sudah disetujui pada turnamen ini.
     */
    public function jumlahTimDisetujui(): int
    {
        return $this->pendaftaran()->where('status_pendaftaran', 'disetujui')->count();
    }
}
