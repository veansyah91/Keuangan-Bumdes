@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Asset') }}</div>

                <div class="card-body">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Data</button>

                    <div class="row justify-content-center mt-2">
                        <div class="col-12 col-md-6 fs-5 fw-bold">
                            Nilai Asset: Rp. {{ number_format($sumAsset,0,",",".") }}
                        </div>
                        <div class="col-12 col-md-6 fs-5 fw-bold text-center text-md-end mt-2 mt-md-0">
                            <a href="{{ route('business.asset.excel', $business->id) }}" class="btn btn-success btn-sm"><i class="bi bi-file-spreadsheet-fill"></i>Excel</a>
                            <a href="{{ route('business.asset.pdf', $business->id) }}" class="btn btn-danger btn-sm"><i class="bi bi-file-pdf-fill"></i>PDF</a>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-2">
                        <div class="col-12 table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>Kode</th>
                                        <th>Nama Item</th>
                                        <th>Harga Satuan</th>
                                        <th>Jumlah Bagus</th>
                                        <th>Jumlah Rusak</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($assets->isNotEmpty())
                                        @foreach ($assets as $asset)
                                            <tr>
                                                <td>{{ $asset->kode }}</td>
                                                <td>{{ strtoupper($asset->name_item) }}</td>
                                                <td class="text-end">Rp. {{ number_format($asset->harga_satuan,0,",",".") }}</td>
                                                <td class="text-center">{{ $asset->jumlah_bagus }}</td>
                                                <td class="text-center">{{ $asset->jumlah_rusak }}</td>
                                                <td class="text-center">{{ $asset->tanggal_masuk }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $business->id }}, {{ $asset->id }})">hapus</button>
                                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editConfirmation({{ $business->id }}, {{ $asset->id }})">ubah</button>
                                                </td>
                                            </tr>
                                        @endforeach                                    
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="7">
                                                <i>Tidak Ada Data</i>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-end x-overflow-auto">
                                {{ $assets->links() }}
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Input Modal -->
    <form action="{{ route('business.asset.store', $business->id) }}" method="post" class="form-input">
        @csrf
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Asset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode</label>
                                <input type="text" class="form-control kode" id="kode" name="kode" aria-describedby="kodeHelp">
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Asset</label>
                                <input type="text" class="form-control nama" id="nama" name="nama" aria-describedby="namaHelp" >
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga Satuan</label>
                                <input type="number" class="form-control harga" id="harga" name="harga" aria-describedby="hargaHelp" >
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_bagus" class="form-label">Jumlah Bagus</label>
                                <input type="number" class="form-control jumlah-bagus" id="jumlah-bagus" name="jumlah_bagus" aria-describedby="jumlah_bagusHelp">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_rusak" class="form-label">Jumlah Rusak</label>
                                <input type="number" class="form-control jumlah-rusak" id="jumlah-rusak" name="jumlah_rusak" aria-describedby="jumlah_rusakHelp" min="0" value="0">
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control tanggal-masuk" id="tanggal-masuk" name="tanggal_masuk" aria-describedby="tanggal_masukHelp" min="0" value="0">
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary save-button">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Edit Modal -->
    <form method="post" class="form-input">
        @csrf 
        @method('patch')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Ubah Pelanggan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode</label>
                                <input type="text" class="form-control kode" id="kode" name="kode" aria-describedby="kodeHelp">
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Asset</label>
                                <input type="text" class="form-control nama" id="nama" name="nama" aria-describedby="namaHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga Satuan</label>
                                <input type="number" class="form-control harga" id="harga" name="harga" aria-describedby="hargaHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_bagus" class="form-label">Jumlah Bagus</label>
                                <input type="number" class="form-control jumlah-bagus" id="jumlah-bagus" name="jumlah_bagus" aria-describedby="jumlah_bagusHelp">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_rusak" class="form-label">Jumlah Rusak</label>
                                <input type="number" class="form-control jumlah-rusak" id="jumlah-rusak" name="jumlah_rusak" aria-describedby="jumlah_rusakHelp" min="0" value="0">
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control tanggal-masuk" id="tanggal-masuk" name="tanggal_masuk" aria-describedby="tanggal_masukHelp" min="0" value="0">
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary save-button">Ubah</button>
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

        const kode = Array.from(document.getElementsByClassName('kode'));
        const nama = Array.from(document.getElementsByClassName('nama'));
        const harga = Array.from(document.getElementsByClassName('harga'));
        const jumlahRusak = Array.from(document.getElementsByClassName('jumlah-rusak'));
        const jumlahBagus = Array.from(document.getElementsByClassName('jumlah-bagus'));
        const tanggalMasuk = Array.from(document.getElementsByClassName('tanggal-masuk'));
        

        saveButtons.map((saveButton, index) => {
            saveButton.addEventListener('click', function()
            {
                let data = {
                    "nama_item" : nama[index].value,
                    "harga_satuan" : harga[index].value,
                    "kode" : kode[index].value,
                    "jumlah_bagus" : jumlahBagus[index].value,
                    "jumlah_rusak" : jumlahRusak[index].value,
                    "tanggal_masuk" : tanggalMasuk[index].value,
                }
                // validasi
                axios.post(`/api/asset`, data)
                .then((res) => {
                    formInput[index].submit();
                })
                .catch((err) => {
                    if (err.response) {
                        if (err.response.data.errors.name_item) {
                            nama[index].classList.add('is-invalid')
                        } else {
                            nama[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.harga_satuan) {
                            harga[index].classList.add('is-invalid')
                        } else {
                            harga[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.tanggal_masuk) {
                            tanggalMasuk[index].classList.add('is-invalid')
                        } else {
                            tanggalMasuk[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.jumlah_bagus) {
                            jumlahBagus[index].classList.add('is-invalid')
                        } else {
                            jumlahBagus[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.jumlah_rusak) {
                            jumlahRusak[index].classList.add('is-invalid')
                        } else {
                            jumlahRusak[index].classList.remove('is-invalid')
                        }
                    }
                    
                    
                })
            })
        })

        const editConfirmation = (businessId, id) => {
            formInput[1].setAttribute('action', `/${businessId}/asset/${id}`);

            axios.get(`/api/asset/${id}`)
            .then((res) => {

                nama[1].value = res.data.data.name_item;
                kode[1].value = res.data.data.kode;
                harga[1].value = res.data.data.harga_satuan;
                jumlahBagus[1].value = res.data.data.jumlah_bagus;
                jumlahRusak[1].value = res.data.data.jumlah_rusak;
                tanggalMasuk[1].value = res.data.data.tanggal_masuk;
            })
            .catch((err) => {
                console.log(err);
            });
        }

        const deleteConfirmation = (businessId, id) => {
            const deleteConfirmationForm = document.getElementById('delete-confirmation-form');

            deleteConfirmationForm.setAttribute('action', `/${businessId}/asset/${id}`);
        }

        window.addEventListener('load', function (){
            
        })
    </script>
@endsection
