<?php

namespace App\Helpers;

use Carbon\Carbon;

class ChileanHolidays
{
    /**
     * Obtiene todos los feriados chilenos para un año específico
     *
     * @param int $year
     * @return array Array de fechas en formato 'Y-m-d' => nombre del feriado
     */
    public static function getHolidaysForYear(int $year): array
    {
        $holidays = [];
        
        // Feriados fijos (sin ajuste automático, se ajustarán después según reglas)
        $fixedHolidays = [
            "{$year}-01-01" => 'Año Nuevo',
            "{$year}-05-01" => 'Día del Trabajador',
            "{$year}-05-21" => 'Día de las Glorias Navales',
            "{$year}-06-29" => 'San Pedro y San Pablo',
            "{$year}-07-16" => 'Día de la Virgen del Carmen',
            "{$year}-08-15" => 'Asunción de la Virgen',
            "{$year}-09-18" => 'Fiestas Patrias',
            "{$year}-09-19" => 'Día del Ejército',
            "{$year}-10-12" => 'Encuentro de Dos Mundos',
            "{$year}-10-31" => 'Día de las Iglesias Evangélicas y Protestantes',
            "{$year}-11-01" => 'Día de Todos los Santos',
            "{$year}-12-08" => 'Inmaculada Concepción',
            "{$year}-12-25" => 'Navidad',
        ];
        
        $holidays = array_merge($holidays, $fixedHolidays);
        
        // Feriados variables (calculados)
        // Viernes Santo (2 días antes del Domingo de Resurrección)
        $easter = self::calculateEaster($year);
        $goodFriday = Carbon::createFromDate($year, $easter['month'], $easter['day'])->subDays(2);
        $holidays[$goodFriday->format('Y-m-d')] = 'Viernes Santo';
        
        // Sábado Santo (1 día antes del Domingo de Resurrección)
        $holySaturday = Carbon::createFromDate($year, $easter['month'], $easter['day'])->subDay();
        $holidays[$holySaturday->format('Y-m-d')] = 'Sábado Santo';
        
        // Ajustar feriados civiles que caen en domingo (se trasladan al lunes siguiente)
        // Feriados religiosos (Viernes Santo, Sábado Santo) NO se ajustan
        $adjustedHolidays = [];
        $religiousHolidays = ['Viernes Santo', 'Sábado Santo'];
        
        foreach ($holidays as $date => $name) {
            $carbonDate = Carbon::parse($date);
            $dayOfWeek = $carbonDate->dayOfWeek; // 0 = domingo, 1 = lunes, etc.
            
            // Los feriados religiosos no se ajustan
            if (in_array($name, $religiousHolidays)) {
                $adjustedHolidays[$date] = $name;
                continue;
            }
            
            // Si cae en domingo, se traslada al lunes siguiente
            if ($dayOfWeek == 0) {
                $adjustedDate = $carbonDate->copy()->addDay();
                $adjustedHolidays[$adjustedDate->format('Y-m-d')] = $name;
            }
            // Si cae en martes o miércoles, se traslada al lunes anterior
            elseif ($dayOfWeek == 2 || $dayOfWeek == 3) {
                $adjustedDate = $carbonDate->copy()->previous('Monday');
                $adjustedHolidays[$adjustedDate->format('Y-m-d')] = $name;
            }
            // Si cae en jueves o viernes, se traslada al lunes siguiente
            elseif ($dayOfWeek == 4 || $dayOfWeek == 5) {
                $adjustedDate = $carbonDate->copy()->next('Monday');
                $adjustedHolidays[$adjustedDate->format('Y-m-d')] = $name;
            }
            // Si cae en lunes o sábado, se mantiene
            else {
                $adjustedHolidays[$date] = $name;
            }
        }
        
        return $adjustedHolidays;
    }
    
    /**
     * Calcula la fecha de Pascua (Domingo de Resurrección) usando el algoritmo de Meeus/Jones/Butcher
     *
     * @param int $year
     * @return array ['year' => int, 'month' => int, 'day' => int]
     */
    private static function calculateEaster(int $year): array
    {
        $a = $year % 19;
        $b = intval($year / 100);
        $c = $year % 100;
        $d = intval($b / 4);
        $e = $b % 4;
        $f = intval(($b + 8) / 25);
        $g = intval(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intval($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intval(($a + 11 * $h + 22 * $l) / 451);
        $month = intval(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        
        return ['year' => $year, 'month' => $month, 'day' => $day];
    }

    /**
     * Verifica si una fecha es feriado en Chile
     *
     * @param string $date Fecha en formato 'Y-m-d'
     * @return bool
     */
    public static function isHoliday(string $date): bool
    {
        $year = (int) date('Y', strtotime($date));
        $holidays = self::getHolidaysForYear($year);
        
        return isset($holidays[$date]);
    }

    /**
     * Obtiene el nombre del feriado para una fecha específica
     *
     * @param string $date Fecha en formato 'Y-m-d'
     * @return string|null
     */
    public static function getHolidayName(string $date): ?string
    {
        $year = (int) date('Y', strtotime($date));
        $holidays = self::getHolidaysForYear($year);
        
        return $holidays[$date] ?? null;
    }

    /**
     * Obtiene todos los feriados para un rango de fechas
     *
     * @param string $startDate Fecha inicio en formato 'Y-m-d'
     * @param string $endDate Fecha fin en formato 'Y-m-d'
     * @return array
     */
    public static function getHolidaysInRange(string $startDate, string $endDate): array
    {
        $startYear = (int) date('Y', strtotime($startDate));
        $endYear = (int) date('Y', strtotime($endDate));
        $holidays = [];
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearHolidays = self::getHolidaysForYear($year);
            foreach ($yearHolidays as $date => $name) {
                if ($date >= $startDate && $date <= $endDate) {
                    $holidays[$date] = $name;
                }
            }
        }
        
        return $holidays;
    }
}

