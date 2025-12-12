@section('title', 'University of Santo Tomas - Legazpi')
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<style>
    .legend-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
</style>


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL('img/new-ust-logo.png') }}" type="image/png">
    <title>University of Santo Tomas - Legazpi :: UMS</title>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap5-3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap-icons/font/bootstrap-icons.css') }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Link CSS -->
    <link rel="stylesheet" href="{{ asset('css/masterdashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <!-- Mobile/Tablet Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebarMenu">
        @include('Template.sidebar')
    </div>

    <!-- Main Content -->
    <div class="content">
        @include('Template.navbar')

        <main>
            <div class="card-custom shadow-sm p-4 bg-white rounded">
                @yield('content')
            </div>
        </main>

        {{-- @include('Template.footer') --}}
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>

    <!-- jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        const sidebar = document.getElementById('sidebarMenu');
        const backdrop = document.getElementById('sidebarBackdrop');

        document.querySelectorAll('.toggle-sidebar').forEach(btn => {
            btn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                backdrop.classList.toggle('active');
            });
        });

        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('active');
            backdrop.classList.remove('active');
        });
    </script>
</body>
</html>
