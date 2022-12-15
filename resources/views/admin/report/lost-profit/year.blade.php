@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Laporan Laba Rugi</h3>

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
                        <h5>Pendapatan</h5>
                        <table class="table">
                            <tbody id="revenue">
                                <tr>
                                    
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="w-50">Total Pendapatan</th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="total-revenue-now">
                                                
                                            </div>
                                        </div>  
                                    </th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="total-revenue-before">
                                                
                                            </div>
                                        </div>      
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-8">
                        <h5>Beban</h5>
                        <table class="table">
                            <tbody id="cost">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="w-50">Total Biaya</th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="total-cost-now">
                                                
                                            </div>
                                        </div>
                                    </th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="total-cost-before">
                                                
                                            </div>
                                        </div>      
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-8">
                        <table class="table">
                            <tfoot>
                                <tr>
                                    <th class="w-50">Laba (Rugi) Kotor</th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="gross-lost-profit-now">
                                                
                                            </div>
                                        </div> 
                                    </th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="gross-lost-profit-before">
                                                
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
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="lost-profit-before-tax-now">
                                                
                                            </div>
                                        </div> 
                                    </th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="lost-profit-before-tax-before">
                                                
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
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="lost-profit-after-tax-now">
                                                
                                            </div>
                                        </div>    
                                    </th>
                                    <th class="w-25">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Rp. 
                                            </div>
                                            <div class="text-end" id="lost-profit-after-tax-before">
                                                
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
    </div>
    
@endsection

@section('script')
    <script src="/js/admin/report/lost-profit-year.js"></script>
    <script src="/js/admin/report/api.js"></script>
@endsection
