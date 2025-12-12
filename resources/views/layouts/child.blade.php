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
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" >

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

    <!-- ECharts -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.2/dist/echarts.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Link CSS -->
    <link rel="stylesheet" href="{{ asset('css/masterdashboard.css') }}">
    

</head>

<body>
    <!-- Mobile/Tablet Sidebar Backdrop -->
    {{-- <div class="sidebar-backdrop" id="sidebarBackdrop"></div> --}}

    <!-- Sidebar -->
    <div class="sidebar" id="sidebarMenu">
        @include('Template.sidebar')
    </div>

    <!-- Main Content -->
    <div class="content">
        {{-- @include('Template.navbar') --}}

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
