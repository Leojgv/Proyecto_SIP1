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

        // El Director de Carrera solo ve casos que están en su fase:
        // - Pendiente de Aprobacion (casos que debe revisar y aprobar/rechazar)
        // También puede ver casos ya procesados (Aprobado/Rechazado) para historial
        $solicitudes = Solicitud::with(['estudiante.carrera', 'asesor'])
            ->where(function ($query) use ($directorId) {
                $query->where('director_id', $directorId)
                    ->orWhereHas('estudiante.carrera', fn ($sub) => $sub->where('director_id', $directorId));
            })
            ->whereIn('estado', [
                'Pendiente de Aprobacion',
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
        if ($solicitud->estado !== 'Pendiente de Aprobacion') {
            return back()->with('error', 'Solo se pueden aprobar solicitudes que estén en estado "Pendiente de Aprobacion".');
        }

        $solicitud->update([
            'estado' => 'Aprobado',
            'motivo_rechazo' => null,
        ]);

        // Actualizar el estado de todos los ajustes razonables asociados a "Aprobado"
        $solicitud->ajustesRazonables()->update([
            'estado' => 'Aprobado',
        ]);

        $this->notifyOnApproval($solicitud);
        
        // Notificar a docentes sobre los ajustes aprobados
        $this->notifyTeachers($solicitud);

        // También notificar a la Asesora Técnica
        $asesorTecnico = \App\Models\User::withRole('Asesora Tecnica Pedagogica')->first();
        if ($asesorTecnico) {
            Notification::send(
                $asesorTecnico,
                new DashboardNotification(
                    'Solicitud aprobada',
                    "La solicitud de {$solicitud->estudiante->nombre} {$solicitud->estudiante->apellido} ha sido aprobada.",
                    route('asesora-tecnica.casos.index'),
                    'Ver casos'
                )
            );
        }

        return redirect()
            ->route('director.dashboard')
            ->with('status', 'Solicitud aprobada exitosamente. Los ajustes han sido notificados a los docentes correspondientes.');
    }

    public function reject(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $validated = $request->validate([
            'motivo_rechazo' => ['required', 'string', 'min:5'],
        ]);

        // Verificar que el estado actual permita esta transición
        if ($solicitud->estado !== 'Pendiente de Aprobacion') {
            return back()->with('error', 'Solo se pueden rechazar solicitudes que estén en estado "Pendiente de Aprobacion".');
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
        if ($solicitud->estado !== 'Pendiente de Aprobacion') {
            return back()->with('error', 'Solo se pueden devolver solicitudes que estén en estado "Pendiente de Aprobacion".');
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

        // Buscar asignaturas de la carrera del estudiante
        $asignaturas = \App\Models\Asignatura::where('carrera_id', $estudiante->carrera_id)
            ->whereNotNull('docente_id')
            ->with('docente')
            ->get();

        $docentes = $asignaturas->map(function ($asignatura) {
            return $asignatura->docente;
        })->filter()->unique('id');

        if ($docentes->isEmpty()) {
            return;
        }

        $ajustes = $solicitud->ajustesRazonables->pluck('nombre')->implode(', ');

        Notification::send(
            $docentes,
            new DashboardNotification(
                'Ajustes razonables aprobados',
                "El estudiante {$estudiante->nombre} {$estudiante->apellido} tiene ajustes aprobados: {$ajustes}",
                route('docente.estudiantes'),
                'Ver estudiantes'
            )
        );
    }
}
