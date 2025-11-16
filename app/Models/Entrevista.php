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
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'observaciones',
        'solicitud_id',
        'asesor_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }
}
