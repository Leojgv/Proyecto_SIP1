<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
        ]);

        $adminRoleId = Rol::where('nombre', 'Admin')->value('id');

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'rol_id' => $adminRoleId,
        ]);
    }
}
