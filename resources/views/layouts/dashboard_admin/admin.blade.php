<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Panel del administrador')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
  
  <style>
    /* Paleta por tonos (rojos) compartida con el dashboard estudiantil */
    :root {
      --tone-50:  #fef2f2;
      --tone-100: #fee2e2;
      --tone-200: #fecaca;
      --tone-300: #fca5a5;
      --tone-400: #f87171;
      --tone-500: #ef4444;
      --tone-600: #dc2626;
      --tone-700: #b91c1c;
      --tone-800: #991b1b;
      --tone-900: #7f1d1d;
      --tone-950: #450a0a;
      --panel-radius: 18px;
      --panel-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
      --panel-border: #ececf5;
    }

    html,
    body {
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .student-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: stretch;
    }

    .student-sidebar {
      width: 240px;
      background-color: #ffffff;
      border-right: 1px solid #e5e7eb;
      padding: 2rem 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .student-sidebar__brand {
      font-size: 1.125rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      color: var(--tone-700);
    }

    .student-sidebar__nav {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .student-sidebar__link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 0.85rem;
      border-radius: 0.75rem;
      color: #344054;
      text-decoration: none;
      font-weight: 500;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    .student-sidebar__link:hover {
      background-color: var(--tone-50);
      color: var(--tone-700);
    }

    .student-sidebar__link.active {
      background-color: var(--tone-600);
      color: #ffffff;
      box-shadow: 0 8px 20px rgba(220, 38, 38, 0.15);
    }

    .student-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .student-topbar {
      background-color: #ffffff;
      border-bottom: 1px solid #e5e7eb;
      padding: 1rem 1.5rem;
    }
    .student-topbar__info {
      color: #6b6c7f;
      font-size: .95rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .student-topbar__info span {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
    }
    .student-topbar__info i {
      color: var(--tone-600);
    }
    .student-topbar .topbar-icon {
      color: var(--tone-600);
      font-size: 1.25rem;
      line-height: 1;
      padding: .375rem .5rem;
      border-radius: .5rem;
    }
    .student-topbar .topbar-icon:hover {
      color: var(--tone-700);
      background-color: var(--tone-50);
    }

    .student-content {
      padding: 2rem;
    }

    /* Dashboard styling inspired by coordinadora panel */
    .dashboard-shell .small-box {
      border-radius: var(--panel-radius);
      border: 1px solid rgba(255, 255, 255, 0.25);
      padding: 1.5rem;
      box-shadow: var(--panel-shadow);
      position: relative;
      overflow: hidden;
      transition: transform 0.15s ease, box-shadow 0.2s ease;
    }
    .dashboard-shell .small-box.text-dark {
      border-color: rgba(15, 23, 42, 0.08);
    }
    .dashboard-shell .small-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 25px 55px rgba(15, 23, 42, 0.12);
    }
    .dashboard-shell .small-box .icon {
      top: 1.25rem;
      right: 1.25rem;
      font-size: 3rem;
      color: rgba(65, 65, 65, 0.6);
      opacity: 1;
    }
    .dashboard-shell .small-box.text-dark .icon {
      color: rgba(15, 23, 42, 0.25);
    }
    .dashboard-shell .small-box .inner h3 {
      font-size: 2.4rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }
    .dashboard-shell .small-box .inner p {
      font-weight: 500;
      letter-spacing: .02em;
    }
    .dashboard-shell .small-box-footer {
      margin-top: 0.85rem;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      border-radius: 999px;
      padding: 0.45rem 0.9rem;
      border: none;
      background: rgba(255, 255, 255, 0.2);
      color: inherit;
      font-weight: 600;
      transition: background 0.2s ease, color 0.2s ease;
    }
    .dashboard-shell .small-box.text-dark .small-box-footer {
      background: rgba(15, 23, 42, 0.08);
    }
    .dashboard-shell .small-box-footer i {
      transition: transform 0.2s ease;
    }
    .dashboard-shell .small-box-footer:hover {
      background: rgba(255, 255, 255, 0.35);
      color: inherit;
    }
    .dashboard-shell .small-box-footer:hover i {
      transform: translateX(2px);
    }

    .dashboard-shell .card,
    .dashboard-shell .card.border-0 {
      border-radius: var(--panel-radius);
      border: 1px solid var(--panel-border) !important;
      box-shadow: var(--panel-shadow);
    }
    .dashboard-shell .card > .card-header:first-child {
      border-radius: calc(var(--panel-radius) - 2px) calc(var(--panel-radius) - 2px) 0 0;
    }
    .dashboard-shell .card-body {
      padding: 1.5rem;
    }
    .dashboard-shell .list-group-item {
      border-radius: 14px !important;
      border: 1px solid var(--panel-border);
      margin-bottom: 0.85rem;
    }
    .dashboard-shell .list-group-item:last-child {
      margin-bottom: 0;
    }

    @media (max-width: 991.98px) {
      .student-wrapper {
        flex-direction: column;
      }

      .student-sidebar {
        width: 100%;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
      }

      .student-sidebar__nav {
        flex-direction: row;
        gap: 0.5rem;
      }

      .student-sidebar__link {
        padding: 0.5rem 0.75rem;
      }

      .student-main {
        width: 100%;
      }
    }

    .card-body .card-title { float: none !important; display: block; }
    .card-body .card-title + .text-muted,
    .card-body .card-title + .card-text { display: block; margin-top: .25rem; }

    .bg-primary { background-color: var(--tone-600) !important; }
    .bg-success { background-color: var(--tone-700) !important; }
    .bg-secondary { background-color: var(--tone-800) !important; }
    .bg-warning { background-color: var(--tone-300) !important; color: #111827 !important; }
    .bg-info { background-color: var(--tone-200) !important; color: #111827 !important; }

    .text-primary { color: var(--tone-700) !important; }

    .btn-primary,
    .btn-primary:disabled {
      background-color: var(--tone-600) !important;
      border-color: var(--tone-600) !important;
    }
    .btn-primary:hover,
    .btn-primary:focus {
      background-color: var(--tone-700) !important;
      border-color: var(--tone-700) !important;
    }
    .btn-outline-primary {
      color: var(--tone-700) !important;
      border-color: var(--tone-300) !important;
    }
    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
      color: var(--tone-800) !important;
      background-color: var(--tone-50) !important;
      border-color: var(--tone-400) !important;
    }
  </style>

  @stack('styles')
</head>
<body>
<div class="student-wrapper">
  @include('layouts.dashboard_admin.sidebar-admin')

  <main class="student-main">
    <header class="student-topbar">
      <div class="container-fluid d-flex justify-content-end align-items-center gap-3">
        <div class="student-topbar__info">
          <span><i class="fas fa-calendar-day"></i>{{ now()->translatedFormat('d \de F, Y') }}</span>
          <span><i class="fas fa-user-circle"></i>{{ auth()->user()->nombre_completo ?? auth()->user()->name ?? '' }}</span>
        </div>
        <a class="text-decoration-none topbar-icon" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Salir">
          <i class="fas fa-right-from-bracket"></i>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </div>
    </header>

    <section class="student-content">
      @yield('content')
    </section>
  </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
@stack('scripts')
</body>
</html>
