@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Stok</title>

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
            font-size: 8pt
        }
    </style>
</head>
<body>
    <div class="p-4">
        <div class="row justify-content-between mb-3" id="content" data-business="{{ $business->id }}">
            <div class="col-6 text-start">
                <h4 class="text-gray font-bold">Unit Usaha : {{ $business->nama }}</h4>
            </div>
            <div class="col-6 text-end d-print-none d-flex gap-2 mr-auto">
                <select class="form-select w-50" aria-label="Default select example" id="select-period-input" onchange="changePeriodInput(this)">
                    <option value="today" @if ($request->date_from === $request->date_to) selected @endif>Hari Ini</option>
                    <option value="this_week" @if ($request->this_week) selected @endif>Minggu Ini</option>
                    <option value="this_month" @if ($request->this_month) selected @endif>Bulan Ini</option>
                    <option value="this_year" @if ($request->this_year) selected @endif>Tahun Ini </option>
                    <option value="custom" @if ($request->date_from !== $request->date_to) selected @endif>Custom</option>
                </select>
                <div class="d-flex gap-2 @if ($request->date_from !== $request->date_to) d-block @else d-none @endif" id="date-between">
                    <div class="d-flex gap-2">
                        <label for="start-filter" class="col-form-label">Dari</label>
                        <input type="date" class="form-control" id="start-filter" placeholder="Tanggal" onchange="changeStartFilter(this)" value="{{ $request->date_from }}">
                    </div>
                    <div class="d-flex gap-2">
                        <label for="end-filter" class="col-form-label">Ke</label>
                        <input type="date" class="form-control" id="end-filter" placeholder="Tanggal" onchange="changeEndFilter(this)" value="{{ $request->date_to }}">
                    </div>
                </div>
                <a href="{{ url('/' . $business->id . '/product/print-stock?this_month=1') }}" class="btn btn-primary" id="reload-page">Pilih</a>
            </div>
        </div>
        
        <div class="row justify-content-center mb-3">
            <div class="col-12 text-center">
                <h3 class="text-gray">Laporan Kartu Stok</h3>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 text-end">
                <p class="fst-italic">Periode: <span id="period">{{ $period }}</span></p>
            </div>
        </div>

        @foreach ($products as $product)
            <div style="font-size:10pt;" class="mt-3">
                <div class="row justify-content-between">
                    <div class="col-6">
                        ({{ $product->code }}) {{ $product->name }}
                    </div>
                    <div class="col-6 text-end">
                        {{ $product->category }}
                    </div>
                </div>
                
                <table class="table table-bordered">
                    <thead style="border: solid 0 0 black">
                        <tr>
                            <th rowspan="2">Tanggal</th>
                            <th rowspan="2">No Ref</th>
                            <th rowspan="2">Kontak</th>
                            <th rowspan="2">Satuan</th>
                            <th class="text-center" colspan="3">Masuk</th>
                            <th class="text-center" colspan="3">Keluar</th>
                            <th class="text-center" colspan="3">Saldo</th>
                        </tr>
                        <tr>
                            <th class="text-end">Qty</th>
                            <th class="text-end">HPP(IDR)</th>
                            <th class="text-end">Total(IDR)</th>

                            <th class="text-end">Qty</th>
                            <th class="text-end">HPP(IDR)</th>
                            <th class="text-end">Total(IDR)</th>

                            <th class="text-end">Qty</th>
                            <th class="text-end">HPP(IDR)</th>
                            <th class="text-end">Total(IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCurrent = $product->stocks->sum('debit') - $product->stocks->sum('credit');
                            $qtyCurrent = $product->stocks->sum('qty');

                            $totalQty = $product->getTotal()['total_qty'] - $qtyCurrent;
                            $grandTotal = $product->getTotal()['total'] - $totalCurrent;
                            $totalCogs = $totalQty > 0 ? $grandTotal / $totalQty : 0;

                            $totalDebitQty = 0;
                            $totalDebitCogs = 0;
                            $totalDebitTotal = 0;

                            $totalCreditQty = 0;
                            $totalCreditCogs = 0;
                            $totalCreditTotal = 0;
                        @endphp
                        <tr>
                            <th colspan="10">Saldo Awal</th>

                            <th class="text-end">{{ number_format($totalQty, 0, '', '.') }}</th>
                            <th class="text-end">{{ number_format($totalCogs, 0, '', '.') }}</th>
                            <th class="text-end">{{ number_format($grandTotal, 0, '', '.') }}</th>
                        </tr>
                        @foreach ($product->stocks as $stock)
                            @php
                                
                                $totalQty += $stock->qty ;
                                $grandTotal += $stock->debit - $stock->credit;
                                $totalCogs = $totalQty > 0 ? $grandTotal /  $totalQty : 0;
                                
                                $totalDebitQty += $stock->debit > 0 ? $stock->qty : 0;
                                $totalDebitTotal += $stock->debit > 0 ? $stock->debit : 0;

                                $totalCreditQty += $stock->credit > 0 ? -$stock->qty : 0;
                                $totalCreditTotal += $stock->credit > 0 ? $stock->credit : 0;
                                
                            @endphp
                            <tr>
                                <td>{{ $carbon::createFromDate($stock->date)->toFormattedDateString() }}</td>
                                <td>{{ $stock->no_ref }}</td>
                                <td>{{ $stock->contact }}</td>
                                <td>{{ $stock->unit }}</td>
                                <td class="text-end">{{ $stock->debit > 0 ? $stock->qty : 0 }}</td>
                                <td class="text-end">{{ $stock->debit > 0 ? number_format($stock->debit / $stock->qty, 0, '', '.') : 0 }}</td>
                                <td class="text-end">{{ $stock->debit > 0 ? number_format($stock->debit, 0, '', '.') : 0 }}</td>

                                <td class="text-end">{{ $stock->credit > 0 ? -$stock->qty : 0 }}</td>
                                <td class="text-end">{{ $stock->credit > 0 ? number_format($stock->credit / -$stock->qty, 0, '', '.') : 0 }}</td>
                                <td class="text-end">{{ $stock->credit > 0 ? number_format($stock->credit, 0, '', '.') : 0 }}</td>

                                <td class="text-end">{{ number_format($totalQty, 0, '', '.') }}</td>
                                <td class="text-end">{{ number_format($totalCogs, 0, '', '.') }}</td>
                                <td class="text-end">{{ number_format($grandTotal, 0, '', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total {{ $product->name }}</th>
                            <th class="text-end">{{ number_format($totalDebitQty, 0, '', '.') }}</th>
                            <th class="text-end">{{ $totalDebitQty > 0 ? number_format($totalDebitTotal / $totalDebitQty, 0, '', '.') : 0 }}</th>
                            <th class="text-end">{{ number_format($totalDebitTotal, 0, '', '.') }}</th>

                            <th class="text-end">{{ number_format($totalCreditQty, 0, '', '.') }}</th>
                            <th class="text-end">{{ $totalCreditQty > 0 ? number_format($totalCreditTotal / $totalCreditQty, 0, '', '.') : 0 }}</th>
                            <th class="text-end">{{ number_format($totalCreditTotal, 0, '', '.') }}</th>

                            <th class="text-end">{{ number_format($totalQty, 0, '', '.') }}</th>
                            <th class="text-end">{{ number_format($totalCogs, 0, '', '.') }}</th>
                            <th class="text-end">{{ number_format($grandTotal, 0, '', '.') }}</th>                           
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
        
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
    <script src="{{ asset('js/public.js') }}" defer></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const business = document.querySelector('#content').dataset.business;
        const selectPeriodInput = document.querySelector('#select-period-input');

        function changePeriodInput(value) {
            let variable = `${value.value}=1`;

            if (value.value == 'today') {
                variable = `date_from=${dateNow()}&date_to=${dateNow()}`;
            }

            document.querySelector('#reload-page').setAttribute('href', `/${business}/product/print-stock?${variable}`);

            if (value.value == 'custom') {
                document.querySelector('#reload-page').classList.add('d-none');
                document.querySelector('#date-between').classList.remove('d-none');
            } else {
                document.querySelector('#reload-page').classList.remove('d-none');
                document.querySelector('#date-between').classList.add('d-none');
            }
        }

        function changeStartFilter(value){
            const endFilter = document.querySelector('#end-filter');

            const reloadPage = document.querySelector('#reload-page');
            if (endFilter) {
                variable = `date_from=${value.value}&date_to=${endFilter.value}`;

                reloadPage.classList.remove('d-none');
                reloadPage.setAttribute('href', `/${business}/product/print-stock?${variable}`);
            }
        }

        function changeEndFilter(value){
            const startFilter = document.querySelector('#start-filter');

            const reloadPage = document.querySelector('#reload-page');
            if (startFilter) {
                variable = `date_from=${startFilter.value}&date_to=${value.value}`;

                reloadPage.classList.remove('d-none');
                reloadPage.setAttribute('href', `/${business}/product/print-stock?${variable}`);
            }
        }

        window.addEventListener('load', function(){

        })
    </script>
</body>
</html>