@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Buku Tabungan</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/iconly/bold.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet">

    <link href="{{ asset('css/admin/custom.css') }}" rel="stylesheet">
    <style>
        td, th {
            font-size: 12pt
        }
    </style>
</head>
<body>
    <div class="p-4" data-business="{{ $business->id }}" data-user="{{ $id }}" id="content">
        <div class="row justify-content-center mb-3">
            <div class="col-6">
                <h4 class="text-gray font-bold">Unit Usaha : {{ $business->nama }}</h4>
            </div>
            <div class="col-6 text-end d-print-none">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal" onclick="filterButton()"><i class="bi bi-filter"></i></button>
            </div>
        </div>
        
        <div class="row justify-content-center mb-3">
            <div class="col-12 text-center">
                <h3 class="text-gray">Buku Tabungan</h3>
            </div>
        </div>

        <div style="font-size:12pt;" class="mt-5">
            <div class="row justify-content-between">
                <div class="col-4">
                    <h5>Nomor Rekening: <span id="no_ref"></span></h5>
                </div>
                <div class="col-4">
                    <h5>Nama: <span id="name"></span></h5>
                </div>
                <div class="col-4 text-end">
                    <p class="fst-italic">Periode: <span id="period"></span></p>
                </div>
            </div>
            
            <table class="table">
                <thead style="border: solid 0 0 black">
                    <tr>
                        <th style="width: 10%">Tanggal</th>
                        <th style="width: 15%">No. Referensi</th>
                        <th style="width: 20%">Deskripsi</th>
                        <th class="text-end" style="width: 15%">Mutasi (IDR)</th>
                        <th class="text-end" style="width: 2%"></th>
                        <th class="text-end" style="width: 15%">Saldo (IDR)</th>
                    </tr>
                </thead>
                <tbody id="list-data">
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-end mx-2" style="margin-top: 6rem">
        <div class="col-4 text-center p-1" style="border-top: 1px solid black;">
            {{ Auth::user()->name }}
        </div>
    </div>
    
    <div class="row justify-content-center mb-3 p-4">
        <div class="col-12">
            <button class="btn btn-success d-print-none" onclick="funcPrint()">print</button>
        </div>
    </div>

    {{-- filter  --}}
    {{-- Modal Filter --}}
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description-input" class="form-label">Tanggal</label>
                        <select class="form-select" id="select-filter" onchange="changeFilter(this)">
                            <option value="today">Hari Ini</option>
                            <option value="this week">Minggu Ini</option>
                            <option value="this month">Bulan Ini</option>
                            <option value="this year">Tahun Ini</option>
                            <option value="custom">Custom</option>
                        </select>
                        
                    </div>
                    <div class="row d-none" id="date-range">
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <label for="start-filter" class="form-label">Dari</label>
                                <input type="date" class="form-control" id="start-filter" placeholder="Tanggal">
                                
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <label for="end-filter" class="form-label">Ke</label>
                                <input type="date" class="form-control" id="end-filter" placeholder="Tanggal">
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="submitFilter()" data-bs-dismiss="modal">
                        Ok    
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function funcPrint()
        {
            window.print()
        }
    </script>
    <script src="/js/business/saving-account/book.js"></script>

    <script src="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/bootstrap.min.js') }}" defer></script>

    <script src="{{ asset('js/public.js') }}" defer></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>