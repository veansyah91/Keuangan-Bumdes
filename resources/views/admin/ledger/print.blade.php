@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Buku Besar</title>

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
            <div class="col-12 text-center">
                <h3 class="text-gray">Laporan Buku Besar</h3>
            </div>
        </div>

        <div style="font-size:12pt;" class="mt-5">
            <div class="row justify-content-between">
                <div class="col-6">
                    <h5>Akun: {{ $account->name }}</h5>
                </div>
                <div class="col-6 text-end">
                    <p class="fst-italic">Periode: {{ $period }}</p>
                </div>
            </div>
            
    
            <table class="table">
                <thead style="border: solid 0 0 black">
                    <tr>
                        <th style="width: 10%">Tanggal</th>
                        <th style="width: 15%">No. Referensi</th>
                        <th style="width: 20%">Deskripsi</th>
                        <th class="text-end" style="width: 15%">Debit (IDR)</th>
                        <th class="text-end" style="width: 15%">Kredit (IDR)</th>
                        <th class="text-end" style="width: 15%">Saldo (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $balance = $amountLedger - ($total_debit - $total_credit);
                    @endphp
                    <tr>
                        <td colspan="2"></td>
                        <td class="font-bold">Saldo Awal</td>
                        <td colspan="3" class="text-end font-bold">{{ number_format($balance, 0, '', '.')}}</td>
                    </tr>
                    
                    @foreach ($ledgers as $ledger)
                        <tr>
                            <td style="width: 15%">{{ $carbon::parse($ledger->date)->isoformat('MMM, D Y') }}</td>
                            <td style="width: 10%">{{ $ledger->no_ref }}</td>
                            <td style="width: 20%">{{ $ledger->description }}</td>
                            <td class="text-end" style="width: 15%">{{ number_format($ledger->debit, 0, '', '.')}}</td>
                            <td class="text-end" style="width: 15%">{{ number_format($ledger->credit, 0, '', '.')}}</td>
                            @php
                                 $balance += $ledger->debit - $ledger->credit;
                            @endphp
                            <td class="text-end" style="width: 15%">{{ number_format($balance, 0, '', '.')}}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="2"></td>
                        <td class="font-bold">Saldo Akhir</td>
                        <td colspan="3" class="text-end font-bold">{{ number_format($balance, 0, '', '.')}}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        
    </div>

    <div class="row justify-content-end mx-2" style="margin-top: 6rem">
        <div class="col-4 text-center p-1" style="border-top: 1px solid black;">
            {{ $author->name }}
        </div>
    </div>
    
    <div class="row justify-content-center mb-3">
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