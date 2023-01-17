@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Faktur Penjualan </title>

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
                        <h4 class="text-gray font-bold">Faktur Penjualan</h4>
                    </div>
                </div>

                <div style="font-size:12pt;">
                    <div class="row justify-content-between">
                        <div class="col-12 col-md-4">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">No Ref</th>
                                        <td style="width: 70%" id="no-ref-detail">: {{ $invoice->no_ref }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Tanggal</th>
                                        <td style="width: 70%" id="date-detail">: {{  $carbon::createFromDate($invoice->date)->toFormattedDateString() }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Kasir</th>
                                        <td style="width: 70%" id="author-detail">: {{ $invoice->author }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Kepada</th>
                                        <td id="contact-name-detail">{{ $invoice->contact->name }}</td>
                                    </tr>
                                    @if ($invoice->contact->address)
                                        <tr>
                                            <th style="width: 30%"></th>
                                            <td id="contact-address-detail">{{ $invoice->contact->address }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>                     
            
                    <table class="table mt-5">
                        <thead style="border: solid 0 0 black">
                            <tr>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Harga (IDR)</th>
                                <th class="text-end">Total (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalQty = 0;
                                $grandTotal = 0;
                            @endphp
                            @foreach ($invoice->products as $ledger)
                                @php
                                    $totalQty += $ledger->pivot['qty'];
                                    $grandTotal += $ledger->pivot['value'];
                                @endphp
                                <tr>
                                    <td>{{ $ledger['code'] }}</td>
                                    <td>{{ $ledger['name'] }}</td>
                                    <td class="text-end">{{ number_format($ledger->pivot['qty'], 0, '', '.')}}</td>
                                    <td class="text-end">Rp.{{ number_format($ledger->pivot['value'] / $ledger->pivot['qty'], 0, '', '.')}}</td>
                                    <td class="text-end">Rp.{{ number_format($ledger->pivot['value'], 0, '', '.')}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th class="text-end">{{ number_format($totalQty, 0, '', '.')}}</th>
                                <th class="text-end" colspan="2">Rp.{{ number_format($grandTotal, 0, '', '.')}}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                
            </div>

            <div class="row justify-content-end mx-5" style="margin-top: 6rem">
                <div class="col-4 text-center" style="border-top: 1px solid black;">
                    {{ $invoice->author }}
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
            {{-- Sub Header --}}

            {{-- Content --}}
            <div class="col-12">
                <table style="font-size: 13px;margin:5px">
                    <tr>
                        <th>No Ref</th>
                        <td>: {{ $invoice->no_ref }}</td>
                    </tr>
                    <tr>
                        <th>Kepada</th>
                        <td>: {{ $invoice->contact->name }}</td>
                    </tr>
                    <tr>
                        <th>Kasir</th>
                        <td>: {{ $invoice->author }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>: {{ $carbon::createFromDate($invoice->date)->toFormattedDateString() }}</td>
                    </tr>
                </table>
            </div>
            {{-- Content --}}

            {{-- List Product --}}
            <div class="col-12" style="border-top: 5px solid black;border-bottom: 5px solid black;">
                @php
                    $totalQty = 0;
                    $grandTotal = 0;
                @endphp
                @foreach ($invoice->products as $product)
                    @php
                        $totalQty += $product->pivot['qty'];
                        $grandTotal += $product->pivot['value'];
                    @endphp
                    <div class="row">
                        <div class="col-12" style="margin:5px">
                            {{ $product['code'] }} - {{ $product['name'] }}
                        </div>
                        <div class="col-12" style="margin:5px">
                            <div class="row">
                                <div class="col-4 text-end">
                                    {{ $product->pivot['qty'] }}
                                </div>
                                <div class="col-4 text-end">
                                    {{ number_format($product->pivot['value'] / $product->pivot['qty'], 0, '', '.')}}
                                </div>
                                <div class="col-4 text-end">
                                    {{ number_format($product->pivot['value'], 0, '', '.')}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- List Product --}}
            <div class="col-12">
                <div class="row">
                    <div class="col-8 text-end fw-bold">
                        Qty
                    </div>
                    <div class="col-4 text-end">
                        {{ number_format($totalQty , 0, '', '.')}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-8 text-end fw-bold">
                        Grand Total
                    </div>
                    <div class="col-4 text-end">
                        {{ number_format($grandTotal , 0, '', '.')}}
                    </div>
                </div>
            </div>
            {{-- Total --}}
            {{-- Footer --}}
            <div class="col-12 my-5">
                <div class="row">
                    <div class="col-12 text-center fst-italic">
                        Terima Kasih
                    </div>
                </div>
            </div>
            {{-- Total --}}
                
            {{--  --}}

            
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