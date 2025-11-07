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
          <a href="{{ route('estudiantes.dashboard') }}" class="nav-link">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Dashboard Estudiante</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('estudiantes.index') }}" class="nav-link">
            <i class="nav-icon fas fa-user-graduate"></i>
            <p>Estudiantes</p>
          </a>
        </li>

        <li class="nav-header">Académico</li>
        <li class="nav-item"><a href="{{ route('carreras.index') }}" class="nav-link"><i class="nav-icon fas fa-school"></i><p>Carreras</p></a></li>
        <li class="nav-item"><a href="{{ route('asignaturas.index') }}" class="nav-link"><i class="nav-icon fas fa-book"></i><p>Asignaturas</p></a></li>
        <li class="nav-item"><a href="{{ route('docentes.index') }}" class="nav-link"><i class="nav-icon fas fa-chalkboard-teacher"></i><p>Docentes</p></a></li>
        <li class="nav-item"><a href="{{ route('docente-asignaturas.index') }}" class="nav-link"><i class="nav-icon fas fa-people-arrows"></i><p>Asignaciones</p></a></li>
        <li class="nav-item"><a href="{{ route('directores-carrera.index') }}" class="nav-link"><i class="nav-icon fas fa-user-tie"></i><p>Directores</p></a></li>

        <li class="nav-header">Apoyos</li>
        <li class="nav-item"><a href="{{ route('asesores-pedagogicos.index') }}" class="nav-link"><i class="nav-icon fas fa-user-friends"></i><p>Asesores pedagógicos</p></a></li>
        <li class="nav-item"><a href="{{ route('solicitudes.index') }}" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p>Solicitudes</p></a></li>
        <li class="nav-item"><a href="{{ route('ajustes-razonables.index') }}" class="nav-link"><i class="nav-icon fas fa-sliders-h"></i><p>Ajustes razonables</p></a></li>
        <li class="nav-item"><a href="{{ route('entrevistas.index') }}" class="nav-link"><i class="nav-icon fas fa-comments"></i><p>Entrevistas</p></a></li>
        <li class="nav-item"><a href="{{ route('evidencias.index') }}" class="nav-link"><i class="nav-icon fas fa-folder-open"></i><p>Evidencias</p></a></li>

        <li class="nav-header">Administración</li>
        <li class="nav-item"><a href="{{ route('roles.index') }}" class="nav-link"><i class="nav-icon fas fa-id-badge"></i><p>Roles</p></a></li>
      </ul>
    </nav>
  </div>
</aside>
