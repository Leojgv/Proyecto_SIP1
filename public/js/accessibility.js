/**
 * Sistema de Accesibilidad Global
 * Maneja modo oscuro y tamaño de fuente
 */

(function() {
  'use strict';

  // Configuración
  const STORAGE_KEYS = {
    darkMode: 'sip_dark_mode',
    fontSize: 'sip_font_size'
  };

  const FONT_SIZES = {
    small: { value: '0.875rem', label: 'Pequeña', multiplier: 0.875 },
    normal: { value: '1rem', label: 'Normal', multiplier: 1 },
    large: { value: '1.125rem', label: 'Grande', multiplier: 1.125 },
    xlarge: { value: '1.25rem', label: 'Muy Grande', multiplier: 1.25 },
    xxlarge: { value: '1.375rem', label: 'Extra Grande', multiplier: 1.375 }
  };

  // Estado actual
  let currentFontSize = localStorage.getItem(STORAGE_KEYS.fontSize) || 'normal';
  let darkModeEnabled = localStorage.getItem(STORAGE_KEYS.darkMode) === 'true';

  /**
   * Inicializar modo oscuro
   */
  function initDarkMode() {
    const toggle = document.getElementById('darkModeToggle');
    if (toggle) {
      toggle.checked = darkModeEnabled;
      toggle.addEventListener('change', function() {
        setDarkMode(this.checked);
      });
    }
    applyDarkMode();
  }

  /**
   * Aplicar modo oscuro
   */
  function setDarkMode(enabled) {
    darkModeEnabled = enabled;
    localStorage.setItem(STORAGE_KEYS.darkMode, enabled.toString());
    applyDarkMode();
  }

  /**
   * Aplicar estilos de modo oscuro
   */
  function applyDarkMode() {
    const html = document.documentElement;
    const body = document.body;
    
    if (darkModeEnabled) {
      html.classList.add('dark-mode');
      body.classList.add('dark-mode');
    } else {
      html.classList.remove('dark-mode');
      body.classList.remove('dark-mode');
    }
  }

  /**
   * Inicializar controles de fuente
   */
  function initFontSize() {
    const increaseBtn = document.getElementById('increaseFont');
    const decreaseBtn = document.getElementById('decreaseFont');
    const resetBtn = document.getElementById('resetFont');
    const fontSizeDisplay = document.getElementById('fontSizeDisplay');

    if (increaseBtn) {
      increaseBtn.addEventListener('click', increaseFontSize);
    }
    if (decreaseBtn) {
      decreaseBtn.addEventListener('click', decreaseFontSize);
    }
    if (resetBtn) {
      resetBtn.addEventListener('click', resetFontSize);
    }

    updateFontSizeDisplay();
    applyFontSize();
  }

  /**
   * Aumentar tamaño de fuente
   */
  function increaseFontSize() {
    const sizes = Object.keys(FONT_SIZES);
    const currentIndex = sizes.indexOf(currentFontSize);
    if (currentIndex < sizes.length - 1) {
      currentFontSize = sizes[currentIndex + 1];
      saveFontSize();
      applyFontSize();
      updateFontSizeDisplay();
    }
  }

  /**
   * Disminuir tamaño de fuente
   */
  function decreaseFontSize() {
    const sizes = Object.keys(FONT_SIZES);
    const currentIndex = sizes.indexOf(currentFontSize);
    if (currentIndex > 0) {
      currentFontSize = sizes[currentIndex - 1];
      saveFontSize();
      applyFontSize();
      updateFontSizeDisplay();
    }
  }

  /**
   * Restablecer tamaño de fuente
   */
  function resetFontSize() {
    currentFontSize = 'normal';
    saveFontSize();
    applyFontSize();
    updateFontSizeDisplay();
  }

  /**
   * Guardar tamaño de fuente
   */
  function saveFontSize() {
    localStorage.setItem(STORAGE_KEYS.fontSize, currentFontSize);
  }

  /**
   * Aplicar tamaño de fuente
   */
  function applyFontSize() {
    const fontSize = FONT_SIZES[currentFontSize];
    const html = document.documentElement;
    
    // Establecer variable CSS personalizada
    html.style.setProperty('--base-font-size', fontSize.value);
    html.style.fontSize = fontSize.value;
    
    // Aplicar a elementos específicos si es necesario
    document.body.style.fontSize = fontSize.value;
  }

  /**
   * Actualizar display del tamaño de fuente
   */
  function updateFontSizeDisplay() {
    const fontSizeDisplay = document.getElementById('fontSizeDisplay');
    if (fontSizeDisplay) {
      fontSizeDisplay.textContent = FONT_SIZES[currentFontSize].label;
    }
  }

  /**
   * Inicializar todo cuando el DOM esté listo
   */
  function init() {
    // Aplicar configuraciones guardadas inmediatamente (antes de que se muestre el contenido)
    applyDarkMode();
    applyFontSize();

    // Inicializar controles cuando el DOM esté listo
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() {
        initDarkMode();
        initFontSize();
      });
    } else {
      initDarkMode();
      initFontSize();
    }
  }

  // Ejecutar inicialización
  init();

})();
