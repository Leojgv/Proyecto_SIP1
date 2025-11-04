<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'fecha_solicitud',
        'descripcion',
        'estado',
        'estudiante_id',
        'asesor_pedagogico_id',
        'director_carrera_id',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function asesorPedagogico(): BelongsTo
    {
        return $this->belongsTo(AsesorPedagogico::class);
    }

    public function directorCarrera(): BelongsTo
    {
        return $this->belongsTo(DirectorCarrera::class);
    }

    public function ajustesRazonables(): HasMany
    {
        return $this->hasMany(AjusteRazonable::class);
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class);
    }

    public function entrevistas(): HasMany
    {
        return $this->hasMany(Entrevista::class);
    }
}
