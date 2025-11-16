<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrevistas', function (Blueprint $table) {
            if (! Schema::hasColumn('entrevistas', 'fecha_hora_inicio')) {
                $table->dateTime('fecha_hora_inicio')->nullable()->after('fecha');
            }

            if (! Schema::hasColumn('entrevistas', 'fecha_hora_fin')) {
                $table->dateTime('fecha_hora_fin')->nullable()->after('fecha_hora_inicio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('entrevistas', function (Blueprint $table) {
            if (Schema::hasColumn('entrevistas', 'fecha_hora_inicio')) {
                $table->dropColumn('fecha_hora_inicio');
            }

            if (Schema::hasColumn('entrevistas', 'fecha_hora_fin')) {
                $table->dropColumn('fecha_hora_fin');
            }
        });
    }
};
