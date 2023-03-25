@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Laporan Penjualan Periode : {{$period}}</title>

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
    {{-- Lancscape Invoice --}}
        <div class="d-none" id="size-landscape">
            <div class="m-4 p-4">
                {{-- Header --}}
                <div class="row justify-content-between border-bottom">
                    <div class="col-6">
                        @if ($identity)
                        <h4 class="font-bold">{{ $identity['nama_bumdes'] }}</h4>
                        @endif
                    </div>
                    <div class="col-6">
                        <h5 class="font-bold text-end">Unit Usaha : {{ $business->nama }}</h5>
                    </div>
                </div>

                <div class="row justify-content-center my-2 ">
                    <div class="col-12 text-center">
                        <h4 class="text-gray font-bold">Laporan Penjualan</h4>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12 text-end">
                        <p class="fst-italic">Periode: {{$period}}</p>
                    </div>
                </div>

                <div class="row justify-content-center mt-3" >
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th colspan="5">Ref</th>
                                    <th class="text-end">Nilai (IDR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @if (count($invoices) > 0)
                                    @foreach ($invoices as $invoice)
                                        @php
                                            $total += $invoice->value;
                                        @endphp
                                        <tr>
                                            <td>{{ $carbon::createFromDate($invoice->date)->toFormattedDateString() }}</td>
                                            <td>{{ $invoice->contact_name }}</td>
                                            <td colspan="5">{{ $invoice->no_ref }}</td>
                                            <td class="text-end">{{ number_format($invoice->value, 0, '', '.') }}</td>
                                        </tr>
                                        @foreach ($invoice->products as $product)
                                            <tr>
                                                <th colspan="2"class="text-center align-middle"></th>
                                                <td class="text-end">{{ $loop->iteration }}</td>
                                                <td>{{ $product['name'] }}</td>
                                                <td class="text-end">{{ number_format($product->pivot['value'] / $product->pivot['qty'], 0, '', '.') }}</td>
                                                <td class="text-end">{{ number_format($product->pivot['qty'], 0, '', '.') }}</td>
                                                <td class="text-end">{{ number_format($product->pivot['value'], 0, '', '.') }}</td>
                                                <td class="text-end"></td>
                                            </tr>
                                        @endforeach
                                        
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center fst-italic">Tidak Ada Data</td>
                                    </tr>
                                @endif
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-center">Total</th>
                                    <th class="text-end">{{ number_format($total, 0, '', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {{-- landscape Invoice --}}

    {{-- 58mm invoice --}}
        <div class="row font-monospace justify-content-center" style="width: 75mm;color:black" id="size-58">
            {{-- style="width: 70mm;color:black" --}}
            {{-- Header --}}
            <div class="col-12 text-center" style="font-size: 13px">
                <h4 class="font-bold">{{ $identity['nama_bumdes'] }}</h4>
            </div>
            {{-- Header --}}
            {{-- Sub Header --}}
            <div class="col-12 text-center border-bottom" style="font-size: 13px">
                <h5 class="font-bold">Unit Usaha : {{ $business->nama }}</h5>
            </div>
            <div class="col-12 text-center border-bottom" style="font-size: 13px">
                <h5 class="font-bold">Laporan Penjualan</h5>
            </div>
            <div class="col-12 text-center border-bottom" style="font-size: 13px">
                <h5 class="font-bold">Periode: {{$period}}</h5>
            </div>
            {{-- Sub Header --}}

            {{-- Content --}}
            <div class="col-12">
                <table style="font-size: 13px;margin:5px">
                    <tbody>
                        @php
                            $grandTotal = 0;
                        @endphp
                        @foreach ($invoices as $invoice)
                            @php
                                $grandTotal += $invoice->value;
                            @endphp
                            <tr>
                                <th>Tanggal</th>
                                <td>: {{ $carbon::createFromDate($invoice->date)->toFormattedDateString() }}</td>
                            </tr>
                            <tr>
                                <th>No Ref</th>
                                <td>: {{ $invoice->no_ref }}</td>
                            </tr>
                            <tr>
                                <th>Kepada</th>
                                <td>: {{ $invoice->contact->name }}</td>
                            </tr>
                            <tr style="border-bottom: 2px solid black">
                                <th>Nilai</th>
                                <td class="d-flex">
                                    : {{ number_format($invoice->value, 0, '', '.') }}
                                </td>
                            </tr>
                        @endforeach   
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>: {{ number_format($grandTotal , 0, '', '.')}}</th>
                        </tr> 
                    </tfoot>                                  
                </table>
            </div>

            
        </div>
    {{-- 58mm invoice --}}
    

    <div class="mx-2 my-5 d-print-none d-flex gap-4">
        <button class="btn btn-success " onclick="funcPrint()">print</button>
        <div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" checked onclick="changeSize(0)">
                <label class="form-check-label" for="flexRadioDefault1">
                  58 mm
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" onclick="changeSize(1)">
                <label class="form-check-label" for="flexRadioDefault2">
                  Landscape
                </label>
              </div>
        </div>
    </div>
    
    <script>
        function funcPrint()
        {
            window.print()
        }

        function changeSize(value)
        {
            if (value == 0) {
                document.querySelector('#size-landscape').classList.add('d-none');
                document.querySelector('#size-58').classList.remove('d-none');
            }
            else{
                document.querySelector('#size-landscape').classList.remove('d-none');
                document.querySelector('#size-58').classList.add('d-none');
            }
        }

        window.addEventListener('load', function(){

        })
    </script>

    <script src="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}" defer></script>

    <script src="{{ asset('js/admin/bootstrap.min.js') }}" defer></script>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>