@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Pembayaran Utang</h3>

        <div class="d-flex">
            <form class="d-flex input-group mb-3" onsubmit="searchForm(event)">
                <input type="text" class="form-control" placeholder="Cari" aria-label="Cari" aria-describedby="search-input" id="search-input">
                <button class="btn btn-outline-secondary btn-sm" type="submit" id="search-button"><i class="bi bi-search"></i></button>
            </form>
            <div class="mx-1">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal" onclick="filterButton()"><i class="bi bi-filter"></i></button>
            </div>
            
        </div>
        <div class="d-none d-md-block">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal" onclick="addData()">Tambah Data</button>
        </div>
        <div class="fixed-bottom text-end mb-3 mr-3 d-md-none ">
            <a class="btn btn-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#createModal" onclick="addData()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                </svg>
            </a>
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
                <div class="fw-bold d-md-flex d-none justify-content-between font-bold border-top border-bottom py-2 px-1">
                    <div style="width:5%" class="px-2">
                    </div>
                    <div style="width:20%" class="px-2 my-auto">Tanggal</div>
                    <div style="width:20%" class="px-2 my-auto">No Ref</div>
                    <div style="width:20%" class="px-2 my-auto text-start">Pelanggan</div>
                    <div style="width:20%" class="px-2 my-auto text-end">Nilai (IDR)</div>
                    
                </div>

                <div style="height: 350px;" class="overflow-auto custom-scroll" id="list-data">
                    
                </div>
            </div>
        </div>
    </div>
    

    <!-- Modal -->
    {{-- Create or Edit Akun --}}
    <form onsubmit="submitAccountPayable(event)">
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="setDefault()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="date-input" class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" id="date-input" placeholder="Tanggal" onchange="changeDateInput(this)">
                        </div>
                        <div class="mb-3">
                            <label for="no-ref" class="form-label fw-bold">No Ref</label>
                            <input type="text" class="form-control" id="no-ref" placeholder="No Ref">
                        </div>
                        <div class="position-relative mb-3 z-index-1">
                            <div class="">
                                <label for="contact" class="form-label fw-bold">Nama Pelanggan</label>
                                <div class="mb-1">
                                    <input type="text" class="form-control search-input-dropdown" placeholder="Nama Pelanggan" aria-label="Nama Pelanggan" aria-describedby="create-contact" onclick="showContactDropdown(this)" onkeyup="changeContactDropdown(this)" onchange="changeContact(this)" id="contact" autocomplete="off">
                                </div>
                            </div>
                            <div class="bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary d-none" id="contact-list" style="max-height: 130px">
                                
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Deskripsi</label>
                            <input type="text" class="form-control" id="description" placeholder="Deskripsi">
                        </div>
                        <div class="mb-3 position-relative" id="invoice-dropdown-input">
                            <div class="">
                                <label for="invoice" class="form-label fw-bold">Faktur Pembelian</label>
                                <div class="mb-1">
                                    <input type="text" class="form-control search-input-dropdown" placeholder="Faktur Pembelian" aria-label="PurchaseGoods" aria-describedby="create-invoice" onclick="showPurchaseGoodsDropdown(this)" onkeyup="changePurchaseGoodsDropdown(this)" onchange="changePurchaseGoods(this)" id="invoice" autocomplete="off">
                                </div>
                            </div>
                            <div class="bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary d-none" id="invoice-list" style="max-height: 130px">
                                
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="no-ref" class="form-label fw-bold">Jumlah Bayar</label>
                            <input type="text" class="form-control text-end" id="value" placeholder="Jumlah Bayar" onkeyup="setCurrencyFormat(this)" inputmode="numeric" autocomplete="off" onclick="this.select()" value="0" onchange="changeValue(this)">
                        </div>
                        <div class="mb-3 position-relative" id="account-dropdown-input">
                            <div class="">
                                <label for="account" class="form-label fw-bold">Kas (Kredit)</label>
                                <div class="mb-1">
                                    <input type="text" class="form-control search-input-dropdown" placeholder="Kredit" aria-label="Kredit" aria-describedby="create-account" onclick="showAccountDropdown(this)" onkeyup="changeAccountDropdown(this)" onchange="changeAccount(this)" id="account" autocomplete="off">
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

    {{-- Delete Confirmation --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="fs-1 text-danger">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <h4>Hapus Data Pembayaran Utang?</h4>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-delete" data-bs-dismiss="modal" onclick="submitDeleteAccountPayablePayment()">
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
                    <h5 class="modal-title" id="showDetailModalLabel">Faktur Pembayaran Utang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-between">
                        <div class="col-12 col-md-4">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">No Ref</th>
                                        <td style="width: 70%" id="no-ref-detail">: INV-000000</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Tanggal</th>
                                        <td style="width: 70%" id="date-detail">: INV-000000</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%">Kasir</th>
                                        <td style="width: 70%" id="author-detail">: INV-000000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">Kepada</th>
                                        <td id="contact-name-detail">Orange Ponsel</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%"></th>
                                        <td id="contact-address-detail">Air Molek</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="ledger" class="mx-3">
                        <div class="row mt-2 bg-info text-white">
                            <div class="col-4 text-start p-2 border border-white">
                                No Faktur
                            </div>
                            <div class="col-4 text-start p-2 border border-white">
                                Tanggal Faktur
                            </div>
                            <div class="col-4 text-end p-2 border border-white">
                                Total Bayar (IDR)
                            </div>
                        </div>
    
                        <div id="content-detail" >
                            
                        </div>
    
                        <div class="row mt-2 bg-info text-white">
                            <div class="col-8 text-start p-2 border border-white">
                                Total
                            </div>
                            <div class="col-4 text-end p-2 border border-white" id="total-detail">
                                Rp. 0
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

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
                        <label for="description-input" class="form-label fw-bold">Tanggal</label>
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
                                <label for="start-filter" class="form-label fw-bold">Dari</label>
                                <input type="date" class="form-control" id="start-filter" placeholder="Tanggal">
                                
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <label for="end-filter" class="form-label fw-bold">Ke</label>
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
    <script src="/js/business/account-payable-payment/index.js"></script>
    <script src="/js/business/account-payable-payment/api.js"></script>
@endsection
