<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pertandingan extends Model
{
    use HasFactory;

    protected $table = 'pertandingan';
    protected $primaryKey = 'id_pertandingan';

    protected $fillable = [
        'id_turnamen',
        'babak',
        'id_tim_1',
        'id_tim_2',
        'skor_1',
        'skor_2',
        'id_tim_pemenang',
        'bukti_hasil',
        'status_pertandingan',
        'next_match_id',
    ];

    protected $casts = [
        'skor_1' => 'integer',
        'skor_2' => 'integer',
    ];

    public function turnamen(): BelongsTo
    {
        return $this->belongsTo(Turnamen::class, 'id_turnamen', 'id_turnamen');
    }

    public function timSatu(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'id_tim_1', 'id_tim');
    }

    public function timDua(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'id_tim_2', 'id_tim');
    }

    public function timPemenang(): BelongsTo
    {
        return $this->belongsTo(Tim::class, 'id_tim_pemenang', 'id_tim');
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(Pertandingan::class, 'next_match_id', 'id_pertandingan');
    }
}
