@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Entrevistas')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Entrevistas</h1>
      <p class="text-muted mb-0">Gestiona tus entrevistas y revisa el historial de estudiantes atendidos.</p>
    </div>
    <a href="{{ route('entrevistas.index') }}" class="btn btn-danger">Ver agenda completa</a>
  </div>

  @php
    $cards = [
      ['title' => 'Total entrevistas', 'value' => $stats['total'] ?? 0, 'sub' => 'Historico registrado', 'icon' => 'fa-calendar-check', 'bg' => '#d62828'],
      ['title' => 'Proximas', 'value' => $stats['proximas'] ?? 0, 'sub' => 'Desde hoy en adelante', 'icon' => 'fa-clock', 'bg' => '#b51b1b'],
      ['title' => 'Hoy', 'value' => $stats['hoy'] ?? 0, 'sub' => 'Citas para hoy', 'icon' => 'fa-sun', 'bg' => '#951010'],
      ['title' => 'Estudiantes con historial', 'value' => $historialEstudiantes->count(), 'sub' => 'Total atendidos', 'icon' => 'fa-users', 'bg' => '#f4a5a5'],
    ];
  @endphp

  <div class="row g-4">
    <div class="col-xl-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Listado de entrevistas</h5>
              <small class="text-muted">Ordenadas de la mas reciente a la mas antigua</small>
            </div>
          </div>
          <div class="table-responsive rounded-4 table-wrapper">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Estudiante</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Solicitud</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse($entrevistas as $entrevista)
                  <tr>
                    <td>
                      {{ optional($entrevista->solicitud)->estudiante->nombre ?? 'Sin nombre' }} {{ optional($entrevista->solicitud)->estudiante->apellido ?? '' }}
                      <div class="text-muted small">{{ optional(optional($entrevista->solicitud)->estudiante)->carrera->nombre ?? 'Sin carrera' }}</div>
                    </td>
                    <td>{{ $entrevista->fecha?->format('d/m/Y') ?? 's/f' }}</td>
                    <td>{{ $entrevista->fecha_hora_inicio?->format('H:i') ?? '--' }}</td>
                    <td class="text-muted small">{{ \Illuminate\Support\Str::limit(optional($entrevista->solicitud)->descripcion ?? 'Sin descripción', 40) }}</td>
                    <td class="text-end">
                      <a href="{{ route('entrevistas.show', $entrevista) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">Aun no registras entrevistas.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-end">
            {{ $entrevistas->links() }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Historial de estudiantes</h5>
              <small class="text-muted">Cuantas veces has atendido a cada estudiante</small>
            </div>
          </div>
          @forelse($historialEstudiantes as $registro)
            <div class="timeline-item">
              <div>
                <strong>{{ $registro['estudiante']->nombre ?? 'Sin nombre' }} {{ $registro['estudiante']->apellido ?? '' }}</strong>
                <p class="text-muted mb-1 small">{{ $registro['estudiante']->carrera->nombre ?? 'Sin carrera' }}</p>
                <p class="text-muted mb-0 small">Ultima: {{ $registro['ultima']?->fecha?->format('d/m/Y') ?? $registro['ultima']?->fecha_hora_inicio?->format('d/m/Y H:i') ?? 's/f' }}</p>
              </div>
              <span class="history-pill">{{ $registro['total'] }} entrevista(s)</span>
            </div>
          @empty
            <p class="text-muted mb-0">Aun no hay historial de estudiantes.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .dashboard-page {
    background: #f7f6fb;
    padding: 1rem;
    border-radius: 1.5rem;
  }
  .stats-card {
    color: #fff;
    border-radius: 18px;
    padding: 1.35rem 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(214, 40, 40, .22);
    background: linear-gradient(135deg, rgba(214,40,40,.92), rgba(245,108,108,.9));
  }
  .stats-card[data-bg="#b51b1b"] { background: linear-gradient(135deg, #b51b1b, #ef4f4f); }
  .stats-card[data-bg="#951010"] { background: linear-gradient(135deg, #951010, #d94444); }
  .stats-card[data-bg="#f4a5a5"] { background: linear-gradient(135deg, #f4a5a5, #f87171); }
  .stats-card__value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: .2rem;
  }
  .stats-card__title {
    font-size: .95rem;
    margin-bottom: .15rem;
    text-transform: capitalize;
  }
  .stats-card__icon {
    position: absolute;
    top: 1.15rem;
    right: 1.1rem;
    opacity: .15;
    font-size: 2.75rem;
  }
  .stats-card__sub {
    font-size: .85rem;
    color: rgba(255,255,255,.9);
  }
  .table-wrapper {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,.05);
  }
  table thead th {
    font-weight: 700;
    color: #444;
    border-bottom-width: 0;
  }
  table tbody tr:not(:last-child) td {
    border-bottom: 1px solid #f1f1f5;
  }
  .timeline-item {
    border: 1px solid #f0f0f5;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    background: #fff;
    box-shadow: 0 8px 20px rgba(0,0,0,.05);
  }
  .history-pill {
    background: #fee2e2;
    color: #b91c1c;
    padding: .3rem .7rem;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 600;
    white-space: nowrap;
  }
</style>
@endpush
@endsection
