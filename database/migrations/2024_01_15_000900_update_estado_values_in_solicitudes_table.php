<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->enum('estado', [
                'Pendiente de entrevista',
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Pendiente de preaprobación',
                'Pendiente de Aprobación',
                'Aprobado',
                'Rechazado',
            ])->default('Pendiente de entrevista')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('estado')->default('pendiente')->change();
        });
    }
};
