@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Journal</title>

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
                <h4 class="text-gray">Laporan Jurnal</h4>
            </div>
        </div>

        <div style="font-size:12pt;">
    
            <table class="mt-5 table table-bordered border-dark">
                <thead style="border: solid 0 0 black">
                    <tr>
                        <th style="width: 20%">Tanggal/No. Referensi</th>
                        <th style="width: 20%">Deskripsi</th>
                        <th class="text-end" style="width: 20%">Debit (IDR)</th>
                        <th class="text-end" style="width: 20%">Kredit (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_debit = 0;
                        $total_credit = 0;
                    @endphp
                    @foreach ($journals as $journal)
                        <tr>
                            <td>{{ $carbon::parse($journal->date)->isoformat('MMM, D Y') }}</td>
                            <td colspan="3">{{ $journal->desc }} - {{ $journal->detail }}</td>
                        </tr>
                        @foreach (LedgerHelper::index($journal->no_ref) as $ledger)
                            @php
                                $total_debit += $ledger->debit;
                                $total_credit += $ledger->credit;
                            @endphp
                            <tr>
                                <td class="text-end">{{ $ledger->no_ref }}</td>
                                <td>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            {{ $ledger->account_name }}
                                        </div>
                                        <div>
                                            {{ $ledger->account_code }}
                                        </div>
                                    </div>
                                    
                                </td>
                                <td class="text-end">{{ number_format((int)$ledger->debit, 0, '', '.') }}</td>
                                <td class="text-end">{{ number_format((int)$ledger->credit, 0, '', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <th class="text-center" colspan="2">Total</th>
                        <th class="text-end">{{ number_format((int)$total_debit, 0, '', '.')}}</th>
                        <th class="text-end">{{ number_format((int)$total_credit, 0, '', '.')}}</th>
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
        <div class="col-8">
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
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>