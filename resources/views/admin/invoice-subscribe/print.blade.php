<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Faktur Pembayaran Perpanjang Layanan-INV{{ $invoice->no_ref }}</title>

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

    <style>
        td, th {
            font-size: 12pt
        }
    </style>
</head>
<body>
    <div class="m-4 p-4">
        @php
            $status = 'unpaid.png';

            if($invoice->is_paid){
                $status = 'paid.png';
            }

            if($invoice->is_waiting){
                $status = 'waiting.png';
            }

        @endphp
        <div class="row mb-3">
            <div class="col-6">
                <img src="{{ asset('images/logo/logo.png') }}" alt="logo-bumdes-pintar" class="w-50">
            </div>
            <div class="col-6 my-auto text-end" style="font-size: 25px">
                <img src="{{ asset('images/invoice/' . $status) }}" alt="payment-status" class="w-25 rounded">
            </div>
        </div>
        <div class="row justify-content-center mb-3">
            <div class="col-12 text-center">
                <h1 class="text-gray">Faktur Pembayaran Perpanjang Layanan</h1>
            </div>
        </div>

        <div style="font-size:12pt;">
            <div class="row">
                <div class="col-6">
                    <div class="row p-1 text-uppercase">
                        <div class="col-3">
                            Tanggal
                        </div>
                        <div class="col-9">
                            : {{ $invoice->date_format }}
                        </div>
                    </div>
            
                    <div class="row p-1 text-uppercase">
                        <div class="col-3">
                            No. Ref
                        </div>
                        <div class="col-9">
                            : INV{{ $invoice->no_ref }}
                        </div>
                    </div>
                </div>
                <div class="col-6 text-uppercase">
                    <div class="row">
                        <div class="col-12 fw-bold">
                            KEPADA:
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-12" style="font-size: 25px">
                            {{ $identity['nama_bumdes'] }}
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-12">
                            {{ $identity['alamat'] }}
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-12">
                            {{ $identity['nama_desa'] }}, {{ $identity['nama_kecamatan'] }}, {{ $identity['nama_kabupaten'] }}, {{ $identity['nama_provinsi'] }}
                        </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-12">
                            Kode Pos, {{ $identity['kode_pos'] }}
                        </div>
                    </div>
                </div>
            </div>
            
    
            <table class="table mt-5">
                <thead>
                    <tr>
                        <th style="width: 75%">Deksripsi</th>
                        <th style="width: 25%" class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td style="width: 75%">Paket  {{ $invoice->package == 'yearly' ? 'Tahunan' : 'Bulanan' }} (Aktif Hingga : {{ $subscribe['date_format'] }})-<span id="url"></span></td>
                            <td class="text-end" style="width: 25%">Rp.{{ number_format($invoice->value, 0, '', '.')}}</td>
                        </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th style="width: 75%" class="text-end">Total</th>
                        <th class="text-end" style="width: 25%">Rp.{{ number_format($invoice->value, 0, '', '.')}}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="row fst-italic mt-5">
                <div class="col-12 text-center">
                    Dicetak Pada : {{ $today }}
                </div>
            </div>
        </div>
    </div>
    

    <div class="mx-5">
        <button class="btn btn-success d-print-none" onclick="funcPrint()">print</button>
    </div>
    
    <script>
        function funcPrint()
        {
            window.print()
        }

        window.addEventListener('load', function(){
            document.querySelector('#url').innerText = window.location.hostname
        })
    </script>

    <script src="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/bootstrap.min.js') }}" defer></script>

    {{-- <script src="{{ asset('js/admin/main.js') }}" defer></script>  --}}
    <script src="{{ asset('js/public.js') }}" defer></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>