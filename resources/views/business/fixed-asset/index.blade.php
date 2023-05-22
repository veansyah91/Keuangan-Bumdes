@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Harta Tetap</h3>

        <div class="d-flex">
            <form class="d-flex input-group mb-3" onsubmit="searchForm(event)">
                <input type="text" class="form-control" placeholder="Cari" aria-label="Cari" aria-describedby="search-input" id="search-input">
                <button class="btn btn-outline-secondary btn-sm" type="submit" id="search-button"><i class="bi bi-search"></i></button>
            </form>
            
        </div>
        <div class="d-none d-md-block">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#depreciateModal">Penyusutan</button>
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
                <div class="fw-bold d-md-flex d-none justify-content-between font-bold border-top border-bottom py-2 px-1">
                    <div style="width:1%" class="px-2">
                    </div>
                    <div style="width:14%" class="px-2 my-auto">Tanggal Perolehan</div>
                    <div style="width:20%" class="px-2 my-auto">Nama</div>
                    <div style="width:12%" class="px-2 my-auto text-end">Nilai Perolehan (IDR)</div>
                    <div style="width:12%" class="px-2 my-auto text-end">Umur Pemakaian (bulan)</div>
                    <div style="width:7%" class="px-2 my-auto text-center">Status</div>
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
                    <h4>Hapus Data Harta Tetap ?</h4>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-delete" data-bs-dismiss="modal" onclick="submitDeleteFixedAsset()">
                        Hapus    
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Show Detail --}}
    <div class="modal fade" id="showDetailModal" tabindex="-1" aria-labelledby="showDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showDetailModalLabel">Rincian Data Harta Tetap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body overflow-auto custom-scroll" style="height: 400px;">
                    <div class="text-center">
                        <h4 id="name-detail"></h4>
                    </div>
                    <div class="container">
                        <div class="row mt-4">
                            <div class="col-6">
                                <div class="font-bold">No Ref</div>
                                <div id="no-ref-detail"></div>
                            </div>
                            <div class="col-6">
                                <div class="font-bold">Tanggal Pengadaan</div>
                                <div id="date-detail"></div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="font-bold">Nilai Perolehan (IDR)</div>
                                <div id="value-detail"></div>
                            </div>
                            <div class="col-6">
                                <div class="font-bold">Residu (IDR)</div>
                                <div id="salvage-detail"></div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="font-bold">Masa Pemakaian</div>
                                <div id="useful-life-detail"></div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="font-bold">Dibuat Oleh</div>
                                <div id="author-detail"></div>
                            </div>
                        </div>

                        <div id="ledger" class="mx-3 mt-3">
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
        
                            <div class="row mt-2">
                                <div class="col-3 text-start p-2 border border-white account-code-detail">
                                    
                                </div>
                                <div class="col-3 text-start p-2 border border-white account-name-detail">
                                    
                                </div>
                                <div class="col-3 text-end p-2 border border-white debit-detail">
                                    
                                </div>
                                <div class="col-3 text-end p-2 border border-white credit-detail">
                                    
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3 text-start p-2 border border-white account-code-detail">
                                    
                                </div>
                                <div class="col-3 text-start p-2 border border-white account-name-detail">
                                    
                                </div>
                                <div class="col-3 text-end p-2 border border-white debit-detail">
                                    
                                </div>
                                <div class="col-3 text-end p-2 border border-white credit-detail">
                                    
                                </div>
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

                        <div class="mt-3">
                            <h5>Tabel Penyusutan</h5>
                        </div>
                        <div class="depreciation row justify-content-center" id="depreciation">
                            <div class="col-12 col-md-8">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">
                                                Tanggal
                                            </th>
                                            <th class="text-end">
                                                Nilai Penyusutan
                                            </th>
                                            <th class="text-end">
                                                Nilai Nilai Buku
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="depreciation-list">
                                        
                                    </tbody>
                                </table>
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

    <!-- Modal -->
    {{-- Create Akun --}}
    <form onsubmit="submitFixedAsset(event)">
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="setDefault()"></button>
                    </div>
                    <div class="modal-body overflow-auto custom-scroll" style="height: 400px;">                        
                        <div class="mb-3">
                            <label for="no-ref-input" class="form-label fw-bold">No Ref</label>
                            <input type="text" class="form-control" id="no-ref-input" placeholder="" onchange="noRefInputChange(this)">
                        </div>
                        <div class="mb-3">
                            <label for="name-input" class="form-label fw-bold">Nama Item (Akun Akan Dibuat Otomatis pada Debit)</label>
                            <input type="text" class="form-control" id="name-input" placeholder="Nama" required onchange="nameInputChange(this)">
                        </div>
                        <div class="position-relative mb-3">
                            <div class="">
                                <label for="credit-account-input" class="form-label fw-bold">Akun Kredit</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control search-input-dropdown" placeholder="Akun Debit" aria-label="Akun Debit" aria-describedby="create-credit-account-input-account" list="dataSubClassificationAccount" onclick="showAccountDropdown()" onkeyup="changeAccountDropdown(this)" onchange="changeAccountValue(this)" id="credit-account-input" autocomplete="off">
                                </div>
                            </div>
                            <div class="bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary" id="debit-account-list" style="max-height: 130px">
                                
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="date-input" class="form-label fw-bold">Tanggal Perolehan</label>
                            <input type="date" class="form-control" id="date-input" placeholder="Nama" required onchange="dateInputChange(this)">
                        </div>
                        <div class="mb-3">
                            <label for="value-input" class="form-label fw-bold">Nilai Perolehan</label>
                            <input type="text" class="form-control text-end" id="value-input" placeholder="" required onchange="valueInputChange(this)" inputmode="numeric" onclick="this.select()" onkeyup="valueInputChange(this)">
                        </div>
                        <div class="mb-3">
                            <label for="salvage-input" class="form-label fw-bold">Nilai Residu</label>
                            <input type="text" class="form-control text-end" id="salvage-input" placeholder="" required onchange="salvageInputChange(this)" inputmode="numeric" onclick="this.select()" onkeyup="salvageInputChange(this)">
                        </div>
                        <div class="mb-3">
                            <label for="useful-life-input" class="form-label fw-bold">Umur Penggunaan (bulan)</label>
                            <input type="text" class="form-control text-end" id="useful-life-input" placeholder="" required onchange="useFullInputChange(this)" inputmode="numeric" onclick="this.select()" onkeyup="useFullInputChange(this)">
                        </div>
                        <div class="form-check form-switch" id="is-active">
                            <input class="form-check-input" type="checkbox" role="switch" id="is-active-input" checked onchange="changeIsActiveInput(this)">
                            <label class="form-check-label" for="is-active-input" id="is-active-input-label">Aktif</label>
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

    {{-- Depreciate Fixed Asset --}}
    <form onsubmit="submitDepreciate(event)">
        <div class="modal fade" id="depreciateModal" tabindex="-1" aria-labelledby="depreciateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="depreciateModalLabel">Penyusutan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="setDefault()"></button>
                    </div>
                    <div class="modal-body overflow-auto custom-scroll" style="height: 400px;">                        
                        <div class="mb-3">
                            <h5>Penjelasan:</h5>
                            <p>
                                Penyusutan Diperbarui Hingga Akhir Bulan Ini.
                            </p>
                            <i>
                                (Saat Ini Hanya Mendukung Metode Penyusutan Garis Lurus)
                            </i>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="setDefault()">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="depreciate-btn-submit">
                            <span id="depreciate-btn-submit-label">Lakukan Penyusutan</span>    
                        </button>
                    </div>
            </div>
            </div>
        </div>
    </form>
    
@endsection

@section('script')
    <script src="/js/business/fixed-asset/index22052023.js"></script>
    <script src="/js/business/fixed-asset/api.js"></script>
@endsection
