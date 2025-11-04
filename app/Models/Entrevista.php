<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrevista extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'observaciones',
        'solicitud_id',
        'asesor_pedagogico_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function asesorPedagogico(): BelongsTo
    {
        return $this->belongsTo(AsesorPedagogico::class);
    }
}
