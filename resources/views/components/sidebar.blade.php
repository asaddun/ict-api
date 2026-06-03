<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link d-flex align-items-center">
        <i class="fas fa-laptop-code ms-3 me-2"></i>
        <span class="brand-text">ICT Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            @php
                $code = session('qrcode');
                $photoUrl = $code
                    ? url("https://api-ess.adyawinsa.com/assets/img/employee/{$code}_PHOTO.png")
                    : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
            @endphp
            <div class="image">
                <img src={{ $photoUrl }} class="img-circle elevation-2 bg-white"
                    onerror="this.onerror=null;this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png';"
                    alt="User Image">
            </div>
            <div class="info">
                <span class="d-block text-white">{{ session('fullname') }}</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2 flex-grow-1">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class -->
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ Route::is('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-house"></i>
                        <p>Home</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('andon') }}" class="nav-link {{ Route::is('andon') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-toggle-on"></i>
                        <p>Andon</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('sensor') }}" class="nav-link {{ Route::is('sensor') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-microchip"></i>
                        <p>Sensor</p>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('locker.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Route::is('locker.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-door-closed"></i>
                        <p>
                            Locker
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('locker.active') }}"
                                class="nav-link {{ Route::is('locker.active') ? 'active' : '' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>Active</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('locker.history') }}"
                                class="nav-link {{ Route::is('locker.history') ? 'active' : '' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>History</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('locker.control') }}"
                                class="nav-link {{ Route::is('locker.control') ? 'active' : '' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>Control</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('locker.access') }}"
                                class="nav-link {{ Route::is('locker.access') ? 'active' : '' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>Access</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link {{ Route::is('copier.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-print"></i>
                        <p>
                            Copier
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('copier.dashboard') }}"
                                class="nav-link {{ Route::is('copier.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('copier.history') }}"
                                class="nav-link {{ Route::is('copier.history') ? 'active' : '' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>History</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Logout button -->
        <div class="mt-auto py-2">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="{{ route('auth.logout') }}" class="nav-link bg-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </div>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
