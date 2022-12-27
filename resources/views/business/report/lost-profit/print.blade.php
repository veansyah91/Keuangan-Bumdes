@inject('carbon', 'Carbon\Carbon')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Laba Rugi</title>

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
                <h1 class="text-gray">Laporan Laba Rugi</h1>
            </div>
        </div>

        <div style="font-size:12pt;" class="mt-3">
            <div class="row justify-content-center">
                <div class="col-8 text-end">
                    <p class="fst-italic">Periode: <span id="period"></span></p>
                </div>
            </div>

            <div class="row justify-content-center mt-3">
                <div class="col-8">
                    <h5>Pendapatan</h5>
                    <table class="table">
                        <tbody id="revenue">
                            <tr>
                                
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-50">Total Pendapatan</th>
                                <td class="w-25"></td>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-revenue">
                                            
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
                    <h5>Beban</h5>
                    <table class="table">
                        <tbody id="cost">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="w-50">Total Biaya</th>
                                <td class="w-25"></td>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="total-cost">
                                            
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
                        <tfoot>
                            <tr>
                                <th class="w-50">Laba (Rugi) Kotor</th>
                                <td class="w-25"></td>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="gross-lost-profit">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table class="table">
                        <tfoot>
                            <tr>
                                <th class="w-50">Laba (Rugi) Bersih Sebelum Pajak</th>
                                <td class="w-25"></td>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="lost-profit-before-tax">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table class="table">
                        <tfoot>
                            <tr>
                                <th class="w-50">Laba (Rugi) Bersih Setelah Pajak</th>
                                <td class="w-25"></td>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="lost-profit-after-tax">
                                            
                                        </div>
                                    </div>      
                                </th>
                            </tr>
                        </tfoot>
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

    <script src="/js/business/report/lost-profit-print.js"></script>
    <script src="/js/business/report/api.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>