<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Panel Docente')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/accessibility.css') }}">
  <style>
    :root {
      --red-50: #fff1f1;
      --red-100: #ffe0e0;
      --red-200: #ffc2c2;
      --red-300: #fca5a5;
      --red-400: #f87171;
      --red-500: #ef4444;
      --red-600: #dc2626;
      --red-700: #b91c1c;
      --red-800: #991b1b;
      --red-900: #7f1d1d;
      font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }
    body {
      background: #f5f6fb;
      margin: 0;
      color: #1f1f2d;
      font-family: inherit;
    }
    .dashboard-wrapper {
      display: flex;
      min-height: 100vh;
    }
    .dashboard-sidebar {
      width: 250px;
      background: #fff;
      border-right: 1px solid #e4e5f0;
      padding: 2rem 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }
    .dashboard-sidebar__brand {
      font-weight: 600;
      font-size: 1.15rem;
      color: var(--red-700);
      display: flex;
      align-items: center;
      gap: .65rem;
    }
    .dashboard-sidebar__nav {
      display: flex;
      flex-direction: column;
      gap: .35rem;
    }
    .dashboard-sidebar__link {
      display: flex;
      align-items: center;
      gap: .75rem;
      padding: .65rem .85rem;
      border-radius: .85rem;
      color: #5c5d70;
      font-weight: 500;
      text-decoration: none;
      transition: background .2s ease, color .2s ease;
    }
    .dashboard-sidebar__link i {
      width: 1.25rem;
      text-align: center;
    }
    .dashboard-sidebar__link:hover {
      background: var(--red-50);
      color: var(--red-700);
    }
    .dashboard-sidebar__link.active {
      background: var(--red-600);
      color: #fff;
      box-shadow: 0 12px 24px rgba(220,38,38,.2);
    }
    .dashboard-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }
    .dashboard-topbar {
      background: #fff;
      border-bottom: 1px solid #e4e5f0;
      padding: 1rem 2rem;
    }
    .dashboard-topbar__items {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 1.5rem;
      color: #6a6b7c;
      font-size: .95rem;
    }
    .dashboard-topbar__items i {
      color: var(--red-600);
      margin-right: .35rem;
    }
    .notification-btn {
      border: none;
      background: none;
      color: #6a6b7c;
      font-size: 1.2rem;
      padding: 0.25rem 0.5rem;
      transition: color 0.2s ease;
    }
    .notification-btn:hover {
      color: var(--red-600);
    }
    .notification-badge {
      font-size: 0.65rem;
      padding: 0.25rem 0.5rem;
      font-weight: 600;
      min-width: 1.5rem;
      text-align: center;
    }
    .notification-item-modal:hover {
      background-color: #f9fafb !important;
    }
    .dashboard-content {
      padding: 2rem;
    }
    @media (max-width: 992px) {
      .dashboard-wrapper {
        flex-direction: column;
      }
      .dashboard-sidebar {
        width: 100%;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 1rem;
      }
      .dashboard-sidebar__nav {
        flex-direction: row;
        flex-wrap: wrap;
      }
      .dashboard-main {
        width: 100%;
      }
    }
  </style>
  @stack('styles')
</head>
<body>
<div class="dashboard-wrapper">
  <aside class="dashboard-sidebar">
    <div class="dashboard-sidebar__brand"><i class="fas fa-graduation-cap"></i>Sistema de Inclusion</div>
    <nav class="dashboard-sidebar__nav">
      <a class="dashboard-sidebar__link {{ request()->routeIs('docente.dashboard') ? 'active' : '' }}" href="{{ route('docente.dashboard') }}"><i class="fas fa-chart-line"></i>Dashboard</a>
      <a class="dashboard-sidebar__link {{ request()->routeIs('docente.estudiantes') ? 'active' : '' }}" href="{{ route('docente.estudiantes') }}"><i class="fas fa-user-graduate"></i>Mis Estudiantes</a>
    </nav>
  </aside>
  <div class="dashboard-main">
    <header class="dashboard-topbar">
      <div class="dashboard-topbar__items">
        <span>
          <i class="fas fa-user-circle"></i>
          {{ auth()->user()->nombre_completo ?? auth()->user()->name ?? '' }}
          @if(auth()->user()->docente && auth()->user()->docente->carrera)
            <span class="text-muted ms-2">- {{ auth()->user()->docente->carrera->nombre }}</span>
          @endif
        </span>
        @php
          $notificationsCount = \App\Models\Notificacion::where('notifiable_type', get_class(auth()->user()))
              ->where('notifiable_id', auth()->user()->id)
              ->whereNull('read_at')
              ->count();
          $recentNotifications = \App\Models\Notificacion::where('notifiable_type', get_class(auth()->user()))
              ->where('notifiable_id', auth()->user()->id)
              ->latest('created_at')
              ->take(10)
              ->get()
              ->map(function ($notification) {
                  $data = $notification->data ?? [];
                  return [
                      'id' => $notification->id,
                      'title' => $data['titulo'] ?? ($data['title'] ?? ($data['subject'] ?? 'Notificación')),
                      'message' => $data['mensaje'] ?? ($data['message'] ?? ($data['body'] ?? 'Nueva actualización disponible.')),
                      'url' => $data['url'] ?? null,
                      'button_text' => $data['texto_boton'] ?? ($data['textoBoton'] ?? null),
                      'time' => optional($notification->created_at)->diffForHumans() ?? 'hace instantes',
                      'read_at' => $notification->read_at,
                  ];
              })
              ->values()
              ->all();
        @endphp
        <button type="button" class="btn btn-link text-decoration-none position-relative p-0 notification-btn" data-bs-toggle="modal" data-bs-target="#notificationsModal">
          <i class="fas fa-bell"></i>
          @if($notificationsCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
              {{ $notificationsCount > 99 ? '99+' : $notificationsCount }}
            </span>
          @endif
        </button>
        @include('components.accessibility-button')
        <a class="text-decoration-none" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-docente').submit();"><i class="fas fa-right-from-bracket"></i>Salir</a>
      </div>
      <form id="logout-form-docente" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </header>
    <main class="dashboard-content">
      @yield('content')
    </main>
  </div>
</div>

<!-- Modal de Notificaciones -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content border-0 shadow-sm">
      <div class="modal-header border-bottom bg-white">
        <h5 class="modal-title fw-semibold" id="notificationsModalLabel">
          <i class="fas fa-bell" style="color: var(--red-600);"></i>
          <span class="ms-2">Notificaciones</span>
          @if($notificationsCount > 0)
            <span class="badge rounded-pill ms-2" style="background-color: var(--red-600); color: white; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
              {{ $notificationsCount > 99 ? '99+' : $notificationsCount }}
            </span>
          @endif
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
        @forelse($recentNotifications as $notification)
          <div class="notification-item-modal border-bottom p-3 {{ is_null($notification['read_at']) ? 'bg-light' : 'bg-white' }}" style="transition: background 0.2s ease;">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                  <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background-color: var(--red-50);">
                    <i class="fas fa-bell" style="color: var(--red-600); font-size: 0.875rem;"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0 fw-semibold" style="color: #1f1f2d; font-size: 0.95rem;">
                      {{ $notification['title'] }}
                    </h6>
                    <small class="text-muted">{{ $notification['time'] }}</small>
                  </div>
                </div>
                <p class="text-muted mb-2" style="font-size: 0.875rem; line-height: 1.5; margin-left: 44px;">
                  {{ $notification['message'] }}
                </p>
                @if(isset($notification['url']) && $notification['url'])
                  <div style="margin-left: 44px;">
                    <a href="{{ $notification['url'] }}" class="btn btn-sm" style="background-color: var(--red-600); color: white; border: none; padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 500;">
                      {{ $notification['button_text'] ?? 'Ver más' }}
                      <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                  </div>
                @endif
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-5">
            <div class="mb-3">
              <i class="fas fa-bell-slash" style="font-size: 3rem; color: #9ca3af;"></i>
            </div>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">No tienes notificaciones.</p>
          </div>
        @endforelse
      </div>
      <div class="modal-footer border-top bg-white">
        <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #6b7280; color: white; border: none; padding: 0.5rem 1rem; font-weight: 500;">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/accessibility.js') }}"></script>
@stack('scripts')
</body>
</html>
