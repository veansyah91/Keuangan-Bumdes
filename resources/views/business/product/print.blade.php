@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Produk</title>

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
        <div class="row justify-content-center mb-3">
            <div class="col-12 text-start">
                <h4 class="text-gray font-bold">Unit Usaha : {{ $business->nama }}</h4>
            </div>
        </div>
        
        <div class="row justify-content-center mb-3">
            <div class="col-12 text-center">
                <h3 class="text-gray">Laporan Daftar Produk</h3>
            </div>
        </div>

        <div style="font-size:12pt;" class="mt-5">
            <table class="table">
                <thead style="border: solid 0 0 black">
                    <tr>
                        <th style="width: 20%">Kode</th>
                        <th style="width: 25%">Nama</th>
                        <th style="width: 10%">Kategori</th>
                        <th class="text-end" style="width: 10%">Stok</th>
                        <th class="text-end" style="width: 15%">HPP(IDR)</th>
                        <th class="text-end" style="width: 15%">Harga Jual(IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td style="width: 20%">{{ $product->code }}</td>
                            <td style="width: 25%">{{ $product->name }}</td>
                            <td style="width: 10%">{{ $product->category }}</td>
                            <td class="text-end" style="width: 10%">{{ $product->stocks_sum_qty ? number_format($product->stocks_sum_qty, 0, '', '.') : '-' }}</td>
                            <td class="text-end" style="width: 15%">{{ number_format($product->unit_price, 0, '', '.')}}</td>
                            <td class="text-end" style="width: 15%">{{ number_format($product->selling_price, 0, '', '.')}} </td>
                        </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>

        
    </div>

    <div class="row justify-content-end mx-2" style="margin-top: 6rem">
        <div class="col-4 text-center p-1" style="border-top: 1px solid black;">
            {{ $author->name }}
        </div>
    </div>
    
    <div class="row justify-content-center mb-3 p-4">
        <div class="col-12">
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

    <script src="{{ asset('js/admin/main.js') }}" defer></script> 
    <script src="{{ asset('js/public.js') }}" defer></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>