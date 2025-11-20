<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrera extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'jornada',
        'grado',
        'director_id',
    ];

    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiante::class);
    }

    public function asignaturas(): HasMany
    {
        return $this->hasMany(Asignatura::class);
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    /**
     * Boot del modelo para validar que el director tenga el rol correcto.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($carrera) {
            if ($carrera->director_id) {
                $director = \App\Models\User::with(['rol', 'roles'])->find($carrera->director_id);
                
                if (!$director) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ['director_id' => ['El usuario seleccionado no existe.']]
                    );
                }

                // Verificar si el usuario tiene el rol "Director de carrera"
                $tieneRol = collect([$director->rol?->nombre])
                    ->merge($director->roles->pluck('nombre') ?? [])
                    ->map(fn ($rol) => mb_strtolower($rol ?? ''))
                    ->contains(mb_strtolower('Director de carrera'));

                if (!$tieneRol) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ['director_id' => ['El usuario seleccionado debe tener el rol "Director de carrera".']]
                    );
                }
            }
        });
    }
}
