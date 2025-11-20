<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Esta migración asegura que solo usuarios con el rol "Director de carrera"
     * puedan ser asignados como directores en la tabla carreras.
     */
    public function up(): void
    {
        // Crear un trigger que valide que el director_id tenga el rol correcto
        // Solo funciona en MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('
                DROP TRIGGER IF EXISTS validate_director_carrera_before_insert;
            ');
            
            DB::unprepared('
                CREATE TRIGGER validate_director_carrera_before_insert
                BEFORE INSERT ON carreras
                FOR EACH ROW
                BEGIN
                    IF NEW.director_id IS NOT NULL THEN
                        IF NOT EXISTS (
                            SELECT 1 
                            FROM users u
                            LEFT JOIN rol_user ru ON u.id = ru.user_id
                            LEFT JOIN roles r ON (ru.rol_id = r.id OR u.rol_id = r.id)
                            WHERE u.id = NEW.director_id 
                            AND LOWER(r.nombre) = LOWER("Director de carrera")
                        ) THEN
                            SIGNAL SQLSTATE "45000"
                            SET MESSAGE_TEXT = "El usuario asignado como director debe tener el rol \"Director de carrera\"";
                        END IF;
                    END IF;
                END;
            ');

            DB::unprepared('
                DROP TRIGGER IF EXISTS validate_director_carrera_before_update;
            ');
            
            DB::unprepared('
                CREATE TRIGGER validate_director_carrera_before_update
                BEFORE UPDATE ON carreras
                FOR EACH ROW
                BEGIN
                    IF NEW.director_id IS NOT NULL AND (NEW.director_id != OLD.director_id OR OLD.director_id IS NULL) THEN
                        IF NOT EXISTS (
                            SELECT 1 
                            FROM users u
                            LEFT JOIN rol_user ru ON u.id = ru.user_id
                            LEFT JOIN roles r ON (ru.rol_id = r.id OR u.rol_id = r.id)
                            WHERE u.id = NEW.director_id 
                            AND LOWER(r.nombre) = LOWER("Director de carrera")
                        ) THEN
                            SIGNAL SQLSTATE "45000"
                            SET MESSAGE_TEXT = "El usuario asignado como director debe tener el rol \"Director de carrera\"";
                        END IF;
                    END IF;
                END;
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS validate_director_carrera_before_insert;');
            DB::unprepared('DROP TRIGGER IF EXISTS validate_director_carrera_before_update;');
        }
    }
};

