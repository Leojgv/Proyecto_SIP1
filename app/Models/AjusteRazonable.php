<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AjusteRazonable extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fecha_solicitud',
        'fecha_inicio',
        'fecha_termino',
        'estado',
        'porcentaje_avance',
        'solicitud_id',
        'estudiante_id',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
        'porcentaje_avance' => 'integer',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }
}
