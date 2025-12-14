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
            if (!Schema::hasColumn('entrevistas', 'estado')) {
                $table->string('estado')->default('Agendada')->after('observaciones');
            }
            if (!Schema::hasColumn('entrevistas', 'motivo_posposicion')) {
                $table->text('motivo_posposicion')->nullable()->after('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrevistas', function (Blueprint $table) {
            if (Schema::hasColumn('entrevistas', 'estado')) {
                $table->dropColumn('estado');
            }
            if (Schema::hasColumn('entrevistas', 'motivo_posposicion')) {
                $table->dropColumn('motivo_posposicion');
            }
        });
    }
};
