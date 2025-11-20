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
        'asesor_id',
        'director_id',
    ];

    /**
     * El estado se gestiona automáticamente según el flujo del proceso.
     * No debe ser modificado manualmente excepto por los controladores específicos
     * que implementan las transiciones del flujo.
     */

    protected $casts = [
        'fecha_solicitud' => 'date',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asesor_id');
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

    /**
     * Obtiene el director de carrera automáticamente según la carrera del estudiante.
     * 
     * @return int|null ID del director de carrera o null si no se encuentra
     */
    public function obtenerDirectorId(): ?int
    {
        $estudiante = $this->estudiante;
        
        if (!$estudiante) {
            return null;
        }

        $carrera = $estudiante->carrera;
        
        if (!$carrera) {
            return null;
        }

        return $carrera->director_id;
    }

    /**
     * Asigna automáticamente el director de carrera según la carrera del estudiante.
     * 
     * @return bool True si se asignó correctamente, false si no se pudo asignar
     */
    public function asignarDirectorAutomatico(): bool
    {
        $directorId = $this->obtenerDirectorId();
        
        if ($directorId) {
            $this->update(['director_id' => $directorId]);
            return true;
        }

        return false;
    }
}
