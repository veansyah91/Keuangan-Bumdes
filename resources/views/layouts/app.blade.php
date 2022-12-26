<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ IdentityHelp::getDesa() }}
    </title>

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('css/business/custom.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app" class="d-print-none">
        <div class="container">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" id="nav">
                <div class="container-fluid">
                    <a class="navbar-brand fs-3 fw-bold" href="{{ url('/') }}">
                        BUMDes
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
    
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        @auth
                            @yield('navmenu')
                        @endauth                       
    
                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle dropdown-nav" href="#" id="navbarDropdown" role="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ Auth::user()->name }}
                                    </a>
    
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        
                                        @hasrole('ADMIN')
                                            <li>
                                                <a class="dropdown-item active" href="{{ route('business.index') }}"
                                                    >
                                                    Halaman Admin
                                                </a>
                                            </li>
                                        @endhasrole

                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.change-password') }}"
                                                >
                                                Ubah Password
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                {{ __('Logout') }}
                                            </a>
                                        </li>                                        
    
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </ul>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>

            {{-- Toast  --}}
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
                <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="toast-body">
                            
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="fixed-bottom text-end p-2 m-2 d-md-none d-block">
        <a class="btn btn-secondary rounded-circle" href="#nav">
            <i class="bi bi-arrow-up-circle"></i>
        </a>
    </div>

    <div id="print" class="d-print-block d-none mt-3 mb-2 font-monospace" style="width: 55mm;color:black">
        
    </div>    
    <div id="tost-message">Some text some message..</div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="{{ asset('js/business/main.js') }}" defer></script> 
    <script src="{{ asset('js/public.js') }}" defer></script> 

    {{-- <script src="{{ asset('js/dist/just-validate.production.min') }}"></script> --}}

    {{-- Toast JS --}}
    @if (Session::has('Success'))
        <!-- JavaScript Bundle with Popper -->
        
        <script>
            let toastLiveExample = document.getElementById('liveToast')
            let toast = new bootstrap.Toast(toastLiveExample)

            let toastBody = document.getElementById('toast-body');
            toastBody.innerHTML = `{!!Session::get('Success')!!}`
            toast.show();

        </script>
    @endif
    @if(session()->has('login'))
        <script>
        
            const searchOnURI = window.location.search;
            const params = new URLSearchParams(searchOnURI);
            
            const token = params.get('token');
            localStorage.setItem('token', token);

            params.delete('token');

        </script>
    @endif

    @yield('script')

</body>

</html>
