@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Kontak</h3>

        <div class="d-flex">
            <form class="d-flex input-group mb-3" onsubmit="searchForm(event)">
                <input type="text" class="form-control" placeholder="Cari" aria-label="Cari" aria-describedby="search-input" id="search-input">
                <button class="btn btn-outline-secondary btn-sm" type="submit" id="search-button"><i class="bi bi-search"></i></button>
            </form>
            <div class="mx-1">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal" onclick="filterButton()"><i class="bi bi-filter"></i></button>
            </div>
            <div class="mx-1">
                <button class="btn btn-outline-secondary" onclick="goToPrintCashMutations()"><i class="bi bi-printer"></i></button>
            </div>
            
        </div>
        <div class="d-none d-md-block">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal" onclick="addData()">Tambah Data</button>
        </div>
        <div class="fixed-bottom text-end mb-3 mr-3 d-md-none ">
            <button class="btn btn-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#createModal" onclick="addData()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-lg-2 col-6 text-end my-auto">
                        <small all class="fst-italic">
                            <span id="count-data"> </span>
                        </small>
                    </div>
                    <div class="col-lg-1 col-1 text-end">
                        <button class="btn btn-sm" id="prev-page" onclick="prevButton()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                                <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                            </svg>
                        </button>
                        
                    </div>
                    <div class="col-lg-1 col-1 my-auto">
                        <button class="btn btn-sm" id="next-page" onclick="nextButton()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                                <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="d-md-flex d-none justify-content-between font-bold border-top border-bottom py-2 px-1">
                    <div style="width:1%" class="px-2">
                    </div>
                    <div style="width:10%" class="px-2 my-auto">Tanggal</div>
                    <div style="width:10%" class="px-2 my-auto">No. Referensi</div>
                    <div style="width:20%" class="px-2 my-auto">Deskripsi</div>
                    <div style="width:20%" class="px-2 text-end my-auto">Nilai (IDR)</div>
                    
                </div>

                <div style="height: 450px;" class="overflow-auto custom-scroll" id="list-data">
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    {{-- Delete Confirmation --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="fs-1 text-danger">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <h4>Hapus Data Mutasi Kas ?</h4>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-delete" data-bs-dismiss="modal" onclick="submitDeleteCashMutation()">
                        Hapus    
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Show Detail --}}
    <div class="modal fade" id="showDetailModal" tabindex="-1" aria-labelledby="showDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showDetailModalLabel">Detail Mutasi Kas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="row justify-content-start text-start p-2">
                        <div class="col-2">
                            Tanggal
                        </div>
                        <div class="col-10 font-bold text-start" id="date-detail">
                            
                        </div>
                    </div>
                    <div class="row justify-content-start text-start p-2">
                        <div class="col-2">
                            No Ref
                        </div>
                        <div class="col-10 font-bold text-start" id="no-ref-detail">
                            
                        </div>
                    </div>
                    <div class="row justify-content-start text-start p-2">
                        <div class="col-2">
                            Deskripsi
                        </div>
                        <div class="col-10 font-bold text-start" id="description-detail">
                            
                        </div>
                    </div>
                    <div class="row justify-content-start text-start p-2">
                        <div class="col-2">
                            Detail
                        </div>
                        <div class="col-10 font-bold text-start" id="detail-detail">
                            
                        </div>
                    </div>
                    <div class="row justify-content-start text-start p-2">
                        <div class="col-2" id="author-detail-label">

                        </div>
                        <div class="col-10 font-bold text-start" id="author-detail">
                            
                        </div>
                    </div>

                    <div id="ledger" class="mx-3">
                        <div class="row mt-2 bg-info text-white">
                            <div class="col-3 text-start p-2 border border-white">
                                Kode Akun
                            </div>
                            <div class="col-3 text-start p-2 border border-white">
                                Nama Akun
                            </div>
                            <div class="col-3 text-end p-2 border border-white">
                                Debit
                            </div>
                            <div class="col-3 text-end p-2 border border-white">
                                Credit
                            </div>
                        </div>
    
                        <div id="content-detail" >
                            
                        </div>
    
                        <div class="row mt-2 bg-info text-white">
                            <div class="col-6 text-start p-2 border border-white">
                                Total
                            </div>
                            <div class="col-3 text-end p-2 border border-white" id="total-debit-detail">
                                Rp. 0
                            </div>
                            <div class="col-3 text-end p-2 border border-white" id="total-credit-detail">
                                Rp. 0
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-submit-print-single" onclick="goToPrintCashMutationPerId(this)" data-bs-dismiss="modal">
                        Cetak    
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- filter  --}}
    {{-- Show Filter --}}
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

    <!-- Modal -->
    {{-- Create Akun --}}
    <form onsubmit="submitAccount(event)">
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="setDefault()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="position-relative mb-3">
                            <div class="">
                                <label for="sub-category" class="form-label">Kategori Akun</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control search-input-dropdown" placeholder="Kategori Akun" aria-label="Kategori Akun" aria-describedby="create-sub-category-account" list="dataSubClassificationAccount" onclick="showSubCategoryDropdown()" onkeyup="changeSubCategoryDropdown(this)" id="sub-category" autocomplete="off">
                                    <button class="btn " type="button" id="create-sub-category-account" onclick="showSubCategory()" data-bs-toggle="modal" data-bs-target="#createSubCategoryModal" data-bs-dismiss="modal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="bg-light position-absolute list-group w-100 d-none search-select overflow-auto custom-scroll border border-2 border-secondary" id="subclassification-list" style="max-height: 130px">
                                
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="code" class="form-label">Kode Akun </label>
                            <input type="text" class="form-control" id="code" placeholder="Kode Akun">
                            
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Akun</label>
                            <input type="text" class="form-control" id="name" placeholder="Nama Akun">
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is-cash">
                            <label class="form-check-label" for="is-cash">Kas</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is-active" checked>
                            <label class="form-check-label" for="is-active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="setDefault()">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <span id="btn-submit-label">Simpan</span>    
                        </button>
                    </div>
            </div>
            </div>
        </div>
    </form>
    
@endsection

@section('script')
    <script src="/js/admin/cash-mutation/index.js"></script>
    <script src="/js/admin/cash-mutation/api.js"></script>
@endsection
