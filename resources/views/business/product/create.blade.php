@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="d-flex col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.product.index', $business->id) }}">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-3 col-12">
            <div class="card" id="input-card">
                <div class="card-header fs-4 fw-bold">{{ __('Tambah Produk') }}</div>

                <form action="{{ route('business.product.store', $business->id) }}" method="post" class="form-input">
                    @csrf
                    <div class="card-body">
                        <x-product.form 
                            :businessid="$business->id" 
                            :pemasok="$pemasok" 
                            :kategori="$kategori" 
                            :brand="$brand" 
                            :page="'create'"
                        />
                        <button class="btn btn-primary save-button" data-business-id="{{ $business['id'] }}" type="button">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="card d-none" id="edit-card">
                <div class="card-header fs-4 fw-bold">
                    <div class="row justify-content-between">
                        <div class="col">
                            Ubah Produk <button class="btn btn-sm btn-success" onclick="showAdd()">batal</button>
                        </div>
                    </div>
                </div>

                <form method="post" class="form-input">
                    @csrf
                    @method('patch')
                    <div class="card-body">
                        <x-product.form :businessid="$business->id" :pemasok="$pemasok" :kategori="$kategori" :brand="$brand" :page="'create'"/>
                        <button class="btn btn-primary save-button" data-business-id="{{ $business['id'] }}" type="button">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-9 col-12 mt-3 mt-md-0">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Input Hari Ini') }}</div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kode</th>
                                {{-- Khusus Retail --}}
                                @if ($business->kategori == 'Retail')
                                    <th>Brand</th>
                                @endif

                                <th>Kategori</th>

                                @if ($business->kategori == 'Retail')
                                    <th>Pemasok</th>
                                @endif

                                {{-- Khusus Retail --}}
                                @if ($business->kategori == 'Retail')
                                    <th>Modal</th> 
                                @endif

                                <th>Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($products->isNotEmpty())
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->nama_produk }}</td>
                                        <td>{{ $product->kode }}</td>
                                        {{-- Khusus Retail --}}
                                        @if ($business->kategori == 'Retail')
                                            <td>{{ $product->brand }}</td>
                                        @endif
                                        <td>{{ $product->kategori }}</td>

                                        @if ($business->kategori == 'Retail')
                                            <td>{{ $product->pemasok }}</td>
                                        @endif

                                        <td>Rp. {{ number_format($product->modal,0,",",".") }}</td>
                                        <td>Rp. {{ number_format($product->jual,0,",",".") }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $business->id }}, {{ $product->id }}, 'create')">hapus</button>
                                            <a href="#edit-card" class="btn btn-sm btn-secondary" onclick="editConfirmation({{ $business->id }}, {{ $product->id }})">ubah</a>
                                        </td>
                                    </tr>
                                @endforeach                                    
                            @else
                                <tr>
                                    <td class="text-center" colspan="8">
                                        <i>Tidak Ada Data</i>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end x-overflow-auto">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal cari pemasok --}}
    <div class="modal fade" id="cariPemasokModal" tabindex="-1" aria-labelledby="cariPemasokModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cariPemasokModalLabel">Cari Pemasok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-2" placeholder="Masukkan Nama Pemasok" id="input-supplier">
                    <div class="result">
                        <ol class="list-group" id="list-supplier">
                            
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal cari kategori --}}
    <div class="modal fade" id="cariKategoriModal" tabindex="-1" aria-labelledby="cariKategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cariKategoriModalLabel">Cari Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-2" placeholder="Masukkan Nama Kategori" id="input-kategori">
                    <div class="result">
                        <ol class="list-group" id="list-kategori">
                            
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal cari brand --}}
    <div class="modal fade" id="cariBrandModal" tabindex="-1" aria-labelledby="cariBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cariBrandModalLabel">Cari Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-2" placeholder="Masukkan Nama Brand" id="input-brand">
                    <div class="result">
                        <ol class="list-group" id="list-brand">
                            
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal  --}}
    <form method="post" id="delete-confirmation-form">
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                @csrf
                @method('delete')
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title text-center" id="deleteModalLabel">Anda Yakin Hapus Data Ini?</h3>
                    </div>
                    <input type="hidden" id="page-type" name="page">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger" id="submit-delete-button">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script type="text/javascript">
        const inputCard = document.getElementById('input-card');
        const editCard = document.getElementById('edit-card');

        const formInput = Array.from(document.getElementsByClassName('form-input'));

        const saveButtons = Array.from(document.getElementsByClassName('save-button'));
        const kategori = Array.from(document.getElementsByClassName('kategori'));
        const kode = Array.from(document.getElementsByClassName('kode'));
        const nama = Array.from(document.getElementsByClassName('nama'));
        const jual = Array.from(document.getElementsByClassName('jual'));
        const pemasok = Array.from(document.getElementsByClassName('pemasok'));
        const brand = Array.from(document.getElementsByClassName('brand'));
        const modal = Array.from(document.getElementsByClassName('modal-input'));
        const page = Array.from(document.getElementsByClassName('page'));

        const cariPemasok = Array.from(document.getElementsByClassName('cari-pemasok'));
        const cariKategori = Array.from(document.getElementsByClassName('cari-kategori'));
        const cariBrand = Array.from(document.getElementsByClassName('cari-brand'));
    
        const getSupplier = (businessId, supplier) => {
            let url= `/api/${businessId}/supplier?search=${supplier}`;

            return fetch(url)
                .then(response => response.json())
                .then(response => response.data)
        }

        cariPemasok.map((cari, index) => {
            cari.addEventListener('click', async function(){
                const inputSupplier = document.getElementById('input-supplier');
                const listSupplier = document.getElementById('list-supplier');

                inputSupplier.value = pemasok[index].value;

                let suppliers = await getSupplier(cari.dataset.businessId, inputSupplier.value);

                let list = '';

                suppliers.map(supplier => {
                    list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">${supplier.nama}</div>
                                    <small>${supplier.alamat}, ${supplier.no_hp ? supplier.no_hp : '-'} </small>
                                </div>
                                <button 
                                    class="btn btn-sm btn-primary" 
                                    onclick="selectSupplier('${supplier.nama}', ${index})"
                                    data-bs-dismiss="modal"
                                    >Pilih</button>
                                    
                            </li>`
                    });

                listSupplier.innerHTML = list;

                inputSupplier.addEventListener('keyup', async function(){
                    listSupplier.innerHTML = `<div class="d-flex justify-content-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>`;

                    let suppliers = await getSupplier(cari.dataset.businessId, inputSupplier.value);

                    let list = '';

                    suppliers.map(supplier => {
                        list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">${supplier.nama}</div>
                                        <small>${supplier.alamat}, ${supplier.no_hp ? supplier.no_hp : '-'} </small>
                                    </div>
                                    <button 
                                        class="btn btn-sm btn-primary" 
                                        onclick="selectSupplier('${supplier.nama}', ${index})"
                                        data-bs-dismiss="modal"
                                        >Pilih</button>
                                        
                                </li>`
                        });

                    listSupplier.innerHTML = list;
                })
            })
        })

        const selectSupplier = (supplier, index) => {
            pemasok[index].value = supplier;
        }

        const getKategori = (businessId, kategori) => {
            let url= `/api/${businessId}/category?search=${kategori}`;

            return fetch(url)
                .then(response => response.json())
                .then(response => response.data)
        }

        cariKategori.map((cari, index) => {
            cari.addEventListener('click', async function(){
                const inputKategori = document.getElementById('input-kategori');
                const listKategori = document.getElementById('list-kategori');

                inputKategori.value = kategori[index].value;

                let categories = await getKategori(cari.dataset.businessId, inputKategori.value);

                let list = '';

                categories.map(category => {
                    list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">${category.nama}</div>
                                </div>
                                <button 
                                    class="btn btn-sm btn-primary" 
                                    onclick="selectCategory('${category.nama}', ${index})"
                                    data-bs-dismiss="modal"
                                    >Pilih</button>
                                    
                            </li>`
                    });

                listKategori.innerHTML = list;

                inputKategori.addEventListener('keyup', async function(){
                    listKategori.innerHTML = `<div class="d-flex justify-content-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>`;

                                    let categories = await getKategori(cari.dataset.businessId, inputKategori.value);

                                    let list = '';

                                    categories.map(category => {
                                        list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                                    <div class="ms-2 me-auto">
                                                        <div class="fw-bold">${category.nama}</div>
                                                    </div>
                                                    <button 
                                                        class="btn btn-sm btn-primary" 
                                                        onclick="selectCategory('${category.nama}', ${index})"
                                                        data-bs-dismiss="modal"
                                                        >Pilih</button>
                                                        
                                                </li>`
                                        });

                                    listKategori.innerHTML = list;
                })
            })
        })

        const selectCategory = (category, index) => {
            kategori[index].value = category;
        }

        const getBrand = (businessId, brand) => {
            let url= `/api/${businessId}/brand?search=${brand}`;

            return fetch(url)
                .then(response => response.json())
                .then(response => response.data)
        }

        cariBrand.map((cari, index) => {
            cari.addEventListener('click', async function(){
                const inputBrand = document.getElementById('input-brand');
                const listBrand = document.getElementById('list-brand');

                inputBrand.value = brand[index].value;

                let brands = await getBrand(cari.dataset.businessId, inputBrand.value);
                let list = '';

                brands.map(brand => {
                    list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">${brand.nama}</div>
                                </div>
                                <button 
                                    class="btn btn-sm btn-primary" 
                                    onclick="selectBrand('${brand.nama}', ${index})"
                                    data-bs-dismiss="modal"
                                    >Pilih</button>
                                    
                            </li>`
                    });

                listBrand.innerHTML = list;

                inputBrand.addEventListener('keyup', async function(){
                    listBrand.innerHTML = `<div class="d-flex justify-content-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>`;

                                    let brands = await getBrand(cari.dataset.businessId, inputBrand.value);

                                    let list = '';

                                    brands.map(brand => {
                                        list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                                    <div class="ms-2 me-auto">
                                                        <div class="fw-bold">${brand.nama}</div>
                                                    </div>
                                                    <button 
                                                        class="btn btn-sm btn-primary" 
                                                        onclick="selectBrand('${brand.nama}', ${index})"
                                                        data-bs-dismiss="modal"
                                                        >Pilih</button>
                                                        
                                                </li>`
                                        });

                                    listBrand.innerHTML = list;
                })
            })
        })

        const selectBrand = (brandValue, index) => {
            brand[index].value = brandValue;
        }
        
        const editConfirmation = (business_id, product_id) => {
            inputCard.classList.add('d-none');
            editCard.classList.remove('d-none');

            formInput[1].setAttribute('action', `/${business_id}/product/${product_id}`);

            axios.get(`/api/${business_id}/product/${product_id}`)
            .then((res) => {
                nama[1].value = res.data.data.nama_produk;
                kategori[1].value = res.data.data.kategori;
                pemasok[1].value = res.data.data.pemasok;
                jual[1].value = res.data.data.jual;
                kode[1].value = res.data.data.kode;
                modal[1].value = res.data.data.modal;
                brand[1].value = res.data.data.brand;
                page[1].value = 'create';
            })
            .catch((err) => {
                console.log(err);
            })
        }

        const showAdd = () => {
            editCard.classList.add('d-none');
            inputCard.classList.remove('d-none');
        }

        saveButtons.map((saveButton, index) => {
            
            saveButton.addEventListener('click', function(){
                let data = {
                    "nama" : nama[index].value,
                    "kategori" : kategori[index].value,
                    "kode" : kode[index].value,
                    "jual" : jual[index].value,
                }
                axios.post(`/api/${saveButton.dataset.businessId}/product`, data)
                .then((res) => {
                    formInput[index].submit()
                })
                .catch((err) => {
                    if (err.response) {
                        if (err.response.data.errors.nama) {
                            nama[index].classList.add('is-invalid')
                        } else {
                            nama[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.kategori) {
                            kategori[index].classList.add('is-invalid')
                        } else {
                            kategori[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.kode) {
                            kode[index].classList.add('is-invalid')
                        } else {
                            kode[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.jual) {
                            jual[index].classList.add('is-invalid')
                        } else {
                            jual[index].classList.remove('is-invalid')
                        }
                    }
                })
            })
        })

        const deleteConfirmation = (businessId, productId, type) => {
            const deleteConfirmationForm = document.getElementById('delete-confirmation-form');
            const pageType = document.getElementById('page-type');

            pageType.value = type;

            deleteConfirmationForm.setAttribute('action', `/${businessId}/product/${productId}`);
        }

        window.addEventListener('load', function (){

        })
    </script>
@endsection
