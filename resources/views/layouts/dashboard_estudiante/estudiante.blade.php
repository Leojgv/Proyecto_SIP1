<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Panel Estudiante')</title>
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
    .dashboard-content {
      padding: 2rem;
    }
    .stats-card {
      color: #fff;
      border-radius: 18px;
      padding: 1.25rem;
      position: relative;
      box-shadow: 0 12px 24px rgba(220,38,38,.15);
      min-height: 180px;
    }
    .stats-card__value {
      font-size: 2rem;
      font-weight: 700;
    }
    .stats-card__title {
      font-size: .95rem;
      margin-bottom: 0;
      text-transform: capitalize;
    }
    .stats-card__icon {
      position: absolute;
      top: 1rem;
      right: 1rem;
      opacity: .25;
      font-size: 2.5rem;
    }
    .stats-card__sub {
      font-size: .85rem;
      color: rgba(255,255,255,.85);
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
    <div class="dashboard-sidebar__brand"><i class="fas fa-grip"></i>Mi Espacio</div>
    <nav class="dashboard-sidebar__nav">
      <a class="dashboard-sidebar__link {{ request()->routeIs('estudiantes.dashboard') ? 'active' : '' }}" href="{{ route('estudiantes.dashboard') }}"><i class="fas fa-table-columns"></i>Dashboard</a>
      <a class="dashboard-sidebar__link {{ request()->routeIs('estudiantes.entrevistas.create') ? 'active' : '' }}" href="{{ route('estudiantes.entrevistas.create') }}"><i class="fas fa-paper-plane"></i>Solicitar Entrevista</a>
      <a class="dashboard-sidebar__link {{ request()->routeIs('estudiantes.dashboard') && request()->query('focus') === 'notificaciones' ? 'active' : '' }}" href="{{ route('estudiantes.dashboard', ['focus' => 'notificaciones']) }}#notificaciones"><i class="fas fa-bell"></i>Notificaciones</a>
      <a class="dashboard-sidebar__link {{ request()->routeIs('estudiantes.dashboard') && request()->query('focus') === 'configuracion' ? 'active' : '' }}" href="{{ route('estudiantes.dashboard', ['focus' => 'configuracion']) }}#configuracion"><i class="fas fa-gear"></i>Configuración</a>
    </nav>
  </aside>
  <div class="dashboard-main">
    <header class="dashboard-topbar">
      <div class="dashboard-topbar__items">
        <span><i class="fas fa-user-circle"></i>{{ auth()->user()->nombre_completo ?? auth()->user()->name ?? '' }}</span>
        @include('components.accessibility-button')
        <a class="text-decoration-none" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-estudiante').submit();"><i class="fas fa-right-from-bracket"></i>Salir</a>
      </div>
      <form id="logout-form-estudiante" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </header>
    <main class="dashboard-content">
      @yield('content')
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/accessibility.js') }}"></script>
@stack('scripts')
</body>
</html>
