<?php

namespace App\Imports;

use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DocentesImport implements ToModel, WithHeadingRow, WithValidation
{
    protected int $directorId;
    protected const DEFAULT_PASSWORD = 'Inacap.2030';

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
            // Crear nuevo User con contraseña por defecto
            $user = User::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'password' => Hash::make(self::DEFAULT_PASSWORD),
            ]);

            $this->assignDocenteRole($user);
        } else {
            $this->assignDocenteRole($user);

            // Actualizar contraseña a la por defecto
            $user->update(['password' => Hash::make(self::DEFAULT_PASSWORD)]);
        }

        // Verificar si ya existe un docente con este email, user_id o rut
        $docente = Docente::where('email', $email)
            ->orWhere('user_id', $user->id)
            ->orWhere('rut', $rut)
            ->first();

        if (!$docente) {
            // Crear nuevo registro de Docente
            $docente = Docente::create([
                'rut' => trim($rut),
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'carrera_id' => $carrera->id,
                'user_id' => $user->id,
            ]);
        } else {
            // Actualizar el docente existente
            $docente->update([
                'rut' => trim($rut),
                'nombre' => $nombre,
                'apellido' => $apellido,
                'carrera_id' => $carrera->id,
                'user_id' => $user->id,
            ]);
        }

        return $docente;
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
     * Asignar rol "Docente" en rol_id y en la tabla pivote si falta.
     */
    protected function assignDocenteRole(User $user): void
    {
        $rolDocente = Rol::where('nombre', 'Docente')->first();

        if (!$rolDocente) {
            // Crear el rol si no existe
            $rolDocente = Rol::create([
                'nombre' => 'Docente',
                'descripcion' => 'Usuario docente',
            ]);
        }

        // Actualizar rol_id si no esta seteado
        if (!$user->rol_id) {
            $user->rol_id = $rolDocente->id;
            $user->save();
        }

        // Asegurar relacion en pivote
        if (! $user->roles->contains($rolDocente->id)) {
            $user->roles()->attach($rolDocente->id);
        }
    }
}




