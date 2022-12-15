@inject('carbon', 'Carbon\Carbon')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Neraca</title>

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
</head>
<body>
    <div class="p-4">
        <div class="row justify-content-center mb-3">
            <div class="col-8 text-center">
                <h1 class="text-gray">Laporan Neraca Saldo</h1>
            </div>
        </div>

        <div style="font-size:12pt;" class="mt-5">
            <div class="row justify-content-center">
                <div class="col-8 text-end">
                    <p class="fst-italic">Periode: <span id="period"></span></p>
                </div>
            </div>

            <div class="row mt-3 justify-content-center">
                <div class="col-8">
                    <h5>Asset</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    Aset Lancar
                                </th>
                            </tr>
                        </thead>
                        <tbody id="current-asset">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Aset Lancar</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-current-asset">
                                            
                                        </div>
                                    </div>      
                                </th>
                                <td class="w-25" style="width: 25%"></td>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    Aset Tetap
                                </th>
                            </tr>
                        </thead>
                        <tbody id="non-current-asset">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Aset Tetap</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-non-current-asset">
                                            
                                        </div>
                                    </div>      
                                </th>
                                <th class="w-25" style="width: 25%"></th>
                            </tr>
                        </tfoot>
                    </table>


                    <table class="table">
                        <thead>
                            <tr class="text-primary">
                                <th class="w-40" style="width: 40%">Total Aset</th>
                                <th class="w-25" style="width: 25%"></th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-asset">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
            </div>

            <div class="row mt-5 justify-content-center">
                <div class="col-8">
                    <h5>Kewajiban</h5>
                    <table class="table">
                        <tbody id="liability">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Kewajiban</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-liability">
                                            
                                        </div>
                                    </div>      
                                </th>
                                <th class="w-25" style="width: 25%"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-8">
                    <h5>Modal</h5>
                    <table class="table">
                        <tbody id="equity">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-40" style="width: 40%">Total Modal</th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-equity">
                                            
                                        </div>
                                    </div>  
                                </th>
                            </tr>
                            <th class="w-25"></th>
                        </tfoot>
                        
                    </table>

                    <table class="table">
                        <thead>
                            <tr class="text-primary">
                                <th class="w-40" style="width: 40%">Total Kewajiban dan Modal</th>
                                <th class="w-25" style="width: 25%"></th>
                                <th class="w-25" style="width: 25%">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-liability-equity">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
            </div>

        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-8">
            <div class="row justify-content-end mx-2" style="margin-top: 6rem">
                <div class="col-4 text-center p-1" style="border-top: 1px solid black;">
                    {{ $author->name }}
                </div>
            </div>
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

    <script src="/js/admin/report/balance-print.js"></script>
    <script src="/js/admin/report/api.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>