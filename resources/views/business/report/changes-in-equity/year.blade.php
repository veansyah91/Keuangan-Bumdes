@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Laporan Neraca Saldo Tahunan</h3>

        <div class="d-flex">
            <div class="mx-1">
                <select class="form-select" onchange="handleSelectYear(this)" id="select-year" aria-label="Default select example">
                    
                </select>
            </div>
            <div class="mx-1">
                <button class="btn btn-outline-secondary" id="print-button" onclick="goToPrintCashflow()"><i class="bi bi-printer"></i></button>
            </div>
            
        </div>
    </div>

    <div class="row justify-content-center mb-2 mt-2" data-business="{{ $business->id }}" id="content">
        <div class="page-content" >
            <div class="card">
                <div style="height: 500px;" class="overflow-auto custom-scroll card-body">
                    <div class="row">
                        <div class="col-8 text-end fst-italic">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="w-40" style="width: 40%"></th>
                                        <th class="text-end w-25 font-bold" id="period" style="width: 25%"></th>
                                        <th class="text-end w-25 font-bold" id="period-before" style="width: 25%"></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="row">
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
                                                <div class="text-end" id="total-current-asset-now">
                                                    
                                                </div>
                                            </div>      
                                        </th>
                                        <td class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-current-asset-before">
                                                    
                                                </div>
                                            </div>   
                                        </td>
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
                                                <div class="text-end" id="total-non-current-asset-now">
                                                    
                                                </div>
                                            </div>      
                                        </th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-non-current-asset-before">
                                                    
                                                </div>
                                            </div> 
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>


                            <table class="table">
                                <thead>
                                    <tr class="text-primary">
                                        <th class="w-40" style="width: 40%">Total Aset</th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-asset-now">
                                                    
                                                </div>
                                            </div>  </th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-asset-before">
                                                    
                                                </div>
                                            </div>      
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        
                    </div>

                    <div class="row mt-5">
                        <div class="col-8">
                            <h5>Kewajiban</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Utang Jangka Pendek</th>
                                    </tr>
                                </thead>
                                <tbody id="short-term-liability">
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="w-40" style="width: 40%">Total Kewajiban Jangka Pendek</th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-short-term-liability-now">
                                                    
                                                </div>
                                            </div>      
                                        </th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-short-term-liability-before">
                                                    
                                                </div>
                                            </div>   
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Utang Jangka Panjang</th>
                                    </tr>
                                </thead>
                                <tbody id="long-term-liability">
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="w-40" style="width: 40%">Total Kewajiban Jangka Panjang</th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-long-term-liability-now">
                                                    
                                                </div>
                                            </div>      
                                        </th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-long-term-liability-before">
                                                    
                                                </div>
                                            </div>     
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="w-40" style="width: 40%">Total Kewajiban</th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-liability-now">
                                                    
                                                </div>
                                            </div>  </th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-liability-before">
                                                    
                                                </div>
                                            </div>      
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="row">
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
                                                <div class="text-end" id="total-equity-now">
                                                    
                                                </div>
                                            </div>  
                                        </th>
                                        <th class="w-25">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-equity-before">
                                                    
                                                </div>
                                            </div>  
                                        </th>
                                    </tr>
                                
                                </tfoot>
                                
                            </table>

                            <table class="table">
                                <thead>
                                    <tr class="text-primary">
                                        <th class="w-40" style="width: 40%">Total Kewajiban dan Modal</th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-liability-equity-now">
                                                    
                                                </div>
                                            </div>  
                                        </th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-liability-equity-before">
                                                    
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
        </div>
    </div>
    
@endsection

@section('script')
    <script src="/js/business/report/balance-year.js"></script>
    <script src="/js/business/report/api.js"></script>
@endsection
