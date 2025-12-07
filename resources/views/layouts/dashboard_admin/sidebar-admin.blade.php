<aside class="student-sidebar">
  <div class="student-sidebar__brand">
    <i class="fas fa-shield-halved"></i>
    <span>Panel Admin</span>
  </div>

  <nav class="student-sidebar__nav">
    <a href="{{ route('admin.dashboard') }}"
       class="student-sidebar__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i class="fas fa-table-columns"></i>
      <span>Dashboard</span>
    </a>

    <a href="{{ route('admin.users.index') }}"
       class="student-sidebar__link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
      <i class="fas fa-users-gear"></i>
      <span>Gestión de Usuarios</span>
    </a>

    <a href="{{ route('roles.index') }}"
       class="student-sidebar__link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
      <i class="fas fa-id-badge"></i>
      <span>Roles</span>
    </a>

    <a href="{{ route('estudiantes.index') }}"
       class="student-sidebar__link {{ request()->routeIs('estudiantes.*') ? 'active' : '' }}">
      <i class="fas fa-user-graduate"></i>
      <span>Estudiantes</span>
    </a>

    <a href="{{ route('solicitudes.index') }}"
       class="student-sidebar__link {{ request()->routeIs('solicitudes.*') ? 'active' : '' }}">
      <i class="fas fa-clipboard-list"></i>
      <span>Reportes</span>
    </a>

  </nav>
</aside>

