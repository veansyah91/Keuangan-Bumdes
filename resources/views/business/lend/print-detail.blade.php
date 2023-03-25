@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Pemberian Pinjaman</title>

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
                        <h4 class="text-gray font-bold">Faktur Setoran Tunai</h4>
                    </div>
                </div>

                <div style="font-size:12pt;">
                    <div class="row justify-content-between">
                        <div class="col-12 col-md-4">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">No Ref</th>
                                        <td style="width: 70%" id="no-ref-detail">: {{ $deposit->no_ref }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Tanggal</th>
                                        <td style="width: 70%" id="date-detail">: {{  $carbon::createFromDate($deposit->date)->toFormattedDateString() }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Kasir</th>
                                        <td style="width: 70%" id="author-detail">: {{ $deposit->author }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Nama</th>
                                        <td id="contact-name-detail">: {{ $deposit->contact_name }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Nomor Rekening</th>
                                        <td id="contact-name-detail">: {{ $deposit->savingAccount->no_ref }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>                     
            
                    <table class="table mt-5">
                        <thead style="border: solid 0 0 black">
                            <tr>
                                <th>Jumlah (IDR)</th>
                                <th>Terbilang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($deposit->value, 0, '', '.')}}</td>
                                <td>{{ $deposit->terbilang }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                
            </div>

            <div class="row justify-content-between mx-5" style="margin-top: 6rem">
                <div class="col-4 text-center" style="border-top: 1px solid black;">
                    {{ $deposit->contact_name }}
                </div>
                <div class="col-4 text-center" style="border-top: 1px solid black;">
                    {{ $deposit->author }}
                </div>
            </div>
        </div>
    {{-- landscape Invoice --}}

    {{-- 58mm deposit --}}
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
                        <td class="d-flex">
                            <div>: </div>
                            <div>{{ $deposit->no_ref }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td class="d-flex">
                            <div>: </div>
                            <div>{{ $deposit->contact_name }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>Nomor Rekening</th>
                        <td class="d-flex">
                            <div>: </div>
                            <div>{{ $deposit->savingAccount->no_ref }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>Kasir</th>
                        <td class="d-flex">
                            <div>: </div>
                            <div>{{ $deposit->author }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td class="d-flex">
                            <div>: </div>
                            <div>{{ $carbon::createFromDate($deposit->date)->toFormattedDateString() }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            {{-- Content --}}

            {{-- List Product --}}
            <div class="col-12" style="border-top: 5px solid black;border-bottom: 5px solid black;">
                <div class="row">
                    <div class="col-12 text-center fw-bold" style="margin:5px;font-size: 30px">
                        Rp.{{ number_format($deposit->value, 0, '', '.')}}
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-12 text-center" style="margin:5px">
                        {{ $deposit->terbilang }}
                    </div>                    
                </div>
            </div>
            {{-- Total --}}
            {{-- Footer --}}
            <div class="col-12">
                <div class="row  my-5">
                    <div class="col-12 text-center fst-italic">
                       Mohon Simpan Bukti Transaksi Setoran Tunai
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center fst-italic">
                        Terima Kasih
                    </div>
                </div>
            </div>
            {{-- Total --}}
                
            {{--  --}}

            
        </div>
    {{-- 58mm deposit --}}
    

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