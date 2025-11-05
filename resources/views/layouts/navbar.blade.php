<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Botón Sidebar -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" role="button" href="#"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Links a la derecha -->
  <ul class="navbar-nav ms-auto">
    <li class="nav-item">
      <a class="nav-link" href="#"><i class="fas fa-user"></i> Perfil</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('logout') }}"
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Salir
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </li>
  </ul>
</nav>
