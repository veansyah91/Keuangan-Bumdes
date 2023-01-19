@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading d-flex justify-content-between my-auto" data-business="{{ $business->id }}" id="content">
        <h3>Ubah Produk</h3>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('business.product.index', $business->id) }}">Produk
            </a></li>
              <li class="breadcrumb-item active" aria-current="page">Ubah Produk</li>
            </ol>
        </nav>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <input type="hidden" id="product-id" value="{{ $product->id }}">
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label for="name-input" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="name-input" placeholder="Nama Produk" onchange="changeProduct(this)" value="{{ $product->name }}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label for="code-input" class="form-label">Kode</label>
                            <input type="text" class="form-control" id="code-input" placeholder="Kode Produk" onchange="changeCode(this)" value="{{ $product->code }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label for="unit-input" class="form-label">Satuan</label>
                            <select class="form-select" id="unit-input" onchange="changeUnit(this)">
                                <option>-- Pilih Satuan --</option>
                                <option value="box" @if ($product->unit == 'box') selected @endif>Box</option>
                                <option value="cup"  @if ($product->unit == 'cup') selected @endif>Cup</option>
                                <option value="gram"  @if ($product->unit == 'gram') selected @endif>Gram</option>
                                <option value="jam"  @if ($product->unit == 'jam') selected @endif>Jam</option>
                                <option value="kilo"  @if ($product->unit == 'kilo') selected @endif>Kg</option>
                                <option value="pack"  @if ($product->unit == 'pack') selected @endif>Pack</option>
                                <option value="pcs"  @if ($product->unit == 'pcs') selected @endif>Pcs</option>
                            </select>                            
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 ">
                        <div class="mb-3 position-relative">
                            <label for="category-input" class="form-label">Kategori</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control search-input-dropdown" placeholder="Kategori" aria-label="Kategori" aria-describedby="create-category-input" list="dataCategory" onclick="showCategoryDropdown(this)" onkeyup="showCategoryDropdown(this)"
                                onchange="changeCategory(this)" id="category-input" autocomplete="off" value="{{ $product->category }}">
                                <button class="btn " type="button" id="create-category-input" onclick="showCategory()" data-bs-toggle="modal" data-bs-target="#createCategoryModal" data-bs-dismiss="modal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="bg-light position-absolute list-group w-100 d-none search-select overflow-auto custom-scroll border border-2 border-secondary" id="category-list" style="max-height: 130px">
                            
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label for="unit-price-input" class="form-label">Harga Beli</label>
                            <input type="text" class="form-control text-end" id="unit-price-input" placeholder="Harga Beli" onchange="changeUnitPrice(this)" autocomplete="off" onclick="this.select()" onkeyup="setCurrencyFormat(this)" value="{{ number_format($product->unit_price,0,',','.') }}" inputmode="numeric">
                            
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <label for="selling-price-input" class="form-label">Harga Jual</label>
                            <input type="text" class="form-control text-end" id="selling-price-input" placeholder="Harga Jual" onchange="changeSellingPrice(this)" autocomplete="off" onclick="this.select()" onkeyup="setCurrencyFormat(this)" value="{{ number_format($product->selling_price,0,',','.') }}" inputmode="numeric">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="mb-3 position-relative">
                            <label for="supplier-input" class="form-label">Pemasok</label>
                            <div class="input-group mb-2 ">
                                <input type="text" class="form-control search-input-dropdown" placeholder="Pemasok" aria-label="Pemasok" aria-describedby="create-supplier-input" list="datasupplier" onclick="showSupplierDropdown(this)" onkeyup="showSupplierDropdown(this)"
                                onchange="changeSupplier(this)" id="supplier-input" autocomplete="off" value="{{ $product->supplier }}">
                                <a class="btn" type="button" id="create-supplier-input" href="{{ route('business.contact.index', $business->id) }}" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="bg-light position-absolute list-group w-100 d-none search-select overflow-auto custom-scroll border border-2 border-secondary" id="supplier-list" style="max-height: 130px">
                            
                            </div>
                        </div>
                       
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is-stock-checked" @if ($product->is_stock_checked) checked="checked" @endif  onchange="changeStockChecked(this)">
                            <label class="form-check-label" for="is-stock-checked">Lacak Persediaan</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is-active-checked" @if ($product->is_active) checked="checked" @endif onchange="changeActiveChecked(this)">
                            <label class="form-check-label" for="is-active-checked">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-end pt-2 border-bottom pb-2 font-bold">
                    <div class="col-lg-2 col-6 text-end">
                        <a class="btn btn-outline-primary w-100" href="{{ route('business.product.index', $business->id) }}">Batal</a>
                    </div>  
                    <div class="col-lg-2 col-6 text-end d-none" id="btn-submit-product">
                        <button class="btn btn-primary w-100" onclick="submitProduct()" id="btn-submit">
                            <span id="submit-button-label">
                                Simpan
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategoryModalLabel">Buat Kategori Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <form onsubmit="storeCategory(event)">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="category-input" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="category-input-create" placeholder="Nama Kategori" onchange="changeCategoryCreateInput(this)" onkeyup="validateCategoryInput(this)">
                                <small class="text-danger d-none" id="validation-name-input"></small>
                            </div>
                        </div>
                        
                        <button class="btn btn-sm btn-primary disabled" disabled type="submit" id="submit-category">Tambah</button>
                    </form>
                    <div class="m-3 border-top overflow-auto custom-scroll" style="height: 250px;">
                        <table class="table">
                            <tbody id="category-account">
                                <tr>
                                    <td>Kas</td>
                                    <td class="text-end"><button class="btn btn-danger btn-sm"><i class="bi bi-trash"></button></td>
                                </tr>
                            </tbody>
                        </table>
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
    <script src="/js/business/product/api.js"></script>
    <script src="/js/business/product/edit.js"></script>
@endsection
