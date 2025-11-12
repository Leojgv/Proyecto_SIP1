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
            ['nombre' => 'Asesora Pedagogica', 'descripcion' => 'Acompaña los procesos pedagógicos'],
            ['nombre' => 'Asesora Tecnica Pedagogica', 'descripcion' => 'Especialista en apoyo técnico pedagógico'],
            ['nombre' => 'Coordinadora de inclusion', 'descripcion' => 'Responsable de la inclusión educativa'],
            ['nombre' => 'Director de carrera', 'descripcion' => 'Director responsable de la carrera'],
            ['nombre' => 'Estudiante', 'descripcion' => 'Usuario estudiante'],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(
                ['nombre' => $rol['nombre']],
                ['descripcion' => $rol['descripcion']]
            );
        }
    }
}
