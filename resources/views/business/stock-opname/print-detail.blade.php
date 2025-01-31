<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Stok Opname </title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/iconly/bold.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet">

    <link href="{{ asset('vendors/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    {{-- <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet">

    <link href="{{ asset('css/admin/custom.css') }}" rel="stylesheet"> --}}
    <style>
        td, th {
            font-size: 12pt
        }
    </style>
</head>
<body>
    <div class="m-4 p-4">
        <div class="row justify-content-center mb-3">
            <div class="col-12 text-start">
                <h4 class="text-gray font-bold">Unit Usaha : {{ $business->nama }}</h4>
            </div>
        </div>

        <div class="row justify-content-center mb-3">
            <div class="col-12 text-center">
                <h4 class="text-gray font-bold">Laporan Rincian Stok Opname</h4>
            </div>
        </div>

        <div style="font-size:12pt;">
            <div class="row p-1">
                <div class="col-3">
                    Tanggal
                </div>
                <div class="col-9">
                    : {{ $stockOpname->date_format }}
                </div>
            </div>
    
            <div class="row p-1">
                <div class="col-3">
                    Kode
                </div>
                <div class="col-9">
                    : {{ $stockOpname->no_ref }}
                </div>
            </div>
    
            <div class="row p-1">
                <div class="col-3">
                    Deskripsi
                </div>
                <div class="col-9">
                    : {{ $stockOpname->description }}
                </div>
            </div>
    
            <table class="table mt-5">
                <thead style="border: solid 0 0 black">
                    <tr>
                        <th>Kode</th>
                        <th >Produk</th>
                        <th class="text-end">Tersedia (Buku)</th>
                        <th class="text-end">Tersedia (Fisik)</th>
                        <th class="text-end">Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stockOpname->listInput as $ledger)
                        <tr>
                            <td>{{ $ledger['productCode'] }}</td>
                            <td>{{ $ledger['productName'] }}</td>
                            <td class="text-end">{{ $ledger['qty_book'] }}</td>
                            <td class="text-end">{{ $ledger['qty_book'] + $ledger['qty_balance']}}</td>
                            <td class="text-end">{{ $ledger['qty_balance'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        
    </div>

    <div class="row justify-content-end mx-5" style="margin-top: 6rem">
        <div class="col-4 text-center" style="border-top: 1px solid black;">
            {{ $stockOpname->author }}
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
    </script>

    <script src="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/bootstrap.min.js') }}" defer></script>

    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>