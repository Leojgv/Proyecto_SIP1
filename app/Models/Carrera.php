<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Carrera extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'jornada',
        'grado',
    ];

    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiante::class);
    }

    public function asignaturas(): HasMany
    {
        return $this->hasMany(Asignatura::class);
    }

    public function directorCarrera(): HasOne
    {
        return $this->hasOne(DirectorCarrera::class);
    }

    public function docenteAsignaturas(): HasMany
    {
        return $this->hasMany(DocenteAsignatura::class);
    }
}
