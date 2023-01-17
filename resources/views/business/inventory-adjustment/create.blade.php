@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Tambah Penyesuaian Barang</h3>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('business.inventory-adjustment.index', $business->id) }}">Penyesuaian Barang</a></li>
              <li class="breadcrumb-item active" aria-current="page">Penyesuaian Barang</li>
            </ol>
        </nav>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success d-flex align-items-center d-none" id="success-alert" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                    <div id="success-alert-message">
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label for="date-input" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="date-input" placeholder="Tanggal" onchange="changeDataInput(this)">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label for="no-ref-input" class="form-label">No Referensi</label>
                            <input type="text" class="form-control" id="no-ref-input" placeholder="Kode Akun" onchange="noRefChange(this)">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="mb-3 position-relative">
                            <label for="credit-account-input" class="form-label">Akun Credit</label>
                            <div class="input-group mb-2 ">
                                <input type="text" class="form-control search-input-dropdown" placeholder="Akun Credit" aria-label="Pemasok" aria-describedby="credit-account--input" list="data-account" onclick="showAccountCreditDropdown(this)" onkeyup="showAccountCreditDropdown(this)"
                                onchange="changeAccount(this)" id="credit-account-input" autocomplete="off">
                            </div>
                            <div class="bg-light position-absolute list-group w-100 d-none search-select overflow-auto custom-scroll border border-2 border-secondary" id="account-list" style="max-height: 130px">
                            
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description-input" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="description-input" placeholder="Detail" onchange="detailInputChange(this)">
                        </div>
                    </div>
                </div>

                {{-- list input jurnal  --}}
                {{-- Header --}}
                <div class="fw-bold row justify-content-between border-top pt-2 border-bottom pb-2 font-bold">
                    <div class="col-4">
                        Produk
                    </div>
                    <div class="col-2 text-end">
                        Jumlah
                    </div>
                    <div class="col-2 text-end">
                        HPP
                    </div>
                    <div class="col-2 text-end">
                        Total
                    </div>
                    <div class="col-1">
                        #
                    </div>
                </div>

                <div id="list-input-content">
                    
                </div>

                <div class="button-add-list">
                    <button class="btn btn-secondary w-100" onclick="addListInput()">Tambah</button>
                </div>

                <div class="row justify-content-end pt-2 border-bottom pb-2 font-bold">
                    <div class="col-lg-2 col-6 text-end">
                        <a class="btn btn-outline-primary w-100" href="{{ route('business.inventory-adjustment.index', $business->id) }}">Batal</a>
                    </div>  
                    <div class="col-lg-2 col-6 text-end d-none" id="btn-submit-inventory-adjustment">
                        <button class="btn btn-primary w-100" onclick="submitInventoryAdjustment()" id="btn-submit">
                            <span id="submit-button-label">
                                Simpan
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/business/inventory-adjustment/api.js"></script>
    <script src="/js/business/inventory-adjustment/create.js"></script>
@endsection
