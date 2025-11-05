@extends('layouts.app')

@section('title', 'Dashboard Estudiante')

@push('styles')
<style>
    :root {
        --sie-primary: #c1121f;
        --sie-primary-dark: #8d0d17;
        --sie-surface: #ffffff;
        --sie-muted: #6b7280;
        --sie-muted-light: #94a3b8;
        --sie-border: #e2e8f0;
        --sie-soft: #f8fafc;
    }

    .content-wrapper {
        background: linear-gradient(180deg, #f9fafb 0%, #ffffff 100%);
    }

    .dashboard {
        font-family: 'Poppins', sans-serif;
        color: #0f172a;
    }

    .dashboard-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .dashboard-header .intro h1 {
        font-weight: 700;
        font-size: clamp(2rem, 2.8vw, 2.6rem);
        margin-bottom: 0.4rem;
    }

    .dashboard-header .intro p {
        color: var(--sie-muted);
        margin-bottom: 0;
        font-size: 1rem;
    }

    .dashboard-header .subtitle {
        text-transform: uppercase;
        font-weight: 600;
        font-size: 0.8rem;
        letter-spacing: 0.2rem;
        color: var(--sie-muted-light);
        margin-bottom: 0.4rem;
    }

    .profile-summary {
        background: var(--sie-surface);
        border-radius: 18px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.22);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        min-width: 280px;
    }

    .profile-avatar {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(193, 18, 31, 0.12), rgba(193, 18, 31, 0.3));
        color: var(--sie-primary);
        display: grid;
        place-items: center;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(193, 18, 31, 0.08);
        color: var(--sie-primary);
        border-radius: 999px;
        padding: 0.25rem 0.7rem;
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 0.4rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.25rem;
    }

    .stat-card {
        background: var(--sie-surface);
        border-radius: 18px;
        padding: 1.6rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        inset: auto -20% -40% -20%;
        background: linear-gradient(135deg, rgba(193, 18, 31, 0.1), transparent 60%);
        height: 70%;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: rgba(193, 18, 31, 0.12);
        color: var(--sie-primary);
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .stat-title {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--sie-muted);
        margin-bottom: 0.35rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .cta-card {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        background: linear-gradient(120deg, rgba(193, 18, 31, 0.92), rgba(141, 13, 23, 0.9));
        border-radius: 24px;
        padding: clamp(1.8rem, 3vw, 2.3rem);
        margin-bottom: 2.5rem;
        color: #ffffff;
        box-shadow: 0 25px 45px rgba(193, 18, 31, 0.25);
    }

    .cta-card h2 {
        font-weight: 600;
        margin-bottom: 0.4rem;
    }

    .cta-card p {
        margin: 0;
        opacity: 0.9;
    }

    .btn-cta {
        background: #ffffff;
        color: var(--sie-primary);
        border-radius: 14px;
        padding: 0.9rem 1.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18);
        color: var(--sie-primary);
    }

    .panel {
        background: var(--sie-surface);
        border-radius: 20px;
        padding: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.16);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.05);
    }

    .panel + .panel {
        margin-top: 1.75rem;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .panel-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .panel-header .meta {
        color: var(--sie-muted);
        font-size: 0.9rem;
    }

    .item-card {
        border-radius: 16px;
        border: 1px solid var(--sie-border);
        padding: 1.25rem;
        margin-bottom: 1rem;
        display: grid;
        gap: 0.6rem;
        grid-template-columns: 1fr auto;
        align-items: center;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .item-card:hover {
        border-color: rgba(193, 18, 31, 0.35);
        box-shadow: 0 12px 24px rgba(193, 18, 31, 0.08);
    }

    .item-info h4 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 600;
    }

    .item-info p {
        margin: 0;
        color: var(--sie-muted);
        font-size: 0.95rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.8rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
        background: rgba(193, 18, 31, 0.12);
        color: var(--sie-primary);
    }

    .progress-track {
        height: 6px;
        border-radius: 999px;
        background: var(--sie-soft);
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(135deg, var(--sie-primary), var(--sie-primary-dark));
    }

    .empty-state {
        padding: 1.25rem;
        border-radius: 16px;
        background: var(--sie-soft);
        border: 1px dashed var(--sie-border);
        color: var(--sie-muted);
        text-align: center;
    }

    @media (max-width: 992px) {
        .item-card {
            grid-template-columns: 1fr;
            text-align: left;
        }

        .item-actions {
            width: 100%;
        }

        .item-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard">
    <div class="dashboard-header">
        <div class="intro">
            <div class="subtitle">Sistema de Inclusión Educativa</div>
            <h1>Mi Dashboard</h1>
            <p>Gestiona tus solicitudes y revisa el avance de tus ajustes académicos.</p>
        </div>

        <div class="profile-summary">
            <div class="profile-avatar">
                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="fw-semibold">{{ $user->name }}</div>
                <div class="text-muted small">{{ $user->email }}</div>
                <div class="role-badge">
                    <i class="fas fa-user-graduate"></i>
                    {{ optional($user->rol)->nombre ?? 'Estudiante' }}
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-file-signature"></i></div>
            <div class="stat-title">Solicitudes activas</div>
            <p class="stat-value">{{ $stats['solicitudes_activas'] }}</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <div class="stat-title">Ajustes razonables activos</div>
            <p class="stat-value">{{ $stats['ajustes_activos'] }}</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-comments"></i></div>
            <div class="stat-title">Próximas entrevistas</div>
            <p class="stat-value">{{ $stats['entrevistas_programadas'] }}</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-folder-open"></i></div>
            <div class="stat-title">Evidencias enviadas</div>
            <p class="stat-value">{{ $stats['evidencias_enviadas'] }}</p>
        </div>
    </div>

    <div class="cta-card">
        <div>
            <h2>¿Necesitas solicitar una entrevista?</h2>
            <p>Agenda una nueva entrevista para coordinar el apoyo pedagógico o revisar tus ajustes razonables.</p>
        </div>
        <a href="{{ route('entrevistas.create') }}" class="btn-cta">
            <i class="fas fa-calendar-plus"></i>
            Solicitar Entrevista
        </a>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h3>Mis Solicitudes</h3>
            <div class="meta">Últimas actualizaciones de tus solicitudes registradas</div>
        </div>

        @if ($solicitudesRecientes->isEmpty())
            <div class="empty-state">
                No tienes solicitudes registradas todavía. Crea una nueva desde el módulo de solicitudes.
            </div>
        @else
            @foreach ($solicitudesRecientes as $solicitud)
                <div class="item-card">
                    <div class="item-info">
                        <h4>Solicitud de {{ $solicitud->descripcion ? \Illuminate\Support\Str::limit($solicitud->descripcion, 45) : 'apoyo pedagógico' }}</h4>
                        <p>Creada el {{ optional($solicitud->fecha_solicitud)->format('d/m/Y') }} • {{ $solicitud->ajustes_razonables_count }} ajustes • {{ $solicitud->entrevistas_count }} entrevistas</p>
                        <span class="status-badge">
                            <i class="fas fa-circle"></i>
                            {{ $solicitud->estado }}
                        </span>
                    </div>
                    <div class="item-actions">
                        <a href="{{ route('solicitudes.show', $solicitud) }}" class="btn btn-outline-danger rounded-pill px-4">
                            Ver detalles
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="panel">
        <div class="panel-header">
            <h3>Ajustes Razonables Activos</h3>
            <div class="meta">Seguimiento del progreso de tus ajustes actuales</div>
        </div>

        @if ($ajustesActivos->isEmpty())
            <div class="empty-state">
                Aún no tienes ajustes razonables activos. Revisa tus solicitudes para conocer su estado.
            </div>
        @else
            @foreach ($ajustesActivos as $ajuste)
                <div class="item-card">
                    <div class="item-info">
                        <h4>{{ $ajuste->nombre }}</h4>
                        <p>Vigente desde {{ optional($ajuste->fecha_inicio)->format('d/m/Y') ?? 'sin fecha' }}</p>
                        <div class="progress-track mt-2">
                            <div class="progress-bar" style="width: {{ $ajuste->porcentaje_avance }}%"></div>
                        </div>
                        <small class="text-muted">Avance del {{ $ajuste->porcentaje_avance }}%</small>
                    </div>
                    <div class="item-actions">
                        <a href="{{ route('ajustes-razonables.show', $ajuste) }}" class="btn btn-outline-danger rounded-pill px-4">Ver ajuste</a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="panel">
        <div class="panel-header">
            <h3>Próximas Entrevistas</h3>
            <div class="meta">Coordina tus encuentros con el equipo de apoyo pedagógico</div>
        </div>

        @if ($proximasEntrevistas->isEmpty())
            <div class="empty-state">
                No tienes entrevistas agendadas por ahora. Puedes solicitar una nueva cuando lo necesites.
            </div>
        @else
            @foreach ($proximasEntrevistas as $entrevista)
                <div class="item-card">
                    <div class="item-info">
                        <h4>Entrevista con {{ optional($entrevista->asesorPedagogico)->nombre ?? 'asesor pedagógico' }}</h4>
                        <p>Programada para el {{ optional($entrevista->fecha)->format('d/m/Y') }}</p>
                    </div>
                    <div class="item-actions">
                        <a href="{{ route('entrevistas.show', $entrevista) }}" class="btn btn-outline-danger rounded-pill px-4">Ver detalles</a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="panel">
        <div class="panel-header">
            <h3>Evidencias recientes</h3>
            <div class="meta">Últimos archivos o registros enviados para tus solicitudes</div>
        </div>

        @if ($evidenciasRecientes->isEmpty())
            <div class="empty-state">
                Todavía no has registrado evidencias. Puedes hacerlo desde la sección de solicitudes.
            </div>
        @else
            @foreach ($evidenciasRecientes as $evidencia)
                <div class="item-card">
                    <div class="item-info">
                        <h4>{{ $evidencia->tipo }}</h4>
                        <p>{{ \Illuminate\Support\Str::limit($evidencia->descripcion ?? 'Sin descripción', 60) }}</p>
                        <span class="status-badge">
                            <i class="fas fa-folder"></i>
                            Solicitud #{{ $evidencia->solicitud_id }}
                        </span>
                    </div>
                    <div class="item-actions">
                        <a href="{{ route('evidencias.show', $evidencia) }}" class="btn btn-outline-danger rounded-pill px-4">Revisar</a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
