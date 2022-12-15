@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Laporan Arus Kas Tahunan</h3>

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

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8 text-end fst-italic">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="w-50"></th>
                                    <th class="text-end w-25 font-bold" id="period"></th>
                                    <th class="text-end w-25 font-bold" id="period-before"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-8">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="3"><h5>Aktifitas Operasional</h5></th>
                                </tr>
                            </thead>
                            <tbody id="operational-activity">
                                
                            </tbody>
                            <tfoot id="total-operational-activity">
                                
                            </tfoot>
                        </table>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-8">
                        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="3"><h5>Aktifitas Investasi</h5></th>
                                </tr>
                            </thead>
                            <tbody id="investment-activity">
                                
                                
                            </tbody>
                            <tfoot id="total-investment-activity">
                                <tr>
                                    <th class="w-50">Total Kas Investasi</th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" >
                                                
                                            </div>
                                        </div>      
                                    </th>
                                    <td class="w-25"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-8">
                        
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="3"><h5>Aktifitas Pendanaan</h5></th>
                                </tr>
                            </thead>
                            <tbody id="finance-activity">
                                
                            </tbody>
                            <tfoot id="total-finance-activity">
                                
                            </tfoot>
                        </table>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-8">
                        <table class="table">
                            <tr>
                                <th class="w-50">Kenaikan (Penurunan) Kas</th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="increase-cash-now">
                                            
                                        </div>
                                    </div>  
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="increase-cash-before">
                                            
                                        </div>
                                    </div>  
                                </th>
                            </tr>
                            <tr>
                                <th class="w-50">Kas Awal</th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="start-cash-now">
                                            
                                        </div>
                                    </div>  
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="start-cash-before">
                                            
                                        </div>
                                    </div> 
                                </th>

                            </tr>
                            <tr>
                                <th class="w-50">Kas Akhir</th>
                                
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="end-cash-now">
                                            
                                        </div>
                                    </div>  
                                </th>
                                <th class="w-25">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            Rp. 
                                        </div>
                                        <div class="text-end" id="end-cash-before">
                                            
                                        </div>
                                    </div> 
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('script')
    <script src="/js/admin/report/cashflow-year.js"></script>
    <script src="/js/admin/report/api.js"></script>
@endsection
