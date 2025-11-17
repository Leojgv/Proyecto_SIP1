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
        if (Schema::hasTable('solicitudes')) {
            return;
        }

        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_solicitud');
            $table->text('descripcion')->nullable();
            $table->enum('estado', [
                'Pendiente de entrevista',
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Pendiente de preaprobación',
                'Pendiente de Aprobación',
                'Aprobado',
                'Rechazado',
            ])->default('Pendiente de entrevista');
            $table->foreignId('estudiante_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coordinadora_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asesor_tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asesor_pedagogico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('director_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
