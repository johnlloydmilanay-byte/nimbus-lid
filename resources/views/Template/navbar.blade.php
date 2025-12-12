<script>
    document.querySelector('.toggle-sidebar').addEventListener('click', function () {
        document.getElementById('sidebarMenu').classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
    });
</script>

<nav class="navbar navbar-expand navbar-dark custom-navbar">
    <!-- Sidebar Toggle (Hamburger) -->
    <button class="btn btn-link text-white me-3 toggle-sidebar" type="button">
        <i class="fas fa-bars fa-lg"></i>
    </button>

    <!-- Navbar Brand -->
    <a class="navbar-brand ps-2 d-flex align-items-center">
        <span class="fw-bold">University Management System</span>
    </a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Search Form -->
        <form class="d-none d-md-inline-block form-inline me-3">
            <div class="input-group custom-search">
                <input class="form-control border-0 shadow-none" type="text" placeholder="Search..." aria-label="Search">
                <button class="btn btn-gold" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <!-- User Dropdown -->
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" id="navbarDropdown" href="#" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle fa-lg"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                    {{-- <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('password.change') }}">
                            <i class="fas fa-cog me-2 text-secondary"></i> Change Password
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li> --}}
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa-solid fa-arrow-right-from-bracket me-2 text-danger"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
        
<!-- Link CSS -->
<link rel="stylesheet" href="{{ asset('css/inclayout.css') }}">
