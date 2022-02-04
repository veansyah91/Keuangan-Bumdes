<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <link href="{{ asset('css/admin/bootstrap.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/iconly/bold.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="/">Admin</a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">

                        <li class="sidebar-item{{ Request::is('admin') ? ' active' :'' }}">
                            <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item{{ Request::is('identity')? ' active' :'' }}">
                            <a href="{{ route('identity.index') }}" class='sidebar-link'>
                                <i class="bi bi-map"></i>
                                <span>Data Desa</span>
                            </a>
                        </li>

                        @role('ADMIN')
                        <li class="sidebar-item has-sub{{ Request::is('income') || Request::is('outcome')? ' active' :'' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-cash-stack"></i>
                                <span>Keuangan</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item{{ Request::is('income')? ' active' :'' }}">
                                    <a href="{{ route('income.index') }}">Uang Masuk</a>
                                </li>
                                <li class="submenu-item{{ Request::is('outcome')? ' active' :'' }}">
                                    <a href="{{ route('outcome.index') }}">Uang Keluar</a>
                                </li>
                                
                            </ul>
                        </li>

                        <li class="sidebar-item{{ Request::is('business') ? ' active' :'' }}">
                            <a href="{{ route('business.index') }}" class='sidebar-link'>
                                <i class="bi bi-list"></i>
                                <span>Unit Usaha</span>
                            </a>
                        </li>
                        @endrole

                        <li class="sidebar-item{{ Request::is('users') ? ' active' :'' }}">
                            <a href="{{ route('users.index') }}" class='sidebar-link'>
                                <i class="bi bi-people"></i>
                                <span>Users</span>
                            </a>
                        </li>

                        @role('DEV')
                            <li class="sidebar-item{{ Request::is('roles') ? ' active' :'' }}">
                                <a href="{{ route('roles.index') }}" class='sidebar-link'>
                                    <i class="bi bi-sliders"></i>
                                    <span>Roles</span>
                                </a>
                            </li>

                            <li class="sidebar-item{{ Request::is('import-asset') ? ' active' :'' }}">
                                <a href="{{ route('import-asset') }}" class='sidebar-link'>
                                    <i class="bi bi-box-arrow-in-down"></i>
                                    <span>Import Asset</span>
                                </a>
                            </li>
                        @endrole

                        <li class="sidebar-title border-top pt-3">Profile</li>

                        <li class="sidebar-item">
                            <li class="sidebar-item{{ Request::is('/users/change-password') ? ' active' :'' }}">
                                <a href="{{ route('users.change-password') }}" class='sidebar-link'>
                                    <i class="bi bi-gear"></i>
                                    <span>Ubah Sandi</span>
                                </a>
                            </li>
                            <a href="#" class='sidebar-link' onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-left"></i>
                                <span>Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            @yield('admin')
        </div>
    </div>

    <script src="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/bootstrap.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/main.js') }}" defer></script> 
      
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>


    @yield('script')

</body>

</html>