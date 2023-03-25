@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Laporan Perubahan Modal</h3>

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

    <div class="row justify-content-center mb-2 mt-2" id="content">
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
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <h5>Penyertaan Modal Desa</h5>
                            <table class="table">
                                <tbody id="equity">
                                    
                                </tbody>
                                <tfoot id="total-current-asset-now">
                                    
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <h5>Laba Ditahan</h5>
                            <table class="table">
                                <tbody id="lost-profit">
                                    <tr>
                                        <td class="w-40" style="width: 40%">Laba Ditahan Awal</td>
                                        <td class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end">
                                                    200.000
                                                </div>
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-40" style="width: 40%">Laba (Rugi) Periode Berjalan</td>
                                        <td class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end">
                                                    200.000
                                                </div>
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="w-40" style="width: 40%">Bagi Hasil Penyertaan :</th>
                                        <th class="w-25" style="width: 25%">
                                            
                                        </th>
                                    </tr>
                                    <tr>
                                        <td class="w-40" style="width: 40%">Bagi Hasil Penyertaan Modal Desa</td>
                                        <td class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end">
                                                    200.000
                                                </div>
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-40" style="width: 40%">Bagi Hasil Penyertaan Modal Masyarakat</td>
                                        <td class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end">
                                                    200.000
                                                </div>
                                            </div> 
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot id="total-lost-profit-now">
                                    <tr>
                                        <th class="w-40" style="width: 40%">Total Laba Ditahan</th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-equity-now">
                                                    
                                                </div>
                                            </div>  
                                        </th>
                                    </tr>
                                </tfoot>
                                
                            </table>

                            <table class="table">
                                <thead id="total-equity">
                                    <tr class="text-primary">
                                        <th class="w-40" style="width: 40%">Modal Akhir</th>
                                        <th class="w-25" style="width: 25%">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Rp. 
                                                </div>
                                                <div class="text-end" id="total-liability-equity-now">
                                                    
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
    <script src="/js/admin/report/changes-in-equity.js"></script>
    <script src="/js/admin/report/api.js"></script>
@endsection
