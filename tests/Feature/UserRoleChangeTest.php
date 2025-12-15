<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles necesarios
        $this->seedRoles();
    }

    private function seedRoles(): void
    {
        $roles = [
            ['nombre' => 'Admin', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'Docente', 'descripcion' => 'Usuario docente'],
            ['nombre' => 'Estudiante', 'descripcion' => 'Usuario estudiante'],
            ['nombre' => 'Director de carrera', 'descripcion' => 'Director responsable de la carrera'],
        ];

        foreach ($roles as $rol) {
            Rol::create($rol);
        }
    }

    /** @test */
    public function admin_puede_cambiar_rol_de_usuario()
    {
        // Crear un usuario admin
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        
        $rolAdmin = Rol::where('nombre', 'Admin')->first();
        $admin->rol_id = $rolAdmin->id;
        $admin->save();
        $admin->roles()->sync([$rolAdmin->id]);

        // Crear un usuario con rol Docente
        $rolDocente = Rol::where('nombre', 'Docente')->first();
        $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();
        
        $usuario = User::factory()->create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolDocente->id,
        ]);
        $usuario->roles()->sync([$rolDocente->id]);

        // Verificar que el usuario tiene el rol Docente inicialmente
        $this->assertEquals($rolDocente->id, $usuario->fresh()->rol_id);
        $this->assertTrue($usuario->fresh()->roles->contains($rolDocente->id));

        // Autenticar como admin
        $this->actingAs($admin);

        // Cambiar el rol a Estudiante
        $response = $this->put(route('admin.users.update', $usuario), [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@test.com',
            'rol_id' => $rolEstudiante->id,
        ]);

        // Verificar que la respuesta es exitosa
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        // Verificar que el rol se actualizó en la base de datos
        $usuario->refresh();
        $this->assertEquals($rolEstudiante->id, $usuario->rol_id, 'El rol_id no se actualizó correctamente');
        
        // Verificar que la relación roles() se sincronizó
        $this->assertTrue(
            $usuario->roles->contains($rolEstudiante->id),
            'El rol no se sincronizó en la relación roles()'
        );
        $this->assertFalse(
            $usuario->roles->contains($rolDocente->id),
            'El rol anterior todavía está en la relación roles()'
        );
        
        // Verificar que el rol principal (rol()) también se actualizó
        $this->assertEquals($rolEstudiante->id, $usuario->rol->id, 'El rol principal no se actualizó');
    }

    /** @test */
    public function cambio_de_rol_actualiza_ambas_relaciones()
    {
        // Crear roles
        $rolAdmin = Rol::where('nombre', 'Admin')->first();
        $rolDocente = Rol::where('nombre', 'Docente')->first();
        $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();

        // Crear admin
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolAdmin->id,
        ]);
        $admin->roles()->sync([$rolAdmin->id]);

        // Crear usuario con rol Docente
        $usuario = User::factory()->create([
            'nombre' => 'María',
            'apellido' => 'González',
            'email' => 'maria@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolDocente->id,
        ]);
        $usuario->roles()->sync([$rolDocente->id]);

        // Verificar estado inicial
        $this->assertEquals($rolDocente->id, $usuario->rol_id);
        $this->assertCount(1, $usuario->roles);
        $this->assertTrue($usuario->roles->contains($rolDocente->id));

        // Autenticar como admin y cambiar rol
        $this->actingAs($admin);
        
        $this->put(route('admin.users.update', $usuario), [
            'nombre' => 'María',
            'apellido' => 'González',
            'email' => 'maria@test.com',
            'rol_id' => $rolEstudiante->id,
        ]);

        // Verificar que ambas relaciones se actualizaron
        $usuario->refresh();
        
        // Verificar rol_id
        $this->assertEquals($rolEstudiante->id, $usuario->rol_id);
        
        // Verificar relación rol()
        $this->assertEquals($rolEstudiante->id, $usuario->rol->id);
        $this->assertEquals('Estudiante', $usuario->rol->nombre);
        
        // Verificar relación roles()
        $this->assertCount(1, $usuario->roles);
        $this->assertTrue($usuario->roles->contains($rolEstudiante->id));
        $this->assertFalse($usuario->roles->contains($rolDocente->id));
    }

    /** @test */
    public function validacion_funciona_correctamente()
    {
        $rolAdmin = Rol::where('nombre', 'Admin')->first();
        
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolAdmin->id,
        ]);
        $admin->roles()->sync([$rolAdmin->id]);

        $usuario = User::factory()->create([
            'nombre' => 'Test',
            'apellido' => 'User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($admin);

        // Intentar actualizar con rol_id inválido
        $response = $this->put(route('admin.users.update', $usuario), [
            'nombre' => 'Test',
            'apellido' => 'User',
            'email' => 'test@test.com',
            'rol_id' => 99999, // ID que no existe
        ]);

        $response->assertSessionHasErrors('rol_id');
    }
}
