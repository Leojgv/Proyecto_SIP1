<!-- Botón de Accesibilidad -->
<div class="dropdown" id="accessibility-dropdown">
  <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center gap-1" type="button" id="accessibilityBtn" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="fas fa-universal-access"></i>
    <span class="d-none d-md-inline">Accesibilidad</span>
  </button>
  <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="accessibilityBtn">
    <li>
      <h6 class="dropdown-header">
        <i class="fas fa-palette me-2"></i>Modo Oscuro
      </h6>
    </li>
    <li>
      <div class="dropdown-item-text px-3 py-2">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="darkModeToggle">
          <label class="form-check-label" for="darkModeToggle">
            Activar modo oscuro
          </label>
        </div>
      </div>
    </li>
    <li><hr class="dropdown-divider"></li>
    <li>
      <h6 class="dropdown-header">
        <i class="fas fa-text-height me-2"></i>Tamaño de Fuente
      </h6>
    </li>
    <li>
      <div class="dropdown-item-text px-3 py-2">
        <div class="d-flex align-items-center gap-2 mb-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" id="decreaseFont" title="Disminuir tamaño">
            <i class="fas fa-minus"></i>
          </button>
          <span class="flex-grow-1 text-center small" id="fontSizeDisplay">Normal</span>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="increaseFont" title="Aumentar tamaño">
            <i class="fas fa-plus"></i>
          </button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="resetFont" title="Restablecer tamaño">
          <i class="fas fa-undo me-1"></i>Restablecer
        </button>
      </div>
    </li>
  </ul>
</div>
