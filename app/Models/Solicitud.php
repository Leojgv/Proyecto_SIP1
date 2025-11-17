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
        'motivo_rechazo',
        'estudiante_id',
        'coordinadora_id',
        'asesor_tecnico_id',
        'asesor_pedagogico_id',
        'director_id',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function coordinadora(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinadora_id');
    }

    public function asesorTecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesor_tecnico_id');
    }

    public function asesorPedagogico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesor_pedagogico_id');
    }

    public function asesor(): BelongsTo
    {
        return $this->asesorPedagogico();
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
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
