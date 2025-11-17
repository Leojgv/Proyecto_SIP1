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

        $solicitudes = Solicitud::with(['estudiante.carrera', 'asesor'])
            ->where(function ($query) use ($directorId) {
                $query->where('director_id', $directorId)
                    ->orWhereHas('estudiante.carrera', fn ($sub) => $sub->where('director_id', $directorId));
            })
            ->latest('fecha_solicitud')
            ->paginate(12);

        return view('DirectorCarrera.casos.index', [
            'solicitudes' => $solicitudes,
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
        $solicitud->update([
            'estado' => 'Aprobado',
            'motivo_rechazo' => null,
        ]);

        $this->notifyOnApproval($solicitud);

        return back()->with('status', 'Solicitud aprobada y notificada.');
    }

    public function reject(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $validated = $request->validate([
            'motivo_rechazo' => ['required', 'string', 'min:5'],
        ]);

        $solicitud->update([
            'estado' => 'Rechazado',
            'motivo_rechazo' => $validated['motivo_rechazo'],
        ]);

        $this->notifyOnRejection($solicitud, $validated['motivo_rechazo']);

        return back()->with('status', 'Solicitud rechazada y notificada.');
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
}
