@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Tarik Tunai</h3>

        <div class="d-flex">
            <form class="d-flex input-group mb-3" onsubmit="searchForm(event)">
                <input type="text" class="form-control" placeholder="Cari" aria-label="Cari" aria-describedby="search-input" id="search-input">
                <button class="btn btn-outline-secondary btn-sm" type="submit" id="search-button"><i class="bi bi-search"></i></button>
            </form>
            <div class="mx-1 d-print-none">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal" onclick="filterButton()"><i class="bi bi-filter"></i></button>
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
                <div class="fw-bold d-md-flex d-none justify-content-between fw-bold border-top border-bottom py-2 px-1">
                    <div style="width:1%" class="px-2">
                    </div>
                    <div style="width:10%" class="px-2 my-auto">Tanggal</div>
                    <div style="width:15%" class="px-2 my-auto">No. Ref</div>
                    <div style="width:20%" class="px-2 my-auto">No. Rekening</div>
                    <div style="width:15%" class="px-2 my-auto">Nama</div>
                    <div style="width:20%" class="px-2 my-auto text-end">Nilai (IDR)</div>                    
                </div>

                <div style="height: 350px;" class="overflow-auto custom-scroll" id="list-data">
                    
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
                    <h4>Hapus Data Tarik Tunai ?</h4>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-delete" data-bs-dismiss="modal" onclick="submitDeleteContact()">
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
                    <h5 class="modal-title fw-bold" id="showDetailModalLabel">Detail Tarik Tunai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="row justify-content-start text-start p-2">
                        <div class="col-md-6 col-12">
                            <div class="row justify-content-start text-start p-2">
                                <div class="col-md-4 col-12 fw-bold">
                                    Tanggal
                                </div>
                                <div class="col-md-8 col-12 font-bold text-start" id="date-detail">
                                    
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="row justify-content-start text-start p-2">
                                <div class="col-md-4 col-12 fw-bold">
                                    No Ref
                                </div>
                                <div class="col-md-8 col-12 font-bold text-start" id="no-ref-detail">
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-start text-start p-2">
                        <div class="col-md-6 col-12">
                            <div class="row justify-content-start text-start p-2">
                                <div class="col-md-4 col-12 fw-bold">
                                    Deskripsi
                                </div>
                                <div class="col-md-8 col-12 font-bold text-start" id="description-detail">
                                    : Tarik Tunai
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                        </div>
                    </div>

                    <div class="row justify-content-start text-start p-2">
                        <div class="col-md-6 col-12">
                            <div class="row justify-content-start text-start p-2">
                                <div class="col-md-4 col-12 fw-bold">
                                    No Rekening
                                </div>
                                <div class="col-md-8 col-12 font-bold text-start" id="saving-contact-detail">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="row justify-content-start text-start p-2">
                                <div class="col-md-4 col-12 fw-bold">
                                    Nama
                                </div>
                                <div class="col-md-8 col-12 font-bold text-start" id="saving-contact-name-detail">
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-start text-start p-2">
                        <div class="col-12">
                            <div class="row justify-content-start text-start p-2">
                                <div class="col-md-2 col-12 fw-bold">
                                    Jumlah (IDR)
                                </div>
                                <div class="col-md-10 col-12 font-bold text-start" id="value-detail">
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-start text-start p-2">
                        <div class="col-12">
                            <div class="row justify-content-start text-start p-2">
                                <div class="col-md-2 col-12 fw-bold" id="author-detail-label">
                                </div>
                                <div class="col-md-10 col-12 font-bold text-start" id="author-detail">
                                </div>
                        </div>                            
                        </div>
                    </div>                   
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <a type="button" class="btn btn-primary" id="btn-submit-print-single" target="_blank" href="#">
                        Cetak    
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    {{-- Create Akun --}}
    <form onsubmit="submitContact(event)">
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="setDefault()"></button>
                    </div>
                    <div class="modal-body overflow-auto custom-scroll" style="height: 400px;"> 
                        <div>
                            <label for="date-input" class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" id="date-input" placeholder="Tanggal" onchange="changeDate(this)">
                        </div> 
                        <div class="mt-3">
                            <label for="no-ref-input" class="form-label fw-bold">No Ref</label>
                            <input type="text" class="form-control" id="no-ref-input" placeholder="No Ref" onchange="changeNoRef(this)">
                        </div>  
                        <div class="position-relative z-index-1 mt-3">
                            <div class="">
                                <label for="contact-input" class="form-label fw-bold">No Rekening</label>
                                <div class="mb-1">
                                    <input type="text" class="form-control search-input-dropdown" placeholder="No Rekening " aria-label="No Rekening " aria-describedby="create-address" onclick="showContactDropdown(this)" onkeyup="changeContactDropdown(this)" onchange="changeContact(this)" id="contact-input" autocomplete="off">
                                </div>
                            </div>
                            <div class="bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary d-none" id="contact-list" style="max-height: 130px">
                                
                            </div>
                        </div>                  
                        <div class="mt-3">
                            <label for="name" class="form-label fw-bold">Nama</label>
                            <input type="text" class="form-control" id="name-input" placeholder="Nama">
                        </div>
                        <div class="mt-3">
                            <label for="value" class="form-label fw-bold">Jumlah</label>
                            <input type="text" class="form-control text-end total-input" 
                            inputmode="numeric" autocomplete="off" 
                            onclick="this.select()" value="0" onkeyup="setCurrencyFormat(this)" onchange="changeTotal(this)" data-order="0" id="value-input">
                        </div>
                        
                        <div class="position-relative z-index-1 mt-3">
                            <div class="">
                                <label for="contact-input" class="form-label fw-bold">Kas (Akun Debit)</label>
                                <div class="mb-1">
                                    <input type="text" class="form-control search-input-dropdown" placeholder="Kas (Akun Debit)" aria-label="NIK" aria-describedby="account" onclick="showAccountDropdown(this)" onkeyup="changeAccountDropdown(this)" onchange="changeAccount(this)" id="account-input" autocomplete="off">
                                </div>
                            </div>
                            <div class="bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary d-none" id="account-list" style="max-height: 130px">
                                
                            </div>
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

    {{-- filter  --}}
    {{-- Modal Filter --}}
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
    <script src="/js/business/withdrawal/index.js"></script>
    <script src="/js/business/withdrawal/api.js"></script>
@endsection
