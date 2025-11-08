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
        if (Schema::hasTable('docente_asignaturas')) {
            return;
        }

        Schema::create('docente_asignaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->foreignId('asignatura_id')->constrained('asignaturas')->cascadeOnDelete();
            $table->foreignId('carrera_id')->constrained('carreras')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['docente_id', 'asignatura_id', 'carrera_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente_asignaturas');
    }
};
