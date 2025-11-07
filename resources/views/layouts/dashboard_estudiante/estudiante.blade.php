<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Panel del estudiante')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">

  <style>
    body {
      background-color: #f4f6f9;
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
      color: #0d6efd;
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
      background-color: #eff4ff;
      color: #0d6efd;
    }

    .student-sidebar__link.active {
      background-color: #0d6efd;
      color: #ffffff;
      box-shadow: 0 8px 20px rgba(13, 110, 253, 0.15);
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

    .student-content {
      padding: 2rem;
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
  </style>
</head>
<body>
<div class="student-wrapper">
  @include('layouts.dashboard_estudiante.sidebar-estudiante')

  <main class="student-main">
    <header class="student-topbar">
      <div class="container-fluid d-flex justify-content-end align-items-center gap-3">
        <a href="#" class="text-decoration-none text-muted"><i class="fas fa-user"></i> Perfil</a>
        <a class="text-decoration-none text-muted" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fas fa-sign-out-alt me-1"></i>Salir
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </div>
    </header>

    <section class="student-content">
      @yield('content')
    </section>

    @include('layouts.footer')
  </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
</body>
</html>
