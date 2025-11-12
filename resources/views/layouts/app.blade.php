<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Panel')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  @include('layouts.navbar')

  <!-- Sidebar -->
  @include('layouts.sidebar')

  <!-- Content -->
  <div class="content-wrapper p-3">
      @yield('content')
  </div>

  <!-- Footer -->
  @include('layouts.footer')

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const toggleButton = document.querySelector('[data-sidebar-toggle]');
    const storageKey = 'sidebar-collapsed';

    if (localStorage.getItem(storageKey) === 'true') {
      body.classList.add('sidebar-collapse');
    }

    if (toggleButton) {
      toggleButton.addEventListener('click', (event) => {
        event.preventDefault();
        body.classList.toggle('sidebar-collapse');
        localStorage.setItem(storageKey, body.classList.contains('sidebar-collapse'));
      });
    }
  });
</script>
</body>
</html>
