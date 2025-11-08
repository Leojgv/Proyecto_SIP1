@php($focus = request()->query('focus'))

<aside class="student-sidebar">
  <div class="student-sidebar__brand">
    <i class="fas fa-grip"></i>
    <span>Mi Espacio</span>
  </div>

  <nav class="student-sidebar__nav">
    <a
      href="{{ route('estudiantes.dashboard') }}"
      class="student-sidebar__link {{ request()->routeIs('estudiantes.dashboard') && ! $focus ? 'active' : '' }}"
    >
      <i class="fas fa-table-columns"></i>
      <span>Dashboard</span>
    </a>

    <a
      href="{{ route('estudiantes.entrevistas.create') }}"
      class="student-sidebar__link {{ request()->routeIs('estudiantes.entrevistas.create') ? 'active' : '' }}"
    >
      <i class="fas fa-paper-plane"></i>
      <span>Solicitar Entrevista</span>
    </a>

    <a
      href="{{ route('estudiantes.dashboard', ['focus' => 'notificaciones']) }}#notificaciones"
      class="student-sidebar__link {{ request()->routeIs('estudiantes.dashboard') && $focus === 'notificaciones' ? 'active' : '' }}"
    >
      <i class="fas fa-bell"></i>
      <span>Notificaciones</span>
    </a>

    <a
      href="{{ route('estudiantes.dashboard', ['focus' => 'configuracion']) }}#configuracion"
      class="student-sidebar__link {{ request()->routeIs('estudiantes.dashboard') && $focus === 'configuracion' ? 'active' : '' }}"
    >
      <i class="fas fa-gear"></i>
      <span>Configuración</span>
    </a>
  </nav>
</aside>
