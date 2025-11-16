<nav class="main-header navbar navbar-expand navbar-white navbar-light py-1">
  <ul class="navbar-nav ms-auto align-items-center gap-2">
    <li class="nav-item">
      <a class="nav-link py-1 d-flex align-items-center gap-1" href="{{ route('notificaciones.index') }}">
        <i class="fas fa-bell"></i> Notificaciones
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link py-1 d-flex align-items-center gap-1" href="#"><i class="fas fa-user"></i> Perfil</a>
    </li>
    <li class="nav-item">
      <a class="nav-link py-1 d-flex align-items-center gap-1" href="{{ route('logout') }}"
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Salir
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
      </form>
    </li>
  </ul>
</nav>

