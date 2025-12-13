@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Gestion de Estudiantes')

@section('content')
@php
  $estadoOptions = ['Pendiente', 'En proceso', 'Terminado'];
@endphp
<div class="students-page">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start mb-4">
    <div>
      <h1 class="h4 mb-1">Gestion de Estudiantes</h1>
      <p class="text-muted mb-0">Registra y gestiona informacion de estudiantes con necesidades especiales.</p>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="info-card">
        <div class="info-card__icon bg-danger-subtle text-danger"><i class="fas fa-users"></i></div>
        <div>
          <p class="text-muted mb-1">Total Estudiantes</p>
          <h3 class="mb-0">{{ $total }}</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="info-card">
        <div class="info-card__icon bg-success-subtle text-success"><i class="fas fa-user-check"></i></div>
        <div>
          <p class="text-muted mb-1">Estudiantes Activos</p>
          <h3 class="mb-0">{{ $activos }}</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="info-card">
        <div class="info-card__icon bg-warning-subtle text-warning"><i class="fas fa-clipboard-list"></i></div>
        <div>
          <p class="text-muted mb-1">Con casos activos</p>
          <h3 class="mb-0">{{ $conCasos }}</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="info-card">
        <div class="info-card__icon bg-info-subtle text-info"><i class="fas fa-calendar-plus"></i></div>
        <div>
          <p class="text-muted mb-1">Nuevos este mes</p>
          <h3 class="mb-0">{{ $nuevosMes }}</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
          <h5 class="card-title mb-0">Lista de Estudiantes</h5>
          <small class="text-muted">Estudiantes registrados con necesidades de apoyo educativo</small>
        </div>
        <div class="filters-group d-flex flex-wrap gap-2 align-items-center">
          <input type="text" class="form-control filters-group__input" placeholder="Buscar estudiantes..." id="searchInput" value="{{ $filters['search'] ?? '' }}">
          <select class="form-select filters-group__input" id="carreraFilter" name="carrera_id">
            <option value="">Todas las carreras</option>
            @foreach($carreras ?? [] as $carrera)
              <option value="{{ $carrera->id }}" {{ ($filters['carrera_id'] ?? '') == $carrera->id ? 'selected' : '' }}>
                {{ $carrera->nombre }}
              </option>
            @endforeach
          </select>
          <select class="form-select filters-group__input" id="ordenarPor" name="ordenar_por">
            <option value="nombre" {{ ($filters['ordenar_por'] ?? 'nombre') == 'nombre' ? 'selected' : '' }}>Ordenar por: Nombre (A-Z)</option>
            <option value="nombre_desc" {{ ($filters['ordenar_por'] ?? '') == 'nombre_desc' ? 'selected' : '' }}>Ordenar por: Nombre (Z-A)</option>
            <option value="carrera" {{ ($filters['ordenar_por'] ?? '') == 'carrera' ? 'selected' : '' }}>Ordenar por: Carrera (A-Z)</option>
            <option value="carrera_desc" {{ ($filters['ordenar_por'] ?? '') == 'carrera_desc' ? 'selected' : '' }}>Ordenar por: Carrera (Z-A)</option>
            <option value="casos" {{ ($filters['ordenar_por'] ?? '') == 'casos' ? 'selected' : '' }}>Ordenar por: Casos (Menor a Mayor)</option>
            <option value="casos_desc" {{ ($filters['ordenar_por'] ?? '') == 'casos_desc' ? 'selected' : '' }}>Ordenar por: Casos (Mayor a Menor)</option>
            <option value="fecha_desc" {{ ($filters['ordenar_por'] ?? '') == 'fecha_desc' ? 'selected' : '' }}>Ordenar por: Fecha (Más recientes)</option>
            <option value="fecha_asc" {{ ($filters['ordenar_por'] ?? '') == 'fecha_asc' ? 'selected' : '' }}>Ordenar por: Fecha (Más antiguos)</option>
          </select>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Estudiante</th>
              <th>Carrera</th>
              <th>Estado</th>
              <th>Casos</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($estudiantes as $est)
              <tr>
                <td>
                  <div class="fw-semibold">{{ $est['nombre'] }} {{ $est['apellido'] }}</div>
                  <div class="text-muted small">{{ $est['rut'] ?? 'Sin rut' }}</div>
                  <div class="text-muted small">{{ $est['email'] ?? 'Sin email' }}</div>
                </td>
                <td>
                  <div class="fw-semibold">{{ $est['carrera'] ?? 'Sin carrera' }}</div>
                  <small class="text-muted">{{ $est['semestre'] }}</small>
                </td>
                <td><span class="badge bg-success">{{ $est['estado'] }}</span></td>
                <td><span class="badge bg-warning text-dark">{{ $est['casos'] }}</span></td>
                <td>
                  @if($est['casos'] > 0)
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-primary"
                      data-bs-toggle="modal"
                      data-bs-target="#modalVerDetalles"
                      data-estudiante-nombre="{{ $est['nombre'] }} {{ $est['apellido'] }}"
                      data-estudiante-rut="{{ $est['rut'] ?? 'Sin RUT' }}"
                      data-estudiante-email="{{ $est['email'] ?? 'Sin email' }}"
                      data-estudiante-carrera="{{ $est['carrera'] ?? 'Sin carrera' }}"
                      data-solicitudes="{{ json_encode($est['solicitudes']) }}"
                    >
                      Ver detalles
                    </button>
                  @else
                    <span class="text-muted small">Sin casos</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Aun no hay estudiantes registrados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver Detalles de Solicitudes -->
<div class="modal fade" id="modalVerDetalles" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title fw-semibold">Detalle de Solicitudes</h5>
          <small class="text-muted">Información de las solicitudes del estudiante</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Información del Estudiante -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-user-graduate me-1"></i><strong>Estudiante</strong>
              </small>
              <div class="fw-semibold" id="modal-detalle-estudiante-nombre">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-id-card me-1"></i><strong>RUT</strong>
              </small>
              <div class="fw-semibold" id="modal-detalle-estudiante-rut">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-envelope me-1"></i><strong>Correo</strong>
              </small>
              <div class="fw-semibold" id="modal-detalle-estudiante-email">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-school me-1"></i><strong>Carrera</strong>
              </small>
              <div class="fw-semibold" id="modal-detalle-estudiante-carrera">-</div>
            </div>
          </div>
        </div>

        <!-- Lista de Solicitudes -->
        <div id="modal-detalle-solicitudes-lista">
          <p class="text-muted text-center">Cargando información...</p>
        </div>
      </div>
      <div class="modal-footer border-top bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para crear casos -->
<div class="modal fade" id="modalNuevoCaso" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" action="{{ route('solicitudes.store') }}">
      @csrf
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0">Registrar nuevo caso</h5>
          <small class="text-muted">Completa los datos para crear la solicitud sin salir del panel.</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="modalFechaSolicitud" class="form-label">Fecha</label>
            <input
              type="date"
              id="modalFechaSolicitud"
              name="fecha_solicitud"
              class="form-control @error('fecha_solicitud') is-invalid @enderror"
              value="{{ old('fecha_solicitud', now()->format('Y-m-d')) }}"
              required
            >
            @error('fecha_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="modalEstado" class="form-label">Estado</label>
            <select
              id="modalEstado"
              name="estado"
              class="form-select @error('estado') is-invalid @enderror"
              required
            >
              <option value="">Selecciona un estado</option>
              @foreach ($estadoOptions as $estadoOption)
                <option value="{{ $estadoOption }}" @selected(old('estado', 'Pendiente') === $estadoOption)>{{ $estadoOption }}</option>
              @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="modalEstudianteSelect" class="form-label">Estudiante</label>
            <select
              id="modalEstudianteSelect"
              name="estudiante_id"
              class="form-select @error('estudiante_id') is-invalid @enderror"
              required
            >
              <option value="">Selecciona un estudiante</option>
              @foreach ($estudiantesOptions as $estudianteOption)
                <option value="{{ $estudianteOption->id }}" @selected(old('estudiante_id') == $estudianteOption->id)>
                  {{ $estudianteOption->nombre }} {{ $estudianteOption->apellido }}
                </option>
              @endforeach
            </select>
            @error('estudiante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="modalAsesorSelect" class="form-label">Asesora pedagogica</label>
            <select
              id="modalAsesorSelect"
              name="asesor_id"
              class="form-select @error('asesor_id') is-invalid @enderror"
              required
            >
              <option value="">Selecciona una asesora</option>
              @foreach ($asesores as $asesor)
                <option value="{{ $asesor->id }}" @selected(old('asesor_id') == $asesor->id)>
                  {{ $asesor->nombre }} {{ $asesor->apellido }}
                </option>
              @endforeach
            </select>
            @error('asesor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="modalDirectorSelect" class="form-label">Director de carrera</label>
            <select
              id="modalDirectorSelect"
              name="director_id"
              class="form-select @error('director_id') is-invalid @enderror"
              required
            >
              <option value="">Selecciona un director</option>
              @foreach ($directores as $director)
                <option value="{{ $director->id }}" @selected(old('director_id') == $director->id)>
                  {{ $director->nombre }} {{ $director->apellido }}
                </option>
              @endforeach
            </select>
            @error('director_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="modalDescripcion" class="form-label">Descripcion</label>
            <textarea
              id="modalDescripcion"
              name="descripcion"
              rows="4"
              class="form-control @error('descripcion') is-invalid @enderror"
              placeholder="Describe brevemente el caso"
            >{{ old('descripcion') }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Guardar caso</button>
      </div>
    </form>
  </div>
</div>

<style>
  .info-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f5;
    box-shadow: 0 12px 25px rgba(15, 30, 60, .04);
  }
  .info-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }
  .table thead th {
    color: #6b6c7f;
    font-weight: 600;
    border-bottom: 1px solid #ececf4;
  }
  .filters-group .filters-group__input {
    width: 220px;
    flex: 0 0 auto;
  }
  #ordenarPor {
    min-width: 220px;
  }
  @media (max-width: 768px) {
    .filters-group .filters-group__input {
      width: 100%;
      flex: 1 1 100%;
    }
    #ordenarPor {
      min-width: 100%;
    }
  }
</style>

<script>
  // Manejar filtros y búsqueda
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const carreraFilter = document.getElementById('carreraFilter');
    const ordenarPor = document.getElementById('ordenarPor');
    
    function aplicarFiltros() {
      const params = new URLSearchParams(window.location.search);
      
      if (searchInput.value.trim()) {
        params.set('search', searchInput.value.trim());
      } else {
        params.delete('search');
      }
      
      if (carreraFilter.value) {
        params.set('carrera_id', carreraFilter.value);
      } else {
        params.delete('carrera_id');
      }
      
      if (ordenarPor.value && ordenarPor.value !== 'nombre') {
        params.set('ordenar_por', ordenarPor.value);
      } else {
        params.delete('ordenar_por');
      }
      
      window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    }
    
    // Agregar event listeners
    if (searchInput) {
      let searchTimeout;
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(aplicarFiltros, 500); // Debounce de 500ms
      });
    }
    
    if (carreraFilter) {
      carreraFilter.addEventListener('change', aplicarFiltros);
    }
    
    if (ordenarPor) {
      ordenarPor.addEventListener('change', aplicarFiltros);
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('modalNuevoCaso');
    if (!modalEl) {
      return;
    }
    modalEl.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      if (!button) {
        return;
      }
      var estudianteId = button.getAttribute('data-estudiante-id');
      var estudianteSelect = modalEl.querySelector('#modalEstudianteSelect');
      if (estudianteId && estudianteSelect) {
        estudianteSelect.value = estudianteId;
      }
    });

    @if ($errors->any())
      var autoModal = new bootstrap.Modal(modalEl);
      autoModal.show();
    @endif
  });

  // Modal Ver Detalles
  document.addEventListener('DOMContentLoaded', function () {
    const modalDetalles = document.getElementById('modalVerDetalles');
    if (!modalDetalles) return;

    modalDetalles.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;

      const estudianteNombre = button.getAttribute('data-estudiante-nombre') || '-';
      const estudianteRut = button.getAttribute('data-estudiante-rut') || '-';
      const estudianteEmail = button.getAttribute('data-estudiante-email') || '-';
      const estudianteCarrera = button.getAttribute('data-estudiante-carrera') || '-';
      const solicitudesJson = button.getAttribute('data-solicitudes') || '[]';

      // Actualizar información del estudiante
      document.getElementById('modal-detalle-estudiante-nombre').textContent = estudianteNombre;
      document.getElementById('modal-detalle-estudiante-rut').textContent = estudianteRut;
      document.getElementById('modal-detalle-estudiante-email').textContent = estudianteEmail;
      document.getElementById('modal-detalle-estudiante-carrera').textContent = estudianteCarrera;

      // Procesar y mostrar solicitudes
      const solicitudesContainer = document.getElementById('modal-detalle-solicitudes-lista');
      try {
        const solicitudes = JSON.parse(solicitudesJson);
        
        if (!solicitudes || solicitudes.length === 0) {
          solicitudesContainer.innerHTML = '<p class="text-muted text-center">No hay solicitudes registradas para este estudiante.</p>';
          return;
        }

        let html = '<h6 class="mb-3 fw-semibold d-flex align-items-center"><i class="fas fa-file-alt me-2 text-danger"></i>Solicitudes</h6>';
        
        solicitudes.forEach((solicitud, index) => {
          const estadoClass = solicitud.estado.toLowerCase().includes('aprobado') ? 'bg-success' : 
                              solicitud.estado.toLowerCase().includes('rechazado') ? 'bg-danger' : 
                              'bg-secondary';
          
          html += `
            <div class="border rounded p-3 bg-light mb-3">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h6 class="mb-1 fw-semibold">Solicitud #${index + 1}</h6>
                  <small class="text-muted">Fecha: ${solicitud.fecha_solicitud}</small>
                </div>
                <span class="badge ${estadoClass}">${solicitud.estado}</span>
              </div>
              
              ${solicitud.titulo ? `
              <div class="mb-3">
                <small class="text-muted d-block mb-1">
                  <strong>Título</strong>
                </small>
                <div class="fw-semibold">${solicitud.titulo}</div>
              </div>
              ` : ''}
              
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-user-tie me-1"></i><strong>Coordinadora</strong>
                  </small>
                  <div class="fw-semibold">${solicitud.coordinadora}</div>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-user-shield me-1"></i><strong>Director de carrera</strong>
                  </small>
                  <div class="fw-semibold">${solicitud.director}</div>
                </div>
              </div>

              ${solicitud.motivo_rechazo ? `
              <div class="mb-3">
                <div class="border rounded p-3 bg-light border-danger">
                  <small class="text-muted d-block mb-2">
                    <i class="fas fa-exclamation-triangle me-1 text-danger"></i><strong>Motivo de rechazo</strong>
                  </small>
                  <div class="text-danger" style="line-height: 1.6;">${solicitud.motivo_rechazo}</div>
                </div>
              </div>
              ` : ''}

              <div class="mb-3">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-2">
                    <strong>Descripción</strong>
                  </small>
                  <div class="text-muted" style="line-height: 1.6;">${solicitud.descripcion}</div>
                </div>
              </div>

              ${solicitud.entrevistas && solicitud.entrevistas.length > 0 ? `
              <div class="mb-0">
                <h6 class="mb-3 fw-semibold d-flex align-items-center">
                  <i class="fas fa-comments me-2 text-danger"></i>Entrevistas
                </h6>
                ${solicitud.entrevistas.map(entrevista => {
                  const modalidad = entrevista.modalidad || '';
                  const isPresencial = modalidad.toLowerCase() === 'presencial';
                  const isVirtual = modalidad.toLowerCase() === 'virtual';
                  
                  return `
                  <div class="border rounded p-3 bg-light mb-2">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-calendar-day me-1"></i><strong>Fecha</strong>
                        </small>
                        <div class="fw-semibold">${entrevista.fecha}</div>
                      </div>
                      ${entrevista.hora_inicio && entrevista.hora_fin ? `
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-clock me-1"></i><strong>Horario</strong>
                        </small>
                        <div class="fw-semibold">${entrevista.hora_inicio} - ${entrevista.hora_fin}</div>
                      </div>
                      ` : ''}
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-laptop me-1"></i><strong>Modalidad</strong>
                        </small>
                        <div class="fw-semibold">
                          <span class="badge ${isVirtual ? 'bg-info' : 'bg-success'}">${modalidad || 'N/A'}</span>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-user me-1"></i><strong>Coordinadora</strong>
                        </small>
                        <div class="fw-semibold">${entrevista.asesor}</div>
                      </div>
                      ${isPresencial ? `
                      <div class="col-md-12">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-map-marker-alt me-1"></i><strong>Lugar</strong>
                        </small>
                        <div class="fw-semibold">Sala 4to Piso, Edificio A</div>
                      </div>
                      ` : ''}
                      ${isVirtual ? `
                      <div class="col-md-12">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-link me-1"></i><strong>Link</strong>
                        </small>
                        <div class="fw-semibold">Por compartir</div>
                      </div>
                      ` : ''}
                    </div>
                  </div>
                `;
                }).join('')}
              </div>
              ` : ''}
            </div>
          `;
        });

        solicitudesContainer.innerHTML = html;
      } catch (e) {
        console.error('Error al procesar solicitudes:', e);
        solicitudesContainer.innerHTML = '<p class="text-danger text-center">Error al cargar las solicitudes.</p>';
      }
    });
  });
</script>
@endsection
