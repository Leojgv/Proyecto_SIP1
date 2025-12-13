<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega el valor 'Listo para Enviar' al ENUM de la columna estado en la tabla solicitudes.
     */
    public function up(): void
    {
        // Modificar el ENUM para incluir 'Listo para Enviar'
        DB::statement("ALTER TABLE solicitudes MODIFY COLUMN estado ENUM(
            'Pendiente de entrevista',
            'Pendiente de formulación del caso',
            'Pendiente de formulación de ajuste',
            'Listo para Enviar',
            'Pendiente de preaprobación',
            'Pendiente de Aprobación',
            'Aprobado',
            'Rechazado'
        ) DEFAULT 'Pendiente de entrevista'");
    }

    /**
     * Reverse the migrations.
     * Remueve el valor 'Listo para Enviar' del ENUM.
     */
    public function down(): void
    {
        // Restaurar el ENUM sin 'Listo para Enviar'
        DB::statement("ALTER TABLE solicitudes MODIFY COLUMN estado ENUM(
            'Pendiente de entrevista',
            'Pendiente de formulación del caso',
            'Pendiente de formulación de ajuste',
            'Pendiente de preaprobación',
            'Pendiente de Aprobación',
            'Aprobado',
            'Rechazado'
        ) DEFAULT 'Pendiente de entrevista'");
    }
};
