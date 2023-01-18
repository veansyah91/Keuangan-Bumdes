<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Laba Rugi Tahunan</title>

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
        th, th {
            font-size: 12pt
        }
    </style>
</head>
<body>
    <div class="p-4">
        {{-- Header --}}
        @if ($identity)
            <div class="header row">
                <div class="col-2" id="logo-bumdes">
                    <img src="{{ asset($identity['logo_usaha']) }}" alt="logo_usaha" class="img-fluid mt-2 p-3" alt="logo_usaha" width="200px" loading='lazy'>
                </div>
                <div class="col-10 my-auto" id="identity">
                    <h4 class="font-bold">{{ $identity['nama_bumdes'] }}</h4>
                    {{ $identity['nama_desa'] }}
                </div>
            </div>
        @endif
        
        <div class="row justify-content-center mb-3">
            <div class="col-12 text-end">
                <h4 class="text-gray font-bold">Unit Usaha : {{ $business->nama }}</h4>
            </div>
        </div>

        <div class="row justify-content-center mb-2" data-business="{{ $business->id }}" id="content">
            <div class="col-12 text-center">
                <h5 class="text-black font-bold" style="font-size: 16pt">Laporan Laba Rugi
                <h5 class="text-black font-bold" style="font-size: 12pt">Untuk Tahun yang Berakhir Pada Tanggal 31 Desember <span class="periods" id="period-title"></span>

            </div>
        </div>

        <div class="mt-3">
            <div class="row justify-content-center">
                <div class="col-10 text-end fst-italic">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-50"></th>
                                <th class="text-end w-25 font-bold" id="period" style="font-size: 12pt"></th>
                                <th class="text-end w-25 font-bold" id="period-before" style="font-size: 12pt"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="row justify-content-center mt-3">
                <div class="col-10">
                    <h5>Pendapatan</h5>
                    <table class="table">
                        <tbody id="revenue">
                            <tr>
                                
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-50">Total Pendapatan</th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-revenue-now">
                                            
                                        </div>
                                    </div>  
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-revenue-before">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
            </div>

            <div class="row justify-content-center">
                <div class="col-10">
                    <h5>Beban</h5>
                    <table class="table">
                        <tbody id="cost">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-50">Total Biaya</th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-cost-now">
                                            
                                        </div>
                                    </div>
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-cost-before">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-10">
                    <table class="table">
                        <tfoot>
                            <tr>
                                <th class="w-50">Laba (Rugi) Kotor</th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="gross-lost-profit-now">
                                            
                                        </div>
                                    </div> 
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="gross-lost-profit-before">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table class="table">
                        <tfoot>
                            <tr>
                                <th class="w-50">Laba (Rugi) Bersih Sebelum Pajak</th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="lost-profit-before-tax-now">
                                            
                                        </div>
                                    </div> 
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="lost-profit-before-tax-before">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table class="table">
                        <tfoot>
                            <tr>
                                <th class="w-50">Laba (Rugi) Bersih Setelah Pajak</th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="lost-profit-after-tax-now">
                                            
                                        </div>
                                    </div>    
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="lost-profit-after-tax-before">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-10">
            <div class="row justify-content-end mx-2">
                <div class="col-5 text-start p-1 font-bold d-flex" id="address-date">
                    <div id="location-print-label">
                        <button class="btn btn-sm d-print-none"><i class="bi bi-pencil"></i></button><span >Air Molek </span>
                    </div>
                    <div>
                        , 31 Desember <span id="year-label"></span>
                    </div>
                </div>
                <form class="col-4 row g-3 d-print-none d-none" id="position-print-form" onsubmit="submitLocation(event)">
                    <div class="col-auto">
                      <label for="position-print-input" class="visually-hidden">Tambah Lokasi</label>
                      <input type="text" class="form-control" id="position-print-input" placeholder="Lokasi">
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-primary mb-3"><i class="bi bi-check"></i></button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-danger mb-3" onclick="cancelEditLocation()"><i class="bi bi-x"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="row justify-content-end mx-2">
                <div class="col-5 text-start p-1 font-bold" id="user-position">
                    <div id="user-position-label" class=" text-danger fst-italic">
                        (belum ada jabatan) <button class="btn btn-sm"><i class="bi bi-pencil"></i></button>
                    </div>
                    
                    <form class="row d-none g-3 d-print-none" id="user-position-form" onsubmit="submitPosition(event)">
                        <div class="col-auto">
                          <label for="user-position-input" class="visually-hidden">Tambah Posisi</label>
                          <input type="text" class="form-control" id="user-position-input" placeholder="Posisi">
                        </div>
                        <div class="col-auto">
                          <button type="submit" class="btn btn-primary mb-3"><i class="bi bi-check"></i></button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger mb-3" onclick="cancelEditPosition()"><i class="bi bi-x"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="row justify-content-end mx-2" style="margin-top: 6rem">
                <div class="col-5 text-start p-1 font-bold">
                    <div id="user-name-label" class="text-danger fst-italic">
                        (belum ada nama) <button class="btn btn-sm"><i class="bi bi-pencil"></i></button>
                    </div>
                    
                    <form class="row g-3 d-none d-print-none" id="user-name-form" onsubmit="submitName(event)">
                        <div class="col-auto">
                          <label for="user-name-input" class="visually-hidden">Tambah Nama</label>
                          <input type="text" class="form-control" id="user-name-input" placeholder="Posisi">
                        </div>
                        <div class="col-auto">
                          <button type="submit" class="btn btn-primary mb-3"><i class="bi bi-check"></i></button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger mb-3" onclick="cancelEditName()"><i class="bi bi-x"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center mb-3" id="print-button">
        <div class="col-10">
            <button class="btn btn-success d-print-none" onclick="funcPrint()">print</button>
        </div>
    </div>
    
    <script>
        function funcPrint()
        {
            window.print()
        }
    </script>

    <script src="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/bootstrap.min.js') }}" defer></script>

    <script src="{{ asset('js/public.js') }}" defer></script> 

    <script src="/js/business/report/lost-profit-year-print.js"></script>
    <script src="/js/business/report/api.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>