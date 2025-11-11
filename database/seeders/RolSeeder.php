<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Admin', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'Estudiante', 'descripcion' => 'Usuario estudiante'],
            ['nombre' => 'Asesor', 'descripcion' => 'Asesor pedagogico'],
            ['nombre' => 'Docente', 'descripcion' => 'Profesor de asignaturas'],
            ['nombre' => 'Director de Carrera', 'descripcion' => 'Director responsable de la carrera'],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(
                ['nombre' => $rol['nombre']],
                ['descripcion' => $rol['descripcion']]
            );
        }
    }
}
