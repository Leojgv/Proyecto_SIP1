<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asignatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'estado',
        'carrera_id',
        'docente_id',
    ];

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(Docente::class, 'docente_asignaturas')
            ->withPivot('carrera_id')
            ->withTimestamps();
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(DocenteAsignatura::class);
    }
}
