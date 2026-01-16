{{-- <div class="sidebar" id="sidebarMenu"> --}}
<div class="sidebar-overlay" id="sidebarOverlay" >

    <div class="sidebar-header text-white text-center py-4">
        <img src="{{ URL('assets/img/new-ust-logo.png') }}" alt="logo" height="100" class="me-2">
        <p></p>
        <h6 class="mb-0 fw-bold">University of Santo Tomas - Legazpi</h6>
    </div>

    <!-- Link CSS -->
    <link rel="stylesheet" href="{{ asset('css/inclayout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <ul class="nav flex-column mt-3">

        <!-- Core -->
        <li class="sidebar-section">Core</li>
        <li>
            <a class="nav-link dashboard-link d-flex align-items-center {{ Request::is('/') ? 'active' : '' }}"
            href="{{ url('/') }}">
                <i class="bi-house-fill me-3"></i> Dashboard
            </a>
        </li>

        <!-- Interface -->
        <li class="sidebar-section mt-3">Modules</li>

        @if(auth()->check() && auth()->user()->usertype == 2)

            <!-- Campus Online Logs -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('elogs*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('elogs*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseElogs"
                        aria-expanded="{{ Request::is('elogs*') ? 'true' : 'false' }}"
                        aria-controls="collapseElogs">
                            <i class="bi-list-check me-3"></i> Campus Online Logs
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ Request::is('elogs*') ? 'show' : '' }}" id="collapseElogs">
                        <ul class="nav flex-column ms-4">
                            <li>
                                <a class="nav-link {{ Request::is('elogs') ? 'active' : '' }}"
                                href="{{ url('/elogs') }}">E-Logs</a>
                            </li>
                            <li>
                                <a class="nav-link {{ Request::is('elogs/list*') ? 'active' : '' }}"
                                href="{{ url('/elogs/list') }}">E-Logs List</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>

            <!-- Admission -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('admission*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('admission*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseAdmission"
                        aria-expanded="{{ Request::is('admission*') ? 'true' : 'false' }}"
                        aria-controls="collapseAdmission">
                            <i class="bi-pencil-square me-3"></i> Admission
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ Request::is('admission*') ? 'show' : '' }}" id="collapseAdmission">
                        <ul class="nav flex-column ms-4">
                            <li>
                                <a class="nav-link {{ Request::is('admission/pse*') ? 'active' : '' }}"
                                href="{{ url('/admission/pse') }}">PSE Admission</a>
                            </li>
                            <li>
                                <a class="nav-link {{ Request::is('admission/jhs*') ? 'active' : '' }}"
                                href="{{ url('/admission/jhs') }}">JHS Admission</a>
                            </li>
                            <li>
                                <a class="nav-link {{ Request::is('admission/shs*') ? 'active' : '' }}"
                                href="{{ url('/admission/shs') }}">SHS Admission</a>
                            </li>
                            <li>
                                <a class="nav-link {{ Request::is('admission/college*') ? 'active' : '' }}"
                                href="{{ url('/admission/college') }}">College Admission</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            
            <!-- Department -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('department*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('department*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseDepartment"
                        aria-expanded="{{ Request::is('department*') ? 'true' : 'false' }}"
                        aria-controls="collapseDepartment">
                            <i class="bi bi-diagram-3-fill me-3"></i> Department
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ Request::is('department*') ? 'show' : '' }}" id="collapseDepartment">
                        <ul class="nav flex-column ms-4">
                            <li>
                                <a class="nav-link {{ Request::is('department/student*') ? 'active' : '' }}"
                                href="{{ url('/department/studentlisting') }}">Student Listing</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>

            <!-- Enrollment -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('enrollment*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('enrollment*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseEnrollment"
                        aria-expanded="{{ Request::is('enrollment*') ? 'true' : 'false' }}"
                        aria-controls="collapseEnrollment">
                            <i class="bi bi-list-check me-3"></i> Enrollment
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ Request::is('enrollment*') ? 'show' : '' }}" id="collapseEnrollment">
                        <ul class="nav flex-column ms-4">
                            <li>
                                <a class="nav-link {{ Request::is('enrollment/subjectmanager*') ? 'active' : '' }}"
                                href="{{ url('/enrollment/subjectmanager') }}">Subject Management</a>
                            </li>
                            <li>
                                <a class="nav-link {{ Request::is('enrollment/curriculum*') ? 'active' : '' }}"
                                href="{{ url('/enrollment/curriculum') }}">Curriculum Management</a>
                            </li>
                        </ul>
                    </div> 
                </div>
            </li>
            
            <!-- Accounting -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('accounting*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('accounting*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseAccounting"
                        aria-expanded="{{ Request::is('department*') ? 'true' : 'false' }}"
                        aria-controls="collapseAccounting">
                            <i class="bi bi-folder-fill me-3"></i> Accounting
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ Request::is('accounting*') ? 'show' : '' }}" id="collapseAccounting">
                        <ul class="nav flex-column ms-4">
                            <li>
                                <a class="nav-link {{ Request::is('accounting/feesmanagement*') ? 'active' : '' }}"
                                href="{{ url('/accounting/feesmanagement') }}">Fees Management</a>
                            </li>
                            <li>
                                <a class="nav-link {{ Request::is('accounting/installmentscheme*') ? 'active' : '' }}"
                                href="{{ url('/accounting/installmentscheme') }}">Installment Scheme</a>
                            </li>
                        </ul>
                    </div> 
                </div>
            </li>
            
            <!-- Cashiering -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('cashier*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('cashier*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseCashiering"
                        aria-expanded="{{ Request::is('cashier*') ? 'true' : 'false' }}"
                        aria-controls="collapseCashiering">
                            <i class="bi bi-cash-coin me-3"></i></i> Cashiering
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ Request::is('cashier*') ? 'show' : '' }}" id="collapseCashiering">
                        <ul class="nav flex-column ms-4">
                            <li>
                                <a class="nav-link {{ Request::is('cashier/collections*') ? 'active' : '' }}"
                                href="{{ url('/cashier/collections') }}">Collections</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            
     <!-- Registrar -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('registrar*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('registrar*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseRegistrar"
                        aria-expanded="{{ Request::is('registrar*') ? 'true' : 'false' }}"
                        aria-controls="collapseRegistrar">
                            <i class="bi bi-mortarboard-fill me-3"></i> Registrar
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ Request::is('registrar*') ? 'show' : '' }}" id="collapseRegistrar">
                        <ul class="nav flex-column ms-4">
                            <li>
                                <a class="nav-link {{ Request::is('registrar/student*') ? 'active' : '' }}"
                                href="{{ url('/registrar/student') }}">Student Management</a>
                            </li>
                            <li>
                                <a class="nav-link {{ Request::is('registrar/otr*') ? 'active' : '' }}"
                                href="{{ url('/registrar/otr') }}">OTR Management</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            
            <!-- Human Resources -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('hr*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('hr*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseHr"
                        aria-expanded="{{ Request::is('hr*') ? 'true' : 'false' }}"
                        aria-controls="collapseHr">
                            <i class="bi bi-people-fill me-3"></i> Human Resources
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ Request::is('hr*') ? 'show' : '' }}" id="collapseHr">
                        <ul class="nav flex-column ms-4">
                            <li>
                                <a class="nav-link {{ Request::is('hr/employees*') ? 'active' : '' }}"
                                href="{{ url('/hr/employees') }}">Employee Management</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>

            <!-- Laboratory & Inventory Department -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('lid*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('lid*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseLid"
                        aria-expanded="{{ Request::is('lid*') ? 'true' : 'false' }}"
                        aria-controls="collapseLid">
                            <i class="bi bi-flask-fill me-3"></i> LID - Laboratory
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="collapse {{ Request::is('lid*') ? 'show' : '' }}" id="collapseLid">
                        <ul class="nav flex-column ms-4">

                            <li>
                                <a class="nav-link {{ Request::is('lid/reservations') ? 'active' : '' }}"
                                href="{{ url('/lid/reservations') }}">
                                    Reservation Management
                                </a>
                            </li>

                            <li>
                                <a class="nav-link {{ Request::is('lid/reservations/student-create') ? 'active' : '' }}"
                                href="{{ url('/lid/reservations/student-create') }}">
                                    Student Reservation
                                </a>
                            </li>

                            <li>
                                <a class="nav-link {{ Request::is('lid/inventory*') ? 'active' : '' }}"
                                href="{{ url('/lid/inventory') }}">
                                    Laboratory Inventory
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </li>

            <!-- PPFMO - Physical Plant & Facilities Management Office -->
            <li class="nav-item">
                <div class="menu-block {{ Request::is('ppfmo*') ? 'with-bg' : '' }}">
                    <a class="nav-linkhead {{ Request::is('ppfmo*') ? 'active' : 'collapsed' }}"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapsePpfmo"
                        aria-expanded="{{ Request::is('ppfmo*') ? 'true' : 'false' }}"
                        aria-controls="collapsePpfmo">
                            <i class="bi bi-tools me-3"></i> PPFMO - Physical Plant
                            <div class="ms-auto"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="collapse {{ Request::is('ppfmo*') ? 'show' : '' }}" id="collapsePpfmo">
                        <ul class="nav flex-column ms-4">

                            <li>
                                <a class="nav-link {{ Request::is('ppfmo/service-requests') ? 'active' : '' }}"
                                href="{{ route('ppfmo.service-requests.index') }}">
                                    Service Requests
                                </a>
                            </li>

                            <li>
                                <a class="nav-link {{ Request::is('ppfmo/service-requests/create') ? 'active' : '' }}"
                                href="{{ route('ppfmo.service-requests.create') }}">
                                    Create New Request
                                </a>
                            </li>

                            <li>
                                <a class="nav-link {{ Request::is('ppfmo/service-requests/reports') ? 'active' : '' }}"
                                href="#">
                                    Reports & Analytics
                                </a>
                            </li>

                            <li>
                                <a class="nav-link {{ Request::is('ppfmo/maintenance-personnel') ? 'active' : '' }}"
                                href="#">
                                    Maintenance Personnel
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </li>
            
        @endif


    </ul>

    <!-- Footer -->
    {{-- <div class="sidebar-footer mt-auto text-center py-3 small">
        <div class="text-white-50">Logged in as: <strong>{{ strtoupper(Auth::user()->name) }}</strong></div>
    </div> --}}
    
</div>

    