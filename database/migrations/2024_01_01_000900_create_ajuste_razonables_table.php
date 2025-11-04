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
        Schema::create('ajuste_razonables', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha_solicitud');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_termino')->nullable();
            $table->string('estado')->default('pendiente');
            $table->unsignedTinyInteger('porcentaje_avance')->default(0);
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuste_razonables');
    }
};
