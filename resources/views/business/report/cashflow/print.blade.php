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
            <div class="col-12 text-end">
                <h4 class="text-gray font-bold">Unit Usaha : {{ $business->nama }}</h4>
            </div>
        </div>
        <div class="row justify-content-center" data-business="{{ $business->id }}" id="content">
            <div class="col-12 text-center">
                <h5 class="text-black" style="font-size: 16pt">Laporan Arus Kas</h5>
            </div>
        </div>

        <div style="font-size:12pt;" class="mt-3">
            <div class="row justify-content-center">
                <div class="col-8 text-end" style="font-size: 12pt">
                    <p class="fst-italic">Periode: <span id="period"></span></p>
                </div>
            </div>

            <div class="row mt-3 justify-content-center">
                <div class="col-8">
                    <h5 style="font-size: 14pt">Aktifitas Operasional</h5>
                    <table class="table" style="font-size: 12pt">
                        <tbody id="operational-activity">
                            <tr>
                                
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-50">Total Kas Operasional</th>
                                <td class="w-25"></td>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-operational-activity">
                                            
                                        </div>
                                    </div>      
                                </th>
                                
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
            </div>

            <div class="row justify-content-center">
                <div class="col-8">
                    <h5>Aktifitas Investasi</h5>
                    <table class="table">
                        <tbody id="investment-activity">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-50">Total Kas Investasi</th>
                                <td class="w-25"></td>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-investment-activity">
                                            
                                        </div>
                                    </div>      
                                </th>
                                
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-8">
                    <h5>Aktifitas Pendanaan</h5>
                    <table class="table">
                        <tbody id="finance-activity">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-50">Total Kas Pendanaan</th>
                                <td class="w-25"></td>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-finance-activity">
                                            
                                        </div>
                                    </div>  
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
            </div>

            <div class="row justify-content-center">
                <div class="col-8">
                    <table class="table">
                        <tr>
                            <th class="w-50">Kenaikan (Penurunan) Kas</th>
                            <th class="w-25"></th>
                            <th class="w-25">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        Rp. 
                                    </div>
                                    <div class="text-end" id="increase-cash">
                                        
                                    </div>
                                </div>  
                            </th>
                        </tr>
                        <tr>
                            <th class="w-50">Kas Awal</th>
                            <th class="w-25"></th>
                            <th class="w-25">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        Rp. 
                                    </div>
                                    <div class="text-end" id="start-cash">
                                        
                                    </div>
                                </div>  
                            </th>
                        </tr>
                        <tr>
                            <th class="w-50">Kas Akhir</th>
                            <th class="w-25"></th>
                            <th class="w-25">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        Rp. 
                                    </div>
                                    <div class="text-end" id="end-cash">
                                        
                                    </div>
                                </div>  
                            </th>
                        </tr>
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

    <script src="/js/business/report/cashflow-print.js"></script>
    <script src="/js/business/report/api.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>