<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-BUMDES</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="{{ asset('css/admin/bootstrap.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/iconly/bold.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet">

    <link href="{{ asset('css/admin/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('images/logo/icon.ico') }}" rel="icon">
    

</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                
                <div class="sidebar-header">
                    @if (OverDueSubscribeHelper::overDue()['is_over_due'])
                        <div class="bg-danger p-2 text-white" style="font-size: 14px;border-radius: 10px;">
                            Jatuh Tempo : {{OverDueSubscribeHelper::overDue()['different']}} Hari
                        </div>
                    @endif
                    @if (!OverDueSubscribeHelper::overDue()['is_over_due'] && OverDueSubscribeHelper::overDue()['different'] < 8)
                        <div class="bg-warning p-2 text-white" style="font-size: 14px;border-radius: 10px;">
                            Jatuh Tempo : {{OverDueSubscribeHelper::overDue()['different']}} Hari
                        </div>
                    @endif
                    
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

                        @role('ADMIN')
                            <li class="sidebar-item{{ Request::is('admin') ? ' active' :'' }}">
                                <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                                    <i class="bi bi-grid-fill"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        @endrole

                        <li class="sidebar-item{{ Request::is('identity')? ' active' :'' }}">
                            <a href="{{ route('identity.index') }}" class='sidebar-link'>
                                <i class="bi bi-map"></i>
                                <span>Data Desa</span>
                            </a>
                        </li>

                        @role('ADMIN')
                        <li class="sidebar-item{{ Request::is('business') ? ' active' :'' }}">
                            <a href="{{ route('business.index') }}" class='sidebar-link'>
                                <i class="bi bi-list"></i>
                                <span>Unit Usaha</span>
                            </a>
                        </li>

                        <li class="sidebar-title border-top pt-3">Master</li>
                        <li class="sidebar-item{{ Request::is('contact')? ' active' :'' }}">
                            <a href="{{ route('contact.index') }}" class='sidebar-link'>
                                <i class="bi bi-person-lines-fill"></i>
                                <span>Kontak</span>
                            </a>
                        </li>
                        <li class="sidebar-item{{ Request::is('fixed-asset')? ' active' :'' }}">
                            <a href="{{ route('fixed-asset.index') }}" class='sidebar-link'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-buildings" viewBox="0 0 16 16">
                                    <path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022ZM6 8.694 1 10.36V15h5V8.694ZM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15Z"/>
                                    <path d="M2 11h1v1H2v-1Zm2 0h1v1H4v-1Zm-2 2h1v1H2v-1Zm2 0h1v1H4v-1Zm4-4h1v1H8V9Zm2 0h1v1h-1V9Zm-2 2h1v1H8v-1Zm2 0h1v1h-1v-1Zm2-2h1v1h-1V9Zm0 2h1v1h-1v-1ZM8 7h1v1H8V7Zm2 0h1v1h-1V7Zm2 0h1v1h-1V7ZM8 5h1v1H8V5Zm2 0h1v1h-1V5Zm2 0h1v1h-1V5Zm0-2h1v1h-1V3Z"/>
                                </svg>
                                <span>Harta Tetap</span>
                            </a>
                        </li>

                        <li class="sidebar-title border-top pt-3">Keuangan</li>

                        <li class="sidebar-item has-sub{{ Request::is('account') || Request::is('ledger') || Request::is('journal/*') || Request::is('journal') ? ' active' :'' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-journals"></i>
                                <span>Akuntansi</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item{{ Request::is('account')? ' active' :'' }}">
                                    <a href="{{ route('account.index') }}">Akun</a>
                                </li>
                                <li class="submenu-item{{ Request::is('journal/*') || Request::is('journal')? ' active' :'' }}">
                                    <a href="{{ route('journal.index') }}">Jurnal</a>
                                </li>
                                <li class="submenu-item{{ Request::is('ledger')? ' active' :'' }}">
                                    <a href="{{ route('ledger.index') }}">Buku Besar</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item has-sub{{ Request::is('revenue') || Request::is('revenue/*') || Request::is('expense') || Request::is('expense/*')? ' active' :'' }}">
                            <a href="#" class='sidebar-link'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z"/>
                                    <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z"/>
                                    <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z"/>
                                    <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z"/>
                                </svg>
                                <span>Arus Kas</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item{{ Request::is('revenue') || Request::is('revenue/*')? ' active' :'' }}">
                                    <a href="{{ route('revenue.index') }}">Pendapatan</a>
                                </li>
                                <li class="submenu-item{{ Request::is('expense') || Request::is('expense/*')? ' active' :'' }}">
                                    <a href="{{ route('expense.index') }}">Pengeluaran</a>
                                </li>
                                <li class="submenu-item{{ Request::is('cash-mutation') || Request::is('cash-mutation/*')? ' active' :'' }}">
                                    <a href="{{ route('cash-mutation.index') }}">Mutasi Kas</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item has-sub{{ Request::is('report/*') ? ' active' :'' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-clipboard-data"></i>
                                <span>Laporan</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item{{ Request::is('report/cashflow')? ' active' :'' }}">
                                    <a href="{{ route('report.cashflow.index') }}">Arus Kas</a>
                                </li>
                                <li class="submenu-item{{ Request::is('report/cashflow-year')? ' active' :'' }}">
                                    <a href="{{ route('report.cashflow.year') }}">Arus Kas Tahunan</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="submenu-item{{ Request::is('report/balance')? ' active' :'' }}">
                                    <a href="{{ route('report.balance.index') }}">Neraca</a>
                                </li>
                                <li class="submenu-item{{ Request::is('report/balance-year')? ' active' :'' }}">
                                    <a href="{{ route('report.balance.year') }}">Neraca Tahunan</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="submenu-item{{ Request::is('report/lost-profit')? ' active' :'' }}">
                                    <a href="{{ route('report.lost-profit.index') }}">Laba Rugi</a>
                                </li>
                                <li class="submenu-item{{ Request::is('report/lost-profit-year')? ' active' :'' }}">
                                    <a href="{{ route('report.lost-profit-year.index') }}">Laba Rugi Tahunan</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="submenu-item{{ Request::is('report/changes-in-equity')? ' active' :'' }}">
                                    <a href="{{ route('report.changes-in-equity.index') }}">Perubahan Modal</a>
                                </li>
                                <li class="submenu-item{{ Request::is('report/trial-balance')? ' active' :'' }}">
                                    <a href="{{ route('report.trial-balance.index') }}">Neraca Saldo</a>
                                </li>
                            </ul>
                        </li>
                        @endrole

                        <li class="sidebar-title border-top pt-3">Atur Pengguna</li>

                        <li class="sidebar-item{{ Request::is('users') ? ' active' :'' }}">
                            <a href="{{ route('users.index') }}" class='sidebar-link'>
                                <i class="bi bi-people"></i>
                                <span>Pengguna</span>
                            </a>
                        </li>

                        <li class="sidebar-title border-top pt-3">Layanan</li>

                        <li class="sidebar-item{{ Request::is('over-due') ? ' active' :'' }}">
                            <a href="{{ route('subscribe.overdue') }}" class='sidebar-link'>
                                <i class="bi bi-calendar3"></i>
                                <span>Status</span>
                            </a>
                        </li>

                        <li class="sidebar-item{{ Request::is('invoice-subscribe*') ? ' active' :'' }}">
                            <a href="{{ route('invoice.subscribe.index') }}" class='sidebar-link'>
                                <i class="bi bi-clock-history"></i>
                                <span>Pembayaran</span>
                            </a>
                        </li>

                        @role('DEV')
                        <li class="sidebar-title border-top pt-3">Lainnya</li>
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
                            <a href="#" class='sidebar-link' onclick="logout()">
                                <i class="bi bi-box-arrow-left"></i>
                                <span>Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                                <input type="hidden" name="token" id="token-id">
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

            @if(Auth::user()['is_default'])
                <div class="alert alert-warning row" role="alert">
                    <div class="col-6 my-auto">
                        Silakan Ganti Password Anda 
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('users.change-password') }}" class="btn btn-secondary btn-sm">Ganti Password</a>
                    </div>
                    
                </div>
            @endif

            

            @yield('admin')
            <div id="tost-message">Some text some message..</div>

        </div>
    </div>

    <script src="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/bootstrap.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/main.js') }}" defer></script> 
    <script src="{{ asset('js/public.js') }}" defer></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Z9B8M2Y1DJ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-Z9B8M2Y1DJ');
    </script>
    

    <script>
        @if(session()->has('login'))
        
            const searchOnURI = window.location.search;
            const params = new URLSearchParams(searchOnURI);
            
            const token = params.get('token');
            localStorage.setItem('token', token);

            params.delete('token');

        @endif
    </script>

    @yield('script')

</body>

</html>