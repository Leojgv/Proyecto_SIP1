<?php

namespace App\Imports;

use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EstudiantesImport implements ToModel, WithHeadingRow, WithValidation
{
    protected int $directorId;

    public function __construct(int $directorId)
    {
        $this->directorId = $directorId;
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
        $carreraNombre = $this->getValue($row, ['carrera', 'nombre_carrera', 'programa']);
        $rut = $this->getValue($row, ['rut', 'dni', 'documento']);
        $telefono = $this->getValue($row, ['telefono', 'phone', 'celular', 'tel']);
        $passwordPlano = $this->getValue($row, ['password', 'contrasena', 'contraseña', 'clave']);

        // Validar que tengamos los datos minimos
        if (empty($nombre) || empty($apellido) || empty($email) || empty($rut) || empty($carreraNombre)) {
            return null;
        }

        // Limpiar el email y convertir a minusculas
        $email = strtolower(trim($email));

        // Validar formato de email basico
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null; // Skip fila con email invalido
        }

        // Resolver la carrera por nombre para este director
        $carrera = $this->resolveCarrera($carreraNombre);

        if (!$carrera) {
            return null;
        }

        // Verificar si el usuario ya existe por email
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Crear nuevo User
            $password = Hash::make($passwordPlano ?: 'password123'); // Contrasena por defecto si no viene en Excel
            $user = User::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'password' => $password,
            ]);

            $this->assignStudentRole($user);
        } else {
            $this->assignStudentRole($user);

            // Si viene password en el Excel, actualizarlo
            if ($passwordPlano) {
                $user->update(['password' => Hash::make($passwordPlano)]);
            }
        }

        // Verificar si ya existe un estudiante con este email o user_id
        $estudiante = Estudiante::where('email', $email)
            ->orWhere('user_id', $user->id)
            ->orWhere('rut', $rut)
            ->first();

        if (!$estudiante) {
            // Crear nuevo registro de Estudiante
            $estudiante = Estudiante::create([
                'rut' => $rut ? trim($rut) : null,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'telefono' => $telefono ? trim($telefono) : null,
                'carrera_id' => $carrera->id,
                'user_id' => $user->id,
            ]);
        } else {
            // Actualizar el estudiante existente (actualizar carrera si es necesario)
            $estudiante->update([
                'rut' => $rut ? trim($rut) : ($estudiante->rut ?? null),
                'nombre' => $nombre,
                'apellido' => $apellido,
                'telefono' => $telefono ? trim($telefono) : ($estudiante->telefono ?? null),
                'carrera_id' => $carrera->id,
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
            // Buscar con diferentes variaciones de mayusculas/minusculas
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
     * Reglas de validacion para las filas
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'rut' => ['required', 'string', 'max:255'],
            'carrera' => [
                'required',
                Rule::exists('carreras', 'nombre')->where(fn ($query) => $query->where('director_id', $this->directorId)),
            ],
        ];
    }

    /**
     * Mensajes de validacion personalizados
     */
    public function customValidationMessages(): array
    {
        return [
            'email.email' => 'El formato del email no es valido.',
            'email.required' => 'El email es obligatorio.',
            'rut.required' => 'El RUT es obligatorio.',
            'carrera.required' => 'La carrera es obligatoria.',
            'carrera.exists' => 'La carrera indicada no pertenece a tu direccion de carrera.',
        ];
    }

    /**
     * Buscar la carrera por nombre dentro de las carreras del director.
     */
    protected function resolveCarrera(string $nombreCarrera): ?Carrera
    {
        $nombreNormalizado = mb_strtolower(trim($nombreCarrera));

        return Carrera::where('director_id', $this->directorId)
            ->get()
            ->first(function ($carrera) use ($nombreNormalizado) {
                return mb_strtolower($carrera->nombre) === $nombreNormalizado;
            });
    }

    /**
     * Asignar rol "Estudiante" en rol_id y en la tabla pivote si falta.
     */
    protected function assignStudentRole(User $user): void
    {
        $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();

        if (!$rolEstudiante) {
            return;
        }

        // Actualizar rol_id si no esta seteado
        if (!$user->rol_id) {
            $user->rol_id = $rolEstudiante->id;
            $user->save();
        }

        // Asegurar relacion en pivote
        if (! $user->roles->contains($rolEstudiante->id)) {
            $user->roles()->attach($rolEstudiante->id);
        }
    }
}
