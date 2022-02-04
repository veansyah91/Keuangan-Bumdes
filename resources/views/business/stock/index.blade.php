@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Stok Barang') }}</div>

                <div class="card-body">
                    <div class="row justify-content-between">   
                        <div class="col-12 col-md-6">
                            <a class="btn btn-primary" href="{{ route('business.stock.create', $business->id) }}">Tambah Data</a>
                        </div>
                            <div class="col-12 col-md-6">
                                <form action="" method="get">

                                    <div class="input-group mb-3">                                        
                                            <input type="text" class="form-control" placeholder="Masukkan Kode / Nama Produk" aria-label="Masukkan Kode / Nama Produk" aria-describedby="button-addon2" name="search" value="{{ request('search') }}">
                                            <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Cari</button>                                        
                                    </div>
                                </form>
                            </div>

                    </div>

                    <div class="row justify-content-center">
                        <div class="col-12 table-responsive">
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
                                        <th>Nomor Nota</th>
                                        <th>Tanggal Nota</th>
                                        <th>Tanggal Masuk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($stocks->isNotEmpty())
                                        @foreach ($stocks as $stock)
                                            <tr>
                                                <td>
                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editConfirmation({{ $business->id }}, {{ $stock->id }})">ubah</button>
                                                    @if (!IncomingItemHelper::getData($stock->incomingitem_id))
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $business->id }}, {{ $stock->id }})">Hapus</button>
                                                    @endif
                                                </td>
                                                <td>{{ $stock->product->kode }}</td>
                                                <td>{{ $stock->product->nama_produk }}</td>
                                                <td>{{ $stock->jumlah }}</td>
                                                <td>Rp. {{ number_format($stock->product->modal,0,",",".") }}</td>
                                                <td>Rp. {{ number_format($stock->product->jual,0,",",".") }}</td>
                                                <td>{{ $stock->product->brand }}</td>
                                                <td>{{ $stock->product->kategori }}</td>
                                                <td>{{ $stock->product->pemasok }}</td>
                                                <td>{{ IncomingItemHelper::getData($stock->incomingitem_id)['nomor_nota'] }}</td>
                                                <td>{{ IncomingItemHelper::getData($stock->incomingitem_id)['tanggal_nota'] }}</td>
                                                <td>{{ IncomingItemHelper::getData($stock->incomingitem_id)['tanggal_masuk'] }}</td>
                                                
                                            </tr>
                                        @endforeach                                    
                                    @else
                                        <tr>
                                            <td class="text-center">
                                                <i>Tidak Ada Data</i>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-end x-overflow-auto">
                                {{ $stocks->links() }}
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <form method="post" class="form-input">
        @csrf 
        @method('patch')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Ubah Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <x-product.form :businessid="$business->id" :pemasok="null" :kategori="null" :brand="null" :page="'edit'"/>                            
                            <x-stock.form />
                            <button class="btn btn-primary save-button" data-business-id="{{ $business['id'] }}" type="button">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

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

        const saveButtons = Array.from(document.getElementsByClassName('save-button'));
        const formInput = Array.from(document.getElementsByClassName('form-input'));

        const kategori = Array.from(document.getElementsByClassName('kategori'));
        const kode = Array.from(document.getElementsByClassName('kode'));
        const nama = Array.from(document.getElementsByClassName('nama'));
        const jual = Array.from(document.getElementsByClassName('jual'));
        const pemasok = Array.from(document.getElementsByClassName('pemasok'));
        const brand = Array.from(document.getElementsByClassName('brand'));
        const modal = Array.from(document.getElementsByClassName('modal-input'));
        const page = Array.from(document.getElementsByClassName('page'));
        const jumlah = Array.from(document.getElementsByClassName('jumlah'));
        const satuan = Array.from(document.getElementsByClassName('satuan'));

        const cariPemasok = Array.from(document.getElementsByClassName('cari-pemasok'));
        const cariKategori = Array.from(document.getElementsByClassName('cari-kategori'));
        const cariBrand = Array.from(document.getElementsByClassName('cari-brand'));

        saveButtons.map((saveButton, index) => {
            
            saveButton.addEventListener('click', function(){
                let data = {
                    "nama" : nama[index].value,
                    "kategori" : kategori[index].value,
                    "kode" : kode[index].value,
                    "jual" : jual[index].value,
                    "jumlah" : jumlah[index].value,
                    "satuan" : satuan[index].value,
                }
                axios.post(`/api/${saveButton.dataset.businessId}/stock`, data)
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

                        if (err.response.data.errors.jumlah) {
                            jumlah[index].classList.add('is-invalid')
                        } else {
                            jumlah[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.satuan) {
                            satuan[index].classList.add('is-invalid')
                        } else {
                            satuan[index].classList.remove('is-invalid')
                        }
                    }
                })
            })
        })

        const editConfirmation = (business_id, stock_id) => {

            formInput[0].setAttribute('action', `/${business_id}/stock/${stock_id}`);

            axios.get(`/api/${business_id}/stock/${stock_id}`)
            .then((res) => {
                nama[0].value = res.data.data.product.nama_produk;
                kategori[0].value = res.data.data.product.kategori;
                pemasok[0].value = res.data.data.product.pemasok;
                jual[0].value = res.data.data.product.jual;
                kode[0].value = res.data.data.product.kode;
                modal[0].value = res.data.data.product.modal;
                brand[0].value = res.data.data.product.brand;
                satuan[0].value = res.data.data.stock.satuan;
                jumlah[0].value = res.data.data.stock.jumlah;
                
                page[0].value = 'edit';
                console.log(res);
            })
            .catch((err) => {
                console.log(err);
            })
        }

        const deleteConfirmation = (businessId, stockId) => {
            const deleteConfirmationForm = document.getElementById('delete-confirmation-form');
            const pageType = document.getElementById('page-type');

            deleteConfirmationForm.setAttribute('action', `/${businessId}/stock/${stockId}`);
        }

        window.addEventListener('load', function (){
            
        })
    </script>
@endsection
