<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Notifications\DashboardNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DirectorCarreraCasoController extends Controller
{
    public function index(Request $request)
    {
        $directorId = $request->user()->id;

        // El Director de Carrera ve casos que están en su fase:
        // - Pendiente de preaprobación (casos enviados por Asesora Pedagógica)
        // - Pendiente de Aprobacion (casos que debe revisar y aprobar/rechazar)
        // También puede ver casos ya procesados (Aprobado/Rechazado) para historial
        $solicitudes = Solicitud::with(['estudiante.carrera', 'asesor', 'director', 'ajustesRazonables'])
            ->where(function ($query) use ($directorId) {
                $query->where('director_id', $directorId)
                    ->orWhereHas('estudiante.carrera', fn ($sub) => $sub->where('director_id', $directorId));
            })
            ->whereIn('estado', [
                'Pendiente de preaprobación',
                'Pendiente de Aprobacion',
                'Pendiente de Aprobación',
                'Aprobado',
                'Rechazado',
            ])
            ->latest('fecha_solicitud')
            ->get();

        $solicitudesPorEstudiante = $solicitudes->groupBy('estudiante_id');

        return view('DirectorCarrera.casos.index', [
            'solicitudesPorEstudiante' => $solicitudesPorEstudiante,
        ]);
    }

    public function show(Request $request, Solicitud $solicitud): View
    {
        $solicitud->load(['estudiante.carrera', 'asesor', 'director', 'ajustesRazonables', 'evidencias', 'entrevistas']);

        return view('DirectorCarrera.casos.show', [
            'solicitud' => $solicitud,
        ]);
    }

    public function approve(Request $request, Solicitud $solicitud): RedirectResponse
    {
        // Verificar que el estado actual permita esta transición
        // Acepta tanto "Pendiente de preaprobación" como "Pendiente de Aprobacion"
        $estadosPermitidos = ['Pendiente de preaprobación', 'Pendiente de Aprobacion', 'Pendiente de Aprobación'];
        if (!in_array($solicitud->estado, $estadosPermitidos)) {
            return back()->with('error', 'Solo se pueden aprobar solicitudes que estén en estado "Pendiente de preaprobación" o "Pendiente de Aprobación".');
        }

        // Validar que se hayan seleccionado ajustes
        $ajustesSeleccionados = $request->input('ajustes', []);
        $motivosRechazo = $request->input('motivos_rechazo', []);

        if (empty($ajustesSeleccionados)) {
            return back()->with('error', 'Debes seleccionar la aprobación o rechazo para cada ajuste razonable.');
        }

        // Validar que todos los ajustes rechazados tengan motivo
        foreach ($ajustesSeleccionados as $ajusteId => $decision) {
            if ($decision === 'rechazado') {
                if (empty($motivosRechazo[$ajusteId]) || trim($motivosRechazo[$ajusteId]) === '') {
                    return back()->with('error', "Debes ingresar un motivo de rechazo para todos los ajustes rechazados.");
                }
            }
        }

        // Actualizar cada ajuste individualmente
        foreach ($ajustesSeleccionados as $ajusteId => $decision) {
            $ajuste = $solicitud->ajustesRazonables()->find($ajusteId);
            
            if ($ajuste) {
                if ($decision === 'aprobado') {
                    $ajuste->update([
                        'estado' => 'Aprobado',
                        'motivo_rechazo' => null,
                    ]);
                } else {
                    $ajuste->update([
                        'estado' => 'Rechazado',
                        'motivo_rechazo' => $motivosRechazo[$ajusteId] ?? null,
                    ]);
                }
            }
        }

        // Verificar si todos los ajustes fueron aprobados
        $todosAprobados = $solicitud->ajustesRazonables()->where('estado', '!=', 'Aprobado')->count() === 0;
        $algunosAprobados = $solicitud->ajustesRazonables()->where('estado', 'Aprobado')->count() > 0;

        // Actualizar el estado de la solicitud
        if ($todosAprobados) {
            // Todos los ajustes aprobados
        $solicitud->update([
            'estado' => 'Aprobado',
            'motivo_rechazo' => null,
        ]);
        } elseif ($algunosAprobados) {
            // Algunos ajustes aprobados, algunos rechazados - el caso queda aprobado parcialmente
            $solicitud->update([
            'estado' => 'Aprobado',
                'motivo_rechazo' => null,
        ]);
        } else {
            // Todos rechazados - debería manejarse como rechazo total
            return back()->with('error', 'No puedes rechazar todos los ajustes. Usa la opción "Rechazar/Devolver" para rechazar el caso completo.');
        }

        // Notificar al estudiante sobre ajustes aprobados y rechazados
        $this->notifyStudentOnAdjustmentDecision($solicitud, $ajustesSeleccionados, $motivosRechazo);
        
        // Notificar a docentes sobre los ajustes aprobados (solo los aprobados)
        $this->notifyTeachers($solicitud);

        // También notificar a la Asesora Técnica
        $asesorTecnico = \App\Models\User::withRole('Asesora Tecnica Pedagogica')->first();
        if ($asesorTecnico) {
            Notification::send(
                $asesorTecnico,
                new DashboardNotification(
                    'Solicitud aprobada',
                    "La solicitud de {$solicitud->estudiante->nombre} {$solicitud->estudiante->apellido} ha sido aprobada con ajustes seleccionados.",
                    route('asesora-tecnica.casos.index'),
                    'Ver casos'
                )
            );
        }

        return redirect()
            ->route('director.dashboard')
            ->with('status', 'Solicitud procesada exitosamente. Los ajustes seleccionados han sido notificados a los docentes correspondientes.');
    }

    public function reject(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $validated = $request->validate([
            'motivo_rechazo' => ['required', 'string', 'min:5'],
        ]);

        // Verificar que el estado actual permita esta transición
        $estadosPermitidos = ['Pendiente de preaprobación', 'Pendiente de Aprobacion', 'Pendiente de Aprobación'];
        if (!in_array($solicitud->estado, $estadosPermitidos)) {
            return back()->with('error', 'Solo se pueden rechazar solicitudes que estén en estado "Pendiente de preaprobación" o "Pendiente de Aprobación".');
        }

        $solicitud->update([
            'estado' => 'Rechazado',
            'motivo_rechazo' => $validated['motivo_rechazo'],
        ]);

        $this->notifyOnRejection($solicitud, $validated['motivo_rechazo']);

        return redirect()
            ->route('director.dashboard')
            ->with('status', 'Solicitud rechazada y notificada correctamente.');
    }

    /**
     * Devuelve la solicitud a CTP para correcciones.
     * Cambia el estado a "Pendiente de formulación de ajuste".
     */
    public function devolverACTP(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $validated = $request->validate([
            'motivo_devolucion' => ['required', 'string', 'min:5'],
        ]);

        // Verificar que el estado actual permita esta transición
        $estadosPermitidos = ['Pendiente de preaprobación', 'Pendiente de Aprobacion', 'Pendiente de Aprobación'];
        if (!in_array($solicitud->estado, $estadosPermitidos)) {
            return back()->with('error', 'Solo se pueden devolver solicitudes que estén en estado "Pendiente de preaprobación" o "Pendiente de Aprobación".');
        }

        $solicitud->update([
            'estado' => 'Pendiente de formulación de ajuste',
            'motivo_rechazo' => $validated['motivo_devolucion'],
        ]);

        $this->notifyOnReturnToCTP($solicitud, $validated['motivo_devolucion']);

        return redirect()
            ->route('director.dashboard')
            ->with('status', 'Solicitud devuelta a CTP para correcciones y notificada.');
    }

    protected function notifyOnApproval(Solicitud $solicitud): void
    {
        $estudianteUser = optional($solicitud->estudiante)->user;
        $asesor = $solicitud->asesor;

        Notification::send(
            collect([$estudianteUser, $asesor])->filter(),
            new DashboardNotification(
                'Solicitud aprobada',
                'La solicitud ha sido aprobada. Revisa los ajustes aplicables.',
                route('solicitudes.show', $solicitud),
                'Ver solicitud'
            )
        );
    }

    protected function notifyStudentOnAdjustmentDecision(Solicitud $solicitud, array $ajustesSeleccionados, array $motivosRechazo): void
    {
        $estudianteUser = optional($solicitud->estudiante)->user;
        if (!$estudianteUser) {
            return;
        }

        $solicitud->load('ajustesRazonables');
        $estudiante = $solicitud->estudiante;

        // Contar ajustes aprobados y rechazados
        $ajustesAprobados = [];
        $ajustesRechazados = [];

        foreach ($ajustesSeleccionados as $ajusteId => $decision) {
            $ajuste = $solicitud->ajustesRazonables->find($ajusteId);
            if ($ajuste) {
                if ($decision === 'aprobado') {
                    $ajustesAprobados[] = $ajuste->nombre ?? 'Ajuste sin nombre';
                } else {
                    $ajustesRechazados[] = $ajuste->nombre ?? 'Ajuste sin nombre';
                }
            }
        }

        // Notificar sobre ajustes aprobados
        if (!empty($ajustesAprobados)) {
            $listaAprobados = implode(', ', $ajustesAprobados);
            Notification::send(
                $estudianteUser,
                new DashboardNotification(
                    'Ajustes razonables aprobados',
                    "Tus ajustes razonables han sido aprobados por Dirección de Carrera: {$listaAprobados}. Ya puedes utilizarlos en tus actividades académicas.",
                    route('estudiantes.dashboard'),
                    'Ver mi dashboard'
                )
            );
        }

        // Notificar sobre ajustes rechazados
        if (!empty($ajustesRechazados)) {
            $listaRechazados = implode(', ', $ajustesRechazados);
            $primerMotivo = !empty($motivosRechazo) ? reset($motivosRechazo) : 'No se especificó motivo';
            
            Notification::send(
                $estudianteUser,
                new DashboardNotification(
                    'Ajustes razonables rechazados',
                    "Los siguientes ajustes fueron rechazados: {$listaRechazados}. Motivo: {$primerMotivo}",
                    route('estudiantes.dashboard'),
                    'Ver detalles'
                )
            );
        }
    }

    protected function notifyOnRejection(Solicitud $solicitud, string $motivo): void
    {
        $estudianteUser = optional($solicitud->estudiante)->user;
        $asesor = $solicitud->asesor;

        Notification::send(
            collect([$estudianteUser, $asesor])->filter(),
            new DashboardNotification(
                'Solicitud rechazada',
                "Motivo: {$motivo}",
                route('solicitudes.show', $solicitud),
                'Ver detalle'
            )
        );
    }

    protected function notifyOnReturnToCTP(Solicitud $solicitud, string $motivo): void
    {
        $asesor = $solicitud->asesor;
        $asesorTecnico = \App\Models\User::withRole('Asesora Tecnica Pedagogica')->first();

        Notification::send(
            collect([$asesor, $asesorTecnico])->filter(),
            new DashboardNotification(
                'Solicitud devuelta para correcciones',
                "Motivo: {$motivo}",
                route('solicitudes.show', $solicitud),
                'Ver detalle'
            )
        );
    }

    protected function notifyTeachers(Solicitud $solicitud): void
    {
        // Obtener docentes relacionados con las asignaturas del estudiante
        $estudiante = $solicitud->estudiante;
        if (!$estudiante) {
            return;
        }

        // Cargar la carrera del estudiante
        $estudiante->load('carrera');
        
        if (!$estudiante->carrera) {
            return;
        }

        // Buscar docentes de la misma carrera del estudiante
        // Primero obtener docentes directamente de la carrera
        $docentesDeCarrera = \App\Models\Docente::where('carrera_id', $estudiante->carrera_id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        // También buscar por asignaturas de la carrera (por si acaso)
        $asignaturas = \App\Models\Asignatura::where('carrera_id', $estudiante->carrera_id)
            ->whereHas('docente')
            ->with('docente')
            ->get();

        $docentesDeAsignaturas = $asignaturas->map(function ($asignatura) {
            return $asignatura->docente?->user;
        })->filter();

        // Combinar y asegurar que solo sean docentes de la misma carrera
        $docentes = $docentesDeCarrera->merge($docentesDeAsignaturas)
            ->unique('id')
            ->filter(function ($user) use ($estudiante) {
                // Verificar que el docente pertenezca a la misma carrera
                $docente = $user->docente;
                return $docente && $docente->carrera_id === $estudiante->carrera_id;
            });

        if ($docentes->isEmpty()) {
            return;
        }

        // Solo notificar sobre ajustes aprobados
        $ajustesAprobados = $solicitud->ajustesRazonables()
            ->where('estado', 'Aprobado')
            ->get();
        
        if ($ajustesAprobados->isEmpty()) {
            return; // No hay ajustes aprobados para notificar
        }

        $listaAjustes = $ajustesAprobados->pluck('nombre')->implode(', ');
        $nombreEstudiante = trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? ''));

        Notification::send(
            $docentes,
            new DashboardNotification(
                'Ajustes razonables aprobados',
                "El estudiante {$nombreEstudiante} tiene ajustes razonables aprobados y activos: {$listaAjustes}. Por favor, considera estos ajustes en tus actividades académicas.",
                route('docente.estudiantes'),
                'Ver estudiantes'
            )
        );
    }
}
