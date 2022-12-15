@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Laporan Laba Rugi</h3>

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

                <div class="row">
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

                <div class="row">
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
    <script src="/js/admin/report/lost-profit.js"></script>
    <script src="/js/admin/report/api.js"></script>
@endsection
