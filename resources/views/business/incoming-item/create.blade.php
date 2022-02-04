@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="d-flex col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.incoming-item.index', $business->id) }}">Barang Masuk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="card" id="create-card">
                <div class="card-header fs-4 fw-bold">{{ __('Tambah') }}</div>
                <div class="card-body">
                    <form action="{{ route('business.incoming-item.store', $business->id) }}" method="post" class="form-input">
                        @csrf
                        <x-incoming-item.form :incomingitemid="$incomingItem['id']" :businessid="$business->id"/>
                        <div class="d-flex justify-content-center text-center mb-2">
                            <div class="col-12 fw-bold">
                                Produk
                            </div>
                        </div>
                        <x-product.form 
                            :businessid="$business->id" 
                            :pemasok="$pemasok" 
                            :kategori="$kategori" 
                            :brand="$brand" 
                            :page="'create'"
                        />
                        <hr>
                        <x-stock.form/>
                        <button class="btn btn-primary save-button" data-business-id="{{ $business['id'] }}" type="button">Simpan</button>
                    </form>
                </div>
            </div>
            <div class="card d-none" id="edit-card">
                <div class="card-header fs-4 fw-bold">
                    <div class="row justify-content-between">
                        <div class="col">{{ __('Ubah') }}</div>
                        <div class="col text-right">
                            <button class="btn btn-sm btn-secondary" onclick="cancelEdit()">batal</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" class="form-input">
                        @csrf
                        @method('patch')
                        <x-incoming-item.form :incomingitemid="$incomingItem['id']" :businessid="$business->id"/>
                        <div class="d-flex justify-content-center text-center mb-2">
                            <div class="col-12 fw-bold">
                                Produk
                            </div>
                        </div>
                        <x-product.form 
                            :businessid="$business->id" 
                            :pemasok="$pemasok" 
                            :kategori="$kategori" 
                            :brand="$brand" 
                            :page="'create'"
                        />
                        <hr>
                        <x-stock.form/>
                        <button class="btn btn-primary save-button" data-business-id="{{ $business['id'] }}" type="button">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Barang Masuk') }}</div>
                <div class="card-body">
                    @if ($incomingItem)
                        <div class="row">
                            <div class="col-6">
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <tr>
                                                <th>Nomor Nota</th>
                                                <td>: {{ $incomingItem['nomor_nota'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Nota</th>
                                                <td>: {{ $incomingItem['tanggal_nota'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Masuk</th>
                                                <td>: {{ $incomingItem['tanggal_masuk'] }}</td>
                                            </tr> 
                                            <tr>
                                                <th>Total</th>
                                                <td>: Rp. {{ number_format($incomingItem['jumlah'],0,",",".") }}</td>
                                            </tr> 
                                        </tr>
                                    </tbody>                                
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Kode / SN</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah</th>
                                        <th>Modal</th>
                                        <th>Jual</th>
                                        <th>Brand</th>
                                        <th>Kategori</th>
                                        <th>Pemasok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($stocks->isNotEmpty())
                                        @foreach ($stocks as $stock)
                                            <tr>
                                                <td>
                                                    <button class="btn btn-sm btn-secondary" onclick="showEdit({{ $business['id'] }}, {{ $stock->id }})">Ubah</button>
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $business->id }}, {{ $stock->id }})">Hapus</button>
                                                </td>
                                                <td>{{ $stock->product->kode }}</td>
                                                <td>{{ $stock->product->nama_produk }}</td>
                                                <td>{{ $stock->jumlah }} {{ $stock->satuan }}</td>
                                                <td>Rp. {{ number_format($stock->product->modal,0,",",".") }}</td>
                                                <td>Rp. {{ number_format($stock->product->jual,0,",",".") }}</td>
                                                <td>{{ $stock->product->brand }}</td>
                                                <td>{{ $stock->product->kategori }}</td>
                                                <td>{{ $stock->product->pemasok }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="text-center">
                                            <td colspan="9">
                                                <i>Tidak Ada Data</i>
                                            </td>
                                        </tr>
                                    @endif
                                    
                                </tbody>
                            </table>
                        </div>
                        
                    @else
                        <center>
                            <i>Belum Ada Data</i>
                        </center>
                    @endif
                    
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
        const inputCard = document.getElementById('create-card');
        const editCard = document.getElementById('edit-card');

        const formInput = Array.from(document.getElementsByClassName('form-input'));

        const saveButtons = Array.from(document.getElementsByClassName('save-button'));

        const nomor = Array.from(document.getElementsByClassName('nomor'));
        const tanggalNota = Array.from(document.getElementsByClassName('tanggal-nota'));
        const tanggalMasuk = Array.from(document.getElementsByClassName('tanggal-masuk'));
        const kategori = Array.from(document.getElementsByClassName('kategori'));
        const kode = Array.from(document.getElementsByClassName('kode'));
        const nama = Array.from(document.getElementsByClassName('nama'));
        const jual = Array.from(document.getElementsByClassName('jual'));
        const pemasok = Array.from(document.getElementsByClassName('pemasok'));
        const brand = Array.from(document.getElementsByClassName('brand'));
        const modal = Array.from(document.getElementsByClassName('modal-input'));
        const page = Array.from(document.getElementsByClassName('page'));
        const satuan = Array.from(document.getElementsByClassName('satuan'));
        const jumlah = Array.from(document.getElementsByClassName('jumlah'));

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
                    "modal" : modal[index].value,
                    "brand" : brand[index].value,
                    "pemasok" : pemasok[index].value,
                    "nomor" : nomor[index].value,
                    "tanggalMasuk" : tanggalMasuk[index].value,
                    "tanggalNota" : tanggalNota[index].value,
                    "satuan" : satuan[index].value,
                    "jumlah" : jumlah[index].value,
                }
                axios.post(`/api/${saveButton.dataset.businessId}/incoming-item`, data)
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

                        if (err.response.data.errors.modal) {
                            modal[index].classList.add('is-invalid')
                        } else {
                            modal[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.brand) {
                            brand[index].classList.add('is-invalid')
                        } else {
                            brand[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.pemasok) {
                            pemasok[index].classList.add('is-invalid')
                        } else {
                            pemasok[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.nomor) {
                            nomor[index].classList.add('is-invalid')
                        } else {
                            nomor[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.tanggalNota) {
                            tanggalNota[index].classList.add('is-invalid')
                        } else {
                            tanggalNota[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.tanggalMasuk) {
                            tanggalMasuk[index].classList.add('is-invalid')
                        } else {
                            tanggalMasuk[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.satuan) {
                            satuan[index].classList.add('is-invalid')
                        } else {
                            satuan[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.jumlah) {
                            jumlah[index].classList.add('is-invalid')
                        } else {
                            jumlah[index].classList.remove('is-invalid')
                        }
                    }
                })
            })
        })

        const deleteConfirmation = (businessId, stockId) => {
            const deleteConfirmationForm = document.getElementById('delete-confirmation-form');

            deleteConfirmationForm.setAttribute('action', `/${businessId}/incoming-item/stock/${stockId}`);
        }

        const showEdit = (businessId, stockId) => {
            inputCard.classList.add('d-none');
            editCard.classList.remove('d-none');

            formInput[1].setAttribute('action', `/${businessId}/incoming-item/stock/${stockId}`);

            axios.get(`/api/incoming-item/stock/${stockId}`)
            .then(res => {

                // Nota
                nomor[1].value = res.data.data.incomingItem.nomor_nota;
                tanggalNota[1].value = res.data.data.incomingItem.tanggal_nota;
                tanggalMasuk[1].value = res.data.data.incomingItem.tanggal_masuk;

                // produk
                nama[1].value = res.data.data.product.nama_produk;
                kategori[1].value = res.data.data.product.kategori;
                kode[1].value = res.data.data.product.kode;
                jual[1].value = res.data.data.product.jual;
                modal[1].value = res.data.data.product.modal;
                brand[1].value = res.data.data.product.brand;
                pemasok[1].value = res.data.data.product.pemasok;

                // stok
                satuan[1].value = res.data.data.stock.satuan;
                jumlah[1].value = res.data.data.stock.jumlah;
            })
            .catch(err => {
                console.log(err);
            })
        }

        const cancelEdit = () => {
            editCard.classList.add('d-none');
            inputCard.classList.remove('d-none');
        }

        window.addEventListener('load', function (){

        })
    </script>
@endsection
