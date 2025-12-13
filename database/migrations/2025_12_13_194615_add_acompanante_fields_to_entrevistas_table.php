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
        Schema::table('entrevistas', function (Blueprint $table) {
            $table->boolean('tiene_acompanante')->default(false)->after('modalidad');
            $table->string('acompanante_rut')->nullable()->after('tiene_acompanante');
            $table->string('acompanante_nombre')->nullable()->after('acompanante_rut');
            $table->string('acompanante_telefono')->nullable()->after('acompanante_nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrevistas', function (Blueprint $table) {
            $table->dropColumn(['tiene_acompanante', 'acompanante_rut', 'acompanante_nombre', 'acompanante_telefono']);
        });
    }
};
