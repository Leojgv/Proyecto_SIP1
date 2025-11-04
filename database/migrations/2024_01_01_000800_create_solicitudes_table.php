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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_solicitud');
            $table->text('descripcion')->nullable();
            $table->string('estado')->default('pendiente');
            $table->foreignId('estudiante_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asesor_pedagogico_id')->nullable()->constrained('asesor_pedagogicos')->nullOnDelete();
            $table->foreignId('director_carrera_id')->nullable()->constrained('director_carreras')->nullOnDelete();
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
