<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Dashboard - KTU Shipyard')</title>
    <!-- Pastikan Font Awesome versi 6 aktif -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-o1rZqU8+lnyBjC4gP4n0mE5GzZcNrbf4qjzQ1cZTtVq0QlrfR3e1tbdD8T9gQ4tWYm59lBRQMRsYdR0g+6v0eA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">

    @vite(['resources/css/it.css', 'resources/js/it.js'])

</head>

<body>
    @include('it.components-it.sidebar-it')
    @include('it.components-it.navbar-it')

    <div class="main-content">
        @yield('content')
    </div>

    @include('it.partials.confirm-modal')
    @include('it.partials.toast')
    <!-- 👇 TAMBAHKAN FOOTER DI SINI -->
    @include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>

</html>
