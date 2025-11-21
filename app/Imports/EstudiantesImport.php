<?php

namespace App\Imports;

use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class EstudiantesImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $carreraId;

    public function __construct(int $carreraId)
    {
        $this->carreraId = $carreraId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Normalizar los nombres de las columnas (case insensitive)
        $nombre = $this->getValue($row, ['nombre', 'name', 'nombres']);
        $apellido = $this->getValue($row, ['apellido', 'lastname', 'apellidos']);
        $email = $this->getValue($row, ['email', 'correo', 'e_mail']);
        $rut = $this->getValue($row, ['rut', 'dni', 'documento']);
        $telefono = $this->getValue($row, ['telefono', 'phone', 'celular', 'tel']);

        // Validar que tengamos los datos mínimos
        if (empty($nombre) || empty($apellido) || empty($email)) {
            // Skip esta fila si no tiene los datos mínimos
            return null;
        }

        // Limpiar el email y convertir a minúsculas
        $email = strtolower(trim($email));

        // Validar formato de email básico
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null; // Skip fila con email inválido
        }

        // Verificar si el usuario ya existe por email
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Crear nuevo User
            $password = Hash::make('password123'); // Contraseña por defecto
            $user = User::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'password' => $password,
            ]);

            // Obtener el rol "Estudiante"
            $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();

            if ($rolEstudiante) {
                // Asignar el rol mediante la tabla pivote rol_user
                $user->roles()->attach($rolEstudiante->id);
            }
        } else {
            // Si el usuario existe pero no tiene el rol Estudiante, asignárselo
            $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();
            if ($rolEstudiante && !$user->roles->contains($rolEstudiante->id)) {
                $user->roles()->attach($rolEstudiante->id);
            }
        }

        // Verificar si ya existe un estudiante con este email o user_id
        $estudiante = Estudiante::where('email', $email)
            ->orWhere('user_id', $user->id)
            ->first();

        if (!$estudiante) {
            // Crear nuevo registro de Estudiante
            $estudiante = Estudiante::create([
                'rut' => $rut ? trim($rut) : null,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'telefono' => $telefono ? trim($telefono) : null,
                'carrera_id' => $this->carreraId,
                'user_id' => $user->id,
            ]);
        } else {
            // Actualizar el estudiante existente (actualizar carrera si es necesario)
            $estudiante->update([
                'rut' => $rut ? trim($rut) : ($estudiante->rut ?? null),
                'nombre' => $nombre,
                'apellido' => $apellido,
                'telefono' => $telefono ? trim($telefono) : ($estudiante->telefono ?? null),
                'carrera_id' => $this->carreraId,
                'user_id' => $user->id,
            ]);
        }

        return $estudiante;
    }

    /**
     * Normalizar y obtener valor de las columnas
     */
    protected function getValue(array $row, array $possibleKeys): ?string
    {
        foreach ($possibleKeys as $key) {
            // Buscar con diferentes variaciones de mayúsculas/minúsculas
            $variations = [
                $key,
                Str::lower($key),
                Str::upper($key),
                Str::title($key),
                Str::snake($key),
                Str::camel($key),
            ];

            foreach ($variations as $variation) {
                if (isset($row[$variation]) && !empty($row[$variation])) {
                    return trim((string) $row[$variation]);
                }
            }
        }

        return null;
    }

    /**
     * Reglas de validación para las filas
     */
    public function rules(): array
    {
        return [
            'nombre' => ['nullable', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    public function customValidationMessages(): array
    {
        return [
            'email.email' => 'El formato del email no es válido.',
        ];
    }
}

