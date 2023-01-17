@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Produk</h3>

        <div class="d-flex">
            <form class="d-flex input-group mb-3" onsubmit="searchForm(event)">
                <input type="text" class="form-control" placeholder="Cari" aria-label="Cari" aria-describedby="search-input" id="search-input">
                <button class="btn btn-outline-secondary btn-sm" type="submit" id="search-button"><i class="bi bi-search"></i></button>
                <div class="mx-1">
                    <div class="btn-group dropend g-1">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-printer"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('business.product.print', $business->id) }}" target="_blank">Produk</a></li>
                            <li><a class="dropdown-item" href="{{ url('/'. $business->id .'/product/print-stock?this_month=1') }}" target="_blank">Kartu Stok</a></li>
                        </ul>
                    </div>
                </div>
            </form>
            
        </div>
        <div class="d-none d-md-block">
            <a class="btn btn-primary" href="{{ route('business.product.create', $business->id) }}">Tambah Data</a>
        </div>
        <div class="fixed-bottom text-end mb-3 mr-3 d-md-none ">
            <button class="btn btn-primary rounded-circle" href="{{ route('business.product.create', $business->id) }}">
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
                                <path fill-ru
                                le="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="d-md-flex d-none justify-content-between font-bold border-top border-bottom py-2 px-1 fw-bold">
                    <div style="width:5%" class="px-2">
                    </div>
                    <div style="width:14%" class="px-2 my-auto">Kode</div>
                    <div style="width:20%" class="px-2 my-auto">Nama</div>
                    <div style="width:20%" class="px-2 my-auto">Kategori</div>
                    <div style="width:20%" class="px-2 my-auto text-end">Harga Jual (IDR)</div>
                    <div style="width:10%" class="px-2 my-auto text-end">Sisa Stok</div>
                    <div style="width:10%" class="px-2 my-auto text-center">Status</div>
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
                    <h4>Hapus Produk ?</h4>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-delete" data-bs-dismiss="modal" onclick="submitDeleteProduct()">
                        Hapus    
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
                    <h5 class="modal-title" id="showDetailModalLabel">Detail Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <h4 id="name-detail"></h4>
                        <p id="status-detail" class="fw-bold bg-opacity-25"></p>
                    </div>
                    <div class="d-flex">
                        <div class="col-6">
                            <div class="font-bold fw-bold">Kode</div>
                            <div id="code-detail"></div>
                        </div>
                        <div class="col-6">
                            <div class="font-bold fw-bold">Satuan</div>
                            <div id="unit-detail" class="text-uppercase"></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="font-bold fw-bold">Kategori</div>
                        <div id="category-detail"></div>
                    </div>
                    <div class="mt-3">
                        <div class="font-bold fw-bold">Harga Jual</div>
                        <div id="selling-price-detail"></div>
                    </div>
                    <div class="mt-3">
                        <div class="font-bold fw-bold">Harga Pokok Penjualan</div>
                        <div id="unit-price-detail"></div>
                    </div>
                    <div class="mt-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="is-stock-checked-detail" readonly disabled>
                        <label class="form-check-label" for="is-stock-checked-detail">Lacak Persediaan</label>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('script')
    <script src="/js/business/product/index.js"></script>
    <script src="/js/business/product/api.js"></script>
@endsection
