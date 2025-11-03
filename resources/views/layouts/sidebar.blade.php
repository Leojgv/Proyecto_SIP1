<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Logo -->
  <a href="{{ url('/') }}" class="brand-link">
    <span class="brand-text fw-light">AdminLTE Laravel</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Menú -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
        <li class="nav-item">
          <a href="{{ route('home') }}" class="nav-link">
            <i class="nav-icon fas fa-home"></i>
            <p>Inicio</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#{{ route('alumnos.index') }}" class="nav-link">
            <i class="nav-icon fas fa-cog"></i>
            <p>Alumnos</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-cog"></i>
            <p>Configuración</p>
          </a>
        </li>
        
      </ul>
    </nav>
  </div>
</aside>
