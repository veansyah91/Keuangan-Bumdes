@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Kontak</h3>

        <div class="d-flex">
            <form class="d-flex input-group mb-3" onsubmit="searchForm(event)">
                <input type="text" class="form-control" placeholder="Cari" aria-label="Cari" aria-describedby="search-input" id="search-input">
                <button class="btn btn-outline-secondary btn-sm" type="submit" id="search-button"><i class="bi bi-search"></i></button>
            </form>
            
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
                    <div style="width:20%" class="px-2 my-auto">Nama</div>
                    <div style="width:10%" class="px-2 my-auto">No. Referensi</div>
                    <div style="width:10%" class="px-2 my-auto">Tipe</div>
                    <div style="width:20%" class="px-2 my-auto">Telepon</div>
                    
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
                    <h4>Hapus Data Kontak ?</h4>
                    
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showDetailModalLabel">Detail Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <h4 id="name-detail"></h4>
                        <p id="type-detail"></p>
                    </div>
                    <div>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-detail" type="button" role="tab" aria-controls="home-detail" aria-selected="true">General</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-detail" type="button" role="tab" aria-controls="detail-detail" aria-selected="false">Detail</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home-detail" role="tabpanel" aria-labelledby="home-tab">
                                <div class="mt-3">
                                    <div class="fw-bold">Email</div>
                                    <div id="email-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Telepon</div>
                                    <div id="phone-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Alamat</div>
                                    <div id="address-detail"></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="detail-detail" role="tabpanel" aria-labelledby="detail-tab">
                                <div class="mt-3">
                                    <div class="fw-bold">No KK</div>
                                    <div id="nkk-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">NIK</div>
                                    <div id="nik-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Desa</div>
                                    <div id="village-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Kecamatan</div>
                                    <div id="district-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Kabupaten</div>
                                    <div id="regency-detail"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold">Provinsi</div>
                                    <div id="province-detail"></div>
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
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">General</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab" aria-controls="detail" aria-selected="false">Detail</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="mb-3 mt-2">
                                    <label for="name" class="form-label fw-bold">Nama</label>
                                    <input type="text" class="form-control" id="name-input" placeholder="Nama" required onchange="nameInputChange(this)">
                                </div>
                                <div class="mb-3">
                                    <label for="type" class="form-label fw-bold">Tipe</label>
                                    <select class="form-select" aria-label="Default select example" id="type-input" required onchange="typeInputChange(this)">
                                        <option value="Customer">Customer</option>
                                        <option value="Staff">Staff</option>
                                        <option value="Supplier">Supplier</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="no-ref" class="form-label fw-bold">No Ref</label>
                                    <input type="text" class="form-control" id="no-ref-input" placeholder="CUST-001" onchange="noRefInputChange(this)">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" id="email-input" placeholder="email@email.com" onchange="emailInputChange(this)">
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-bold">Telepon/HP</label>
                                    <input type="tel" class="form-control" id="phone-input" placeholder="Telepon/HP" onchange="phoneInputChange(this)">
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label fw-bold">Alamat</label>
                                    <textarea class="form-control" name="address" id="address-input" cols="30" rows="3" placeholder="Alamat" onchange="addressInputChange(this)"></textarea>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                                <div class="mb-3">
                                    <label for="nkk" class="form-label fw-bold">No KK</label>
                                    <input type="text" class="form-control" id="nkk-input" placeholder="No KK" onchange="nkkInputChange(this)">
                                </div>
                                <div class="mb-3">
                                    <label for="nik" class="form-label fw-bold">NIK</label>
                                    <input type="text" class="form-control" id="nik-input" placeholder="NIK" onchange="nikInputChange(this)">
                                </div>
                                <div class="position-relative mb-3 z-index-1">
                                    <div class="">
                                        <label for="village-input" class="form-label fw-bold">Desa</label>
                                        <div class="mb-1">
                                            <input type="text" class="form-control search-input-dropdown" placeholder="Alamat KTP" aria-label="Alamat KTP" aria-describedby="create-address" onclick="showAddressDropdown(this)" onkeyup="changeAddressDropdown(this)" onchange="changeAddress(this)" id="village-input" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary d-none" id="address-list" style="max-height: 130px">
                                        
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="district-input" class="form-label fw-bold">Kecamatan</label>
                                    <input type="text" class="form-control" id="district-input" placeholder="Kecamatan" onchange="districtInputChange(this)">
                                </div>
                                <div class="mb-3">
                                    <label for="regency-input" class="form-label fw-bold">Kabupaten</label>
                                    <input type="text" class="form-control" id="regency-input" placeholder="Kabupaten" onchange="regencyInputChange(this)">
                                </div>
                                <div class="mb-3">
                                    <label for="province-input" class="form-label fw-bold">Provinsi</label>
                                    <input type="text" class="form-control" id="province-input" placeholder="Provinsi" onchange="provinceInputChange(this)">
                                </div>
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
    
@endsection

@section('script')
    <script src="/js/admin/contact/index.js"></script>
    <script src="/js/admin/contact/api.js"></script>
@endsection
