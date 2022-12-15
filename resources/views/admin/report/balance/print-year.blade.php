<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Neraca Tahunan</title>

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
    <div class="p-4">
        {{-- Header --}}
        @if ($identity)
            <div class="header row">
                <div class="col-2" id="logo-bumdes">
                    <img src="{{ asset($identity['logo_usaha']) }}" alt="logo_usaha" class="img-fluid mt-2 p-3" alt="logo_usaha" width="200px">
                </div>
                <div class="col-10 my-auto" id="identity">
                    <h4 class="font-bold">{{ $identity['nama_bumdes'] }}</h4>
                    {{ $identity['nama_desa'] }}
                </div>
            </div>
        @endif
        

        <div class="row justify-content-center mb-2 mt-2">
            <div class="col-12 text-center">
                <h5 class="text-black font-bold" style="font-size: 16pt">Laporan Neraca
                <h5 class="text-black font-bold" style="font-size: 12pt">Untuk Tahun yang Berakhir Pada Tanggal 31 Desember <span class="periods" id="period-title"></span>

            </div>
        </div>

        <div class="mt-5">
            <div class="row justify-content-center">
                <div class="col-10 text-end fst-italic">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th class="w-40" style="width: 40%"></th>
                                <th class="text-end w-25 font-bold" id="period" style="width: 25%"></th>
                                <th class="text-end w-25 font-bold" id="period-before" style="width: 25%"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-10">
                    <h5>Asset</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    Aset Lancar
                                </th>
                            </tr>
                        </thead>
                        <tbody id="current-asset">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Aset Lancar</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-current-asset-now">
                                            
                                        </div>
                                    </div>      
                                </th>
                                <td class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-current-asset-before">
                                            
                                        </div>
                                    </div>   
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    Aset Tetap
                                </th>
                            </tr>
                        </thead>
                        <tbody id="non-current-asset">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Aset Tetap</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-non-current-asset-now">
                                            
                                        </div>
                                    </div>      
                                </th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-non-current-asset-before">
                                            
                                        </div>
                                    </div> 
                                </th>
                            </tr>
                        </tfoot>
                    </table>


                    <table class="table">
                        <thead>
                            <tr class="text-primary">
                                <th class="w-40" style="width: 40%">Total Aset</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-asset-now">
                                            
                                        </div>
                                    </div>  </th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-asset-before">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-10">
                    <h5>Kewajiban</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Utang Jangka Pendek</th>
                            </tr>
                        </thead>
                        <tbody id="short-term-liability">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Kewajiban Jangka Pendek</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-short-term-liability-now">
                                            
                                        </div>
                                    </div>      
                                </th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-short-term-liability-before">
                                            
                                        </div>
                                    </div>   
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Utang Jangka Panjang</th>
                            </tr>
                        </thead>
                        <tbody id="long-term-liability">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Kewajiban Jangka Panjang</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-long-term-liability-now">
                                            
                                        </div>
                                    </div>      
                                </th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-long-term-liability-before">
                                            
                                        </div>
                                    </div>     
                                </th>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Kewajiban</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-liability-now">
                                            
                                        </div>
                                    </div>  </th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-liability-before">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-10">
                    <h5>Modal</h5>
                    <table class="table">
                        <tbody id="equity">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Modal</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-equity-now">
                                            
                                        </div>
                                    </div>  
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-equity-before">
                                            
                                        </div>
                                    </div>  
                                </th>
                            </tr>
                           
                        </tfoot>
                        
                    </table>

                    <table class="table">
                        <thead>
                            <tr class="text-primary">
                                <th class="w-40" style="width: 40%">Total Kewajiban dan Modal</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-liability-equity-now">
                                            
                                        </div>
                                    </div>  
                                </th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-liability-equity-before">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </thead>
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

    <script src="/js/admin/report/balance-year-print.js"></script>
    <script src="/js/admin/report/api.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>