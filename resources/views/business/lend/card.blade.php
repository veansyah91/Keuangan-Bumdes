@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Kartu Pemberian Pinjaman</title>

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
        <div id="size-landscape">
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
                        <h4 class="text-gray font-bold">Kartu Pinjaman</h4>
                    </div>
                </div>

                <div style="font-size:12pt;">
                    <div class="row justify-content-between">
                        <div class="col-12 col-md-4">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%">No Ref</th>
                                        <td style="width: 60%" id="no-ref-detail">:{{ $accountReceivable->no_ref }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 40%">Tanggal Pijaman</th>
                                        <td style="width: 60%" id="date-detail">:{{  $carbon::createFromDate($accountReceivable->date)->toFormattedDateString() }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 40%">Tempo</th>
                                        <td style="width: 60%" id="due-date-detail">:{{  $carbon::createFromDate($accountReceivable->due_date)->toFormattedDateString() }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 40%">Nilai Pinjaman (IDR)</th>
                                        <td style="width: 60%" class="my-auto" id="value-detail">:{{ number_format($accountReceivable->debit, 0, '', '.')}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Nama</th>
                                        <td id="contact-name-detail">:{{ $accountReceivable->contact_name }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Alamat</th>
                                        <td id="contact-name-detail" class="d-flex">
                                            <div>
                                                :
                                            </div>
                                            <div>
                                                 {{ $accountReceivable->contact->detail->village }}, {{ $accountReceivable->contact->detail->district }}, {{ $accountReceivable->contact->detail->regency }}, {{ $accountReceivable->contact->detail->province }}
                                            </div>  
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Telepon / HP</th>
                                        <td id="contact-phone-detail">:{{ $accountReceivable->contact->phone }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>                     
            
                    <table class="table mt-5">
                        <thead style="border: solid 0 0 black">
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-end">Jumlah Bayar (IDR)</th>
                                <th class="text-end">Sisa (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($payments) > 0)
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($payments as $payment)
                                @php
                                    $total += $payment->credit
                                @endphp
                                    <tr>
                                        <td  class="text-center">{{ $carbon::createFromDate($payment->date)->toFormattedDateString() }}</td>
                                        <td  class="text-end">{{ number_format($payment->credit, 0, '', '.')}}</td>
                                        <td class="text-end">{{ number_format($accountReceivable->debit - $total, 0, '', '.') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center fst-italic" colspan="3">Belum Dilakukan Pembayaran</td>
                                </tr>
                            @endif
                            
                        </tbody>
                    </table>
                </div>

                
            </div>

            <div class="row justify-content-between mx-5" style="margin-top: 6rem">
                <div class="col-4 text-center" style="border-top: 1px solid black;">
                    {{ $accountReceivable->contact_name }}
                </div>
                <div class="col-4 text-center" style="border-top: 1px solid black;">
                    {{ $accountReceivable->author }}
                </div>
            </div>
        </div>
    {{-- landscape Invoice --}}
    

    <div class="mx-2 my-5 d-print-none d-flex gap-4">
        <button class="btn btn-success " onclick="funcPrint()">print</button>
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