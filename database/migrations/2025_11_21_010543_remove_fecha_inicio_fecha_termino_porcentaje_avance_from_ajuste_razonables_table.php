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
        Schema::table('ajuste_razonables', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_termino', 'porcentaje_avance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajuste_razonables', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('fecha_solicitud');
            $table->date('fecha_termino')->nullable()->after('fecha_inicio');
            $table->unsignedTinyInteger('porcentaje_avance')->default(0)->after('estado');
        });
    }
};
