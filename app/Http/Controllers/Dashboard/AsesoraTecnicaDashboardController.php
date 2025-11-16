<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AsesoraTecnicaDashboardController extends Controller
{
    public function show(Request $request)
    {
        $solicitudesBase = Solicitud::query();

        $casosPendientes = (clone $solicitudesBase)
            ->where(function ($query) {
                $query->whereNull('estado')
                    ->orWhereIn('estado', ['Pendiente', 'En espera']);
            })
            ->count();

        $casosEnProceso = (clone $solicitudesBase)
            ->whereIn('estado', ['En Proceso', 'En revisión', 'Derivado'])
            ->count();

        $casosCompletados = AjusteRazonable::query()
            ->whereHas('solicitud', fn ($query) => $query->whereIn('estado', ['Completado', 'Cerrado']))
            ->count();

        $tiempoPromedioDias = $this->calcularTiempoPromedioRespuesta(null);

        $metrics = [
            [
                'label' => 'Casos pendientes',
                'value' => $casosPendientes,
                'hint' => 'Por revisar',
                'icon' => 'fa-inbox',
            ],
            [
                'label' => 'En proceso',
                'value' => $casosEnProceso,
                'hint' => 'Trabajando en ellos',
                'icon' => 'fa-spinner',
            ],
            [
                'label' => 'Completados',
                'value' => $casosCompletados,
                'hint' => 'Este mes',
                'icon' => 'fa-circle-check',
            ],
            [
                'label' => 'Tiempo promedio',
                'value' => $tiempoPromedioDias ? $tiempoPromedioDias . ' días' : '—',
                'hint' => 'De respuesta',
                'icon' => 'fa-stopwatch',
            ],
        ];

        $assignedCases = (clone $solicitudesBase)
            ->with(['estudiante.carrera'])
            ->latest('fecha_solicitud')
            ->take(4)
            ->get()
            ->map(function (Solicitud $solicitud) {
                $carrera = optional(optional($solicitud->estudiante)->carrera)->nombre;
                return [
                    'case_id' => $solicitud->id,
                    'student' => trim(($solicitud->estudiante->nombre ?? 'Estudiante') . ' ' . ($solicitud->estudiante->apellido ?? '')),
                    'program' => $carrera ? $carrera : 'Sin carrera asignada',
                    'summary' => $solicitud->descripcion ?? 'Sin descripción registrada.',
                    'priority' => $this->inferirPrioridad($solicitud->estado),
                    'status' => $solicitud->estado ?? 'Pendiente',
                    'assigned_at' => optional($solicitud->fecha_solicitud ?? $solicitud->created_at)->format('Y-m-d'),
                ];
            })->toArray();

        $recentAdjustments = AjusteRazonable::query()
            ->with(['estudiante.carrera'])
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(function (AjusteRazonable $ajuste) {
                $carrera = optional(optional($ajuste->estudiante)->carrera)->nombre;
                return [
                    'student' => trim(($ajuste->estudiante->nombre ?? 'Estudiante') . ' ' . ($ajuste->estudiante->apellido ?? '')),
                    'program' => $carrera ?: 'Programa no asignado',
                    'description' => $ajuste->nombre ?? 'Ajuste sin título',
                    'status' => $ajuste->estado ?? 'Enviado',
                    'completed_at' => optional($ajuste->updated_at ?? $ajuste->fecha_termino)->format('Y-m-d') ?? 's/f',
                ];
            })->toArray();

        return view('asesora tecnica.dashboard', [
            'metrics' => $metrics,
            'assignedCases' => $assignedCases,
            'recentAdjustments' => $recentAdjustments,
        ]);
    }

    protected function calcularTiempoPromedioRespuesta(?object $user): ?float
    {
        $ajustes = AjusteRazonable::query()
            ->whereNotNull('fecha_solicitud')
            ->whereNotNull('fecha_inicio')
            ->whereHas('solicitud')
            ->get();

        if ($ajustes->isEmpty()) {
            return null;
        }

        $promedio = $ajustes->avg(function (AjusteRazonable $ajuste) {
            $inicio = $ajuste->fecha_inicio ?? $ajuste->updated_at;
            $solicitud = $ajuste->fecha_solicitud ?? $ajuste->created_at;
            if (! $inicio || ! $solicitud) {
                return null;
            }

            return Carbon::parse($solicitud)->diffInDays(Carbon::parse($inicio));
        });

        return $promedio ? round($promedio, 1) : null;
    }

    protected function inferirPrioridad(?string $estado): string
    {
        return match (strtolower((string) $estado)) {
            'en proceso', 'en revisión' => 'Media',
            'completado', 'cerrado' => 'Baja',
            default => 'Alta',
        };
    }
}
