<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMK TI Airlangga Samarinda - E-Perizinan - @yield('title')</title>
  @vite(['resources/css/style.css', 'resources/js/script.js'])
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="antialiased selection:bg-blue-100 selection:text-blue-700">

  <x-navbar />

  <main class="pt-6 md:pt-10">
    @yield('content')
  </main>

  <x-footer />
</body>

</html>
