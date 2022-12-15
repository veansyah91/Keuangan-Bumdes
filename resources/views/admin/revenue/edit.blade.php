@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Ubah Pendapatan</h3>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('revenue.index') }}">Pendapatan</a></li>
              <li class="breadcrumb-item active" aria-current="page">Ubah Pendapatan</li>
            </ol>
        </nav>
    </div>

    <div class="page-content" id="page-content" data-id="{{ $revenue_id }}">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success d-flex align-items-center d-none" id="success-alert" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                    <div id="success-alert-message">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6" >
                        <div class="mb-3">
                            <label for="contact" class="form-label">Diterima Dari</label>
                            <div class="position-relative">
                                <input type="text" class="form-control search-input-dropdown" placeholder="Kontak" aria-label="Kontak" aria-describedby="input-contact"
                                onclick="showContactDropdown(this)" onkeyup="showContactDropdown(this)"
                                onchange="changeContactDropdown(this)" autocomplete="off" id="contact-input">
                                <div class="d-none bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary" style="max-height: 130px; z-index:1" id="contact-list">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6" >
                        <div class="mb-3">
                            <label for="to-account" class="form-label">Ke Akun</label>
                            <div class="position-relative">
                                <input type="text" class="form-control search-input-dropdown" placeholder="Akun Kas" aria-label="Akun Kas" aria-describedby="input-to-account"
                                onclick="showToAccountDropdown(this)" onkeyup="showToAccountDropdown(this)"
                                onchange="changeToAccountDropdown(this)" autocomplete="off" id="to-account-input">
                                <div class="d-none bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary" style="max-height: 130px; z-index:1" id="to-account-list">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label for="date-input" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="date-input" placeholder="Tanggal" onchange="changeDateInput(this)">
                            
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label for="no-ref-input" class="form-label">No Referensi</label>
                            <input type="text" class="form-control" id="no-ref-input" placeholder="No Referensi" onchange="noRefChange(this)">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label for="description-input" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="description-input" placeholder="Deskripsi" onchange="getNoRefAccountAuto(this)">
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="detail-input" class="form-label">Detail (optional) </label>
                            <input type="text" class="form-control" id="detail-input" placeholder="Detail" onchange="detailInputChange(this)">
                        </div>
                    </div>
                </div>

                {{-- list input Pendapatan  --}}
                {{-- Header --}}
                <div class="row justify-content-between border-top pt-2 border-bottom pb-2 font-bold">
                    <div class="col-5">
                        Akun
                    </div>
                    <div class="col-5 text-end">
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

                <div class="row justify-content-between pt-2 border-top mt-2 pb-2 font-bold">
                    <div class="col-5 text-end">
                        Total
                    </div>
                    <div class="col-5 text-end" id="total-label">
                       
                    </div>
                    <div class="col-1">
                        
                    </div>
                </div>

               
                <div class="row justify-content-end pt-2 border-bottom pb-2 font-bold">
                    <div class="col-lg-2 col-6 text-end">
                        <a class="btn btn-outline-primary w-100" href="{{ route('revenue.index') }}">Batal</a>
                    </div>  
                    <div class="col-lg-2 col-6 text-end d-none" id="btn-submit-revenue">
                        <button class="btn btn-primary w-100" onclick="submitRevenue()" id="btn-submit">
                            <span id="submit-button-label">
                                Ubah
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/admin/revenue/api.js"></script>
    <script src="/js/admin/revenue/edit.js"></script>
@endsection
