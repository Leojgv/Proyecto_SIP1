<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Docente extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'especialidad',
    ];

    public function asignaturas(): HasMany
    {
        return $this->hasMany(Asignatura::class);
    }

    public function asignaturasColaboracion(): BelongsToMany
    {
        return $this->belongsToMany(Asignatura::class, 'docente_asignaturas')
            ->withPivot('carrera_id')
            ->withTimestamps();
    }

    public function carreras(): BelongsToMany
    {
        return $this->belongsToMany(Carrera::class, 'docente_asignaturas')
            ->withPivot('asignatura_id')
            ->withTimestamps();
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(DocenteAsignatura::class);
    }
}
