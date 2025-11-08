<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'rol_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('rol_id')->nullable()->after('email')->constrained('roles')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'estudiante_id')) {
            $afterColumn = Schema::hasColumn('users', 'rol_id') ? 'rol_id' : 'email';

            Schema::table('users', function (Blueprint $table) use ($afterColumn) {
                $table->foreignId('estudiante_id')
                    ->nullable()
                    ->after($afterColumn)
                    ->constrained('estudiantes')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'estudiante_id')) {
                $table->dropForeign(['estudiante_id']);
                $table->dropColumn('estudiante_id');
            }

            if (Schema::hasColumn('users', 'rol_id')) {
                $table->dropForeign(['rol_id']);
                $table->dropColumn('rol_id');
            }
        });
    }
};
