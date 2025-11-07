<aside class="student-sidebar">
  <div class="student-sidebar__brand">
    <i class="fas fa-grip"></i>
    <span>Mi Espacio</span>
  </div>

  <nav class="student-sidebar__nav">
    <a
      href="{{ route('estudiantes.dashboard') }}"
      class="student-sidebar__link {{ request()->routeIs('estudiantes.dashboard') ? 'active' : '' }}"
    >
      <i class="fas fa-table-columns"></i>
      <span>Dashboard</span>
    </a>

    <a
      href="{{ route('entrevistas.create') }}"
      class="student-sidebar__link {{ request()->routeIs('entrevistas.create') ? 'active' : '' }}"
    >
      <i class="fas fa-paper-plane"></i>
      <span>Solicitar Entrevista</span>
    </a>

    <a
      href="{{ route('estudiantes.dashboard') }}#notificaciones"
      class="student-sidebar__link"
    >
      <i class="fas fa-bell"></i>
      <span>Notificaciones</span>
    </a>

    <a
      href="{{ route('estudiantes.dashboard') }}#configuracion"
      class="student-sidebar__link"
    >
      <i class="fas fa-gear"></i>
      <span>Configuración</span>
    </a>
  </nav>
</aside>
