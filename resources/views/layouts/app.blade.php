<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Keystone Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="container-custom">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      @include('partials.sidebar')
    </div>
   
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
  
      @yield('content')
    </div>
  </div>

  <script src="{{ asset('js/script.js') }}?v={{ time() }}"></script>
</body>
</html>
