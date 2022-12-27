@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Laporan Neraca Saldo</h3>

        <div class="d-flex">
            <div class="mx-1">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal" onclick="filterButton()"><i class="bi bi-filter"></i></button>
            </div>
            <div class="mx-1">
                <button class="btn btn-outline-secondary" id="print-button" onclick="goToPrintCashflow()"><i class="bi bi-printer"></i></button>
            </div>
            
        </div>
    </div>

    <div class="page-content mt-2">
        <div style="height: 500px;" class="overflow-auto custom-scroll card-body">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-end fst-italic">
                        Periode: <span id="period" class="font-bold"></span>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
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

                <div class="row mt-5">
                    <div class="col-12">
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

                <div class="row">
                    <div class="col-12">
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
    <script src="/js/business/report/balance.js"></script>
    <script src="/js/business/report/api.js"></script>
@endsection
