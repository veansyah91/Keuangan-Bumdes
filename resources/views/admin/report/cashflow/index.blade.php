@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Laporan Arus Kas</h3>

        <div class="d-flex">
            <div class="mx-1">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal" onclick="filterButton()"><i class="bi bi-filter"></i></button>
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
                        Periode: <span id="period" class="font-bold"></span>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-8">
                        <h5>Aktifitas Operasional</h5>
                        <table class="table">
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

                <div class="row">
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

                <div class="row">
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

                <div class="row">
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
    </div>

    <!-- Modal -->
    
    {{-- filter  --}}
    {{-- Show Detail --}}
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description-input" class="form-label">Tanggal</label>
                        <select class="form-select" id="select-filter" onchange="changeFilter(this)">
                            <option value="today">Hari Ini</option>
                            <option value="this week">Minggu Ini</option>
                            <option value="this month">Bulan Ini</option>
                            <option value="this year">Tahun Ini</option>
                            <option value="custom">Custom</option>
                        </select>
                        
                    </div>
                    <div class="row d-none" id="date-range">
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <label for="start-filter" class="form-label">Dari</label>
                                <input type="date" class="form-control" id="start-filter" placeholder="Tanggal">
                                
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <label for="end-filter" class="form-label">Ke</label>
                                <input type="date" class="form-control" id="end-filter" placeholder="Tanggal">
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="submitFilter()" data-bs-dismiss="modal">
                        Ok    
                    </button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('script')
    <script src="/js/admin/report/cashflow.js"></script>
    <script src="/js/admin/report/api.js"></script>
@endsection
