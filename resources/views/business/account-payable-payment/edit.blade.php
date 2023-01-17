@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('style')
    <style>
        @media only screen and (max-width: 600px) {
            .list-input{
                width: 200%;
            }
            .list-input-form{
                overflow:scroll;
            }
        }

    </style>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" data-invoice="{{ $invoice }}" id="content">
        <h3>Ubah Faktur Penjualan</h3>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('business.invoice.index', $business->id) }}">Faktur Penjualan</a></li>
              <li class="breadcrumb-item active" aria-current="page">Faktur Penjualan</li>
            </ol>
        </nav>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body ">
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
                            <label for="contact-input" class="form-label">Pelanggan</label>
                            <div class="input-group mb-2 ">
                                <input type="text" class="form-control search-input-dropdown" placeholder="Kontak" aria-label="Pemasok" aria-describedby="contact--input" list="data-contact" onclick="showContactDropdown(this)" onkeyup="showContactDropdown(this)"
                                onchange="changeContact(this)" id="contact-input" autocomplete="off">
                            </div>
                            <div class="bg-light position-absolute list-group w-100 d-none search-select overflow-auto custom-scroll border border-2 border-secondary" id="contact-list" style="max-height: 130px">
                            
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
                <div class="w-100 list-input-form">
                    <div class="list-input">
                        <div class="fw-bold row justify-content-between border-top pt-2 border-bottom pb-2 fw-bold">
                            <div class="col-4">
                                Produk
                            </div>
                            <div class="col-1 col-md-2 text-end">
                                Qty
                            </div>
                            <div class="col-3 col-md-2 text-end">
                                Harga
                            </div>
                            <div class="col-3 col-md-2 text-end">
                                Total
                            </div>
                            <div class="col-1">
                                #
                            </div>
                        </div>
        
                        <div id="list-input-content">
                            
                        </div>
                    </div>
                </div>
                

                <div class="button-add-list">
                    <button class="btn btn-secondary w-100" onclick="addListInput()">Tambah</button>
                </div>

                <div class="row justify-content-end pt-2 border-bottom pb-2 fw-bold">
                    <div class="col-lg-4 col-12">
                        <div class="row">
                            <div class="col-6">
                                Grand Total
                            </div>
                            <div class="col-6 text-end" id="grand-total">
                                Rp. 0
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="row justify-content-end pt-2 border-bottom pb-2 fw-bold">
                    <div class="col-lg-4 col-12">
                        <div class="row">
                            <div class="col-6">
                                Jumlah Bayar
                            </div>
                            <div class="col-6 text-end">
                                <input type="text" class="form-control text-end payment-input" inputmode="numeric" autocomplete="off" onclick="this.select()" onkeyup="setCurrencyFormat(this)" onchange="changePayment(this)" value="0" id="payment-input">
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="row justify-content-end pt-2 border-bottom pb-2 fw-bold">
                    <div class="col-lg-4 col-12">
                        <div class="row">
                            <div class="col-6">
                                Kembali
                            </div>
                            <div class="col-6 text-end" id="return-payment-input">
                                Rp. 0
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="row justify-content-end pt-2 border-bottom pb-2 fw-bold">
                    <div class="col-lg-4 col-12">
                        <div class="row">
                            <div class="col-6 form-label">
                                Akun (Debit)
                            </div>
                            <div class="col-6 position-relative" id="account-debit-debit">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control search-input-dropdown" placeholder="Akun" aria-label="Akun" aria-describedby="account-debit-input" list="data-account" onclick="showAccountDebitDropdown(this)" onkeyup="showAccountDebitDropdown(this)"
                                    onchange="changeAccountDebit(this)" id="account-debit-input" autocomplete="off">
                                </div>
                                <div class="bg-light position-absolute list-group w-100 d-none search-select overflow-auto custom-scroll border border-2 border-secondary dropdown-menu-end" id="account-debit-list" style="max-height: 130px">
                                
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="row justify-content-end pt-2 border-bottom pb-2 fw-bold">
                    <div class="col-lg-2 col-6 text-end">
                        <a class="btn btn-outline-primary w-100" href="{{ route('business.invoice.index', $business->id) }}">Batal</a>
                    </div>  
                    <div class="col-lg-2 col-6 text-end d-none" id="btn-submit-invoice">
                        <button class="btn btn-primary w-100" onclick="submitInvoice()" id="btn-submit">
                            <span id="submit-button-label">
                                Simpan
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cari Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control" type="text" placeholder="Masukkan Kode/Nama Produk" id="search-product-input" onkeyup="changeSearchProductModal(this)">
                    <div class="list-group mt-3" id="product-lists">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
  </div>
@endsection

@section('script')
    <script src="/js/business/invoice/api.js"></script>
    <script src="/js/business/invoice/edit.js"></script>
@endsection
