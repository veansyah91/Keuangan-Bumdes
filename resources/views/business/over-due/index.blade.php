@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Jatuh Tempo</h3>

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
            <span class="fst-italic update-label d-none">Pembaruan Terakhir</span>
            <button class="btn btn-primary update-button d-none" onclick="reloadData()">
                <span class="update-button-label">Perbarui Data</span>    
            </button>
        </div>
    </div>
    <div class="row mb-3 mr-3 d-md-none">
        <div class="col-12">
            <button class="btn btn-primary update-button d-none" style="width: 100%" onclick="reloadData()">
                <span class="update-button-label">Perbarui Data</span> 
            </button>
            <div class="fst-italic text-center w-full update-label d-none">Pembaruan Terakhir</div>
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
                    <div style="width:5%" class="px-2">
                    </div>
                    <div style="width:15%" class="px-2 my-auto">No. Ref</div>
                    <div style="width:15%" class="px-2 my-auto">Nama</div>
                    <div style="width:20%" class="px-2 my-auto">Jatuh Tempo</div>
                    <div style="width:20%" class="px-2 my-auto">Terlambat</div>                 
                    <div style="width:20%" class="px-2 my-auto">Detail</div>
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
                <div class="modal-body">
                    <div class="text-center">
                        <div class="fs-1 text-danger">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <h4>Hapus Data Piutang?</h4>
                        <hr>
                    </div>

                    <div>
                        <h5 class="fw-bold" style="font-size: 14px">Catatan:</h5>
                        <div>
                            Penghapusan Piutang Akan Dimasukkan Ke Pembayaran Piutang
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h5 class="fw-bold" style="font-size: 14px">Posisi Akun:</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Beban Piutang</td>
                                    <td></td>
                                </tr>
                                <tr style="background-color: #D9D9D9">
                                    <td></td>
                                    <td>Piutang</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-delete" data-bs-dismiss="modal" onclick="submitDeleteData()">
                        Hapus Piutang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Show Detail --}}
    <div class="modal fade" id="showDetailModal" tabindex="-1" aria-labelledby="showDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="showDetailModalLabel">Detail Pengajuan Kredit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body overflow-auto custom-scroll">
                    <div class="text-center">
                        <h4 id="name-detail"></h4>
                        <p id="status-detail">
                            
                        </p>
                    </div>
                    <div>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-detail" type="button" role="tab" aria-controls="home-detail" aria-selected="true">Pengajuan</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-detail" type="button" role="tab" aria-controls="detail-detail" aria-selected="false">Detail</button>
                            </li>
                        </ul>
                        <div class="tab-content overflow-auto custom-scroll" id="myTabContent"  style="height: 330px;">
                            <div class="tab-pane fade show active" id="home-detail" role="tabpanel" aria-labelledby="home-tab">
                                <div class="mt-3">
                                    <div class="fw-bold">Produk</div>
                                    <div id="product-name-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Nilai Perolehan (IDR)</div>
                                    <div id="unit-price-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Nilai Penjualan (IDR)</div>
                                    <div id="value-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Tenor</div>
                                    <div id="tenor-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Angsuran Per Bulan (IDR)</div>
                                    <div id="installment-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">DP(IDR)</div>
                                    <div id="downpayment-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Tanggal Pengajuan</div>
                                    <div id="date-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Jatuh Tempo</div>
                                    <div id="term-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Diinput Oleh</div>
                                    <div id="author-detail"></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="detail-detail" role="tabpanel" aria-labelledby="detail-tab">
                                <div class="mt-3">
                                    <div class="fw-bold">No HP</div>
                                    <div>: <span id="phone-detail"></span></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">No KK</div>
                                    <div>: <span id="nkk-detail"></span></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">NIK</div>
                                    <div>: <span id="nik-detail"></span></div>
                                    
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Alamat</div>
                                    <div>: <span id="address-detail"></span></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Desa</div>
                                    <div>: <span id="village-detail"></span></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Kecamatan</div>
                                    <div>: <span id="district-detail"></span></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Kabupaten</div>
                                    <div>: <span id="regency-detail"></span></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Provinsi</div>
                                    <div>: <span id="province-detail"></span></div>
                                </div>
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
                    <h5 class="modal-title fw-bold" id="filterModalLabel">Filter</h5>
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
                    <div class="mb-3">
                        <label for="description-input" class="form-label fw-bold">Status</label>
                        <select class="form-select" id="select-status" onchange="changeStatus(this)">
                            <option value="all">Semua</option>
                            <option value="pending">Menunggu</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
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
    <script src="/js/business/over-due/index.js"></script>
    <script src="/js/business/over-due/api.js"></script>
@endsection
