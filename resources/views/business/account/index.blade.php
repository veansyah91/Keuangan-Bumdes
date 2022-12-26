@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection


@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Akun</h3>
        <div>
            <form class="d-flex input-group mb-3" onsubmit="searchForm(event)">
                <input type="text" class="form-control" placeholder="Cari" aria-label="Cari" aria-describedby="search-input" id="search-input">
                <button class="btn btn-outline-secondary btn-sm" type="submit" id="search-button"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="d-none d-md-block">
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal" onclick="addData()">Tambah Data</button>
        </div>
        <div class="fixed-bottom text-end mb-3 mr-3 d-md-none ">
            <button class="btn btn-primary rounded-circle" type="button" data-bs-toggle="modal" data-bs-target="#createModal" onclick="addData()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="text-end">
                    <small class="fst-italic">
                        Jumlah Data : <span id="count-data"></span>
                    </small>
                </div>
                
                <div class="border border-top border-bottom">
                    <div class="d-md-flex d-none justify-content-between font-bold  py-2 px-1 border border-bottom">
                        <div style="width:1%" class="px-2">
                        </div>
                        <div style="width:10%" class="px-2">Kode</div>
                        <div style="width:30%" class="px-2">Nama</div>
                        <div style="width:20%" class="px-2">Kategori</div>
                        <div style="width:20%" class="px-2">Status</div>
                    </div>
            
                    <div style="height: 350px;" class="overflow-auto custom-scroll" id="list-data">
                        
                    </div>
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

    {{-- Create Classification --}}
    <div class="modal fade" id="createSubCategoryModal" tabindex="-1" aria-labelledby="createSubCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSubCategoryModalLabel">Buat Sub Klasifikasi Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <form onsubmit="storeSubClassificationAccount(event)">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="sub-classification-input" class="form-label">Sub Klasifikasi</label>
                                <input type="text" class="form-control" id="sub-classification-input" placeholder="Nama Akun">
                                <small class="text-danger d-none" id="validation-name-input"></small>
                            </div>
                            <div class="col-6">
                                <label for="sub-classification-code" class="form-label">Kode</label>
                                <input type="text" class="form-control" id="sub-classification-code" placeholder="Kode Akun">
                                <small class="text-danger d-none" id="validation-name-code"></small>

                            </div>
                        </div>
                        
                        <button class="btn btn-sm btn-primary" type="submit">Tambah</button>
                    </form>
                    <div class="m-3 border-top overflow-auto custom-scroll" style="height: 250px;">
                        <table class="table">
                            <tbody id="sub-classification-account">
                                <tr>
                                    <td>Kas</td>
                                    <td class="text-end"><button class="btn btn-danger btn-sm"><i class="bi bi-trash"></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createModal" data-bs-dismiss="modal">Kembali</button>
                </div>
           </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/business/account/index.js">
       
    </script>
@endsection
