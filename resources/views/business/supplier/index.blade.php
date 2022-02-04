@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Pemasok') }}</div>

                <div class="card-body">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Data</button>


                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Nomor Hp</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($suppliers->isNotEmpty())
                                        @foreach ($suppliers as $supplier)
                                            <tr>
                                                <td>{{ $supplier->nama }}</td>
                                                <td>{{ $supplier->alamat }}</td>
                                                <td>{{ $supplier->no_hp }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $business->id }}, {{ $supplier->id }})">hapus</button>
                                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editConfirmation({{ $business->id }}, {{ $supplier->id }})">ubah</button>
                                                </td>
                                            </tr>
                                        @endforeach                                    
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="4">
                                                <i>Tidak Ada Data</i>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-end x-overflow-auto">
                                {{ $suppliers->links() }}
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Input Modal -->
    <form action="{{ route('business.supplier.store', $business->id) }}" method="post" id="form-input">
        @csrf
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Pemasok</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <input type="hidden" id="business-id" value="{{ $business->id }}">
                                <label for="nama" class="form-label">Nama Pemasok</label>
                                <input type="text" class="form-control nama" id="nama" name="nama" aria-describedby="namaHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Pemasok</label>
                                <input type="text" class="form-control alamat" id="alamat" name="alamat" aria-describedby="alamatHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP Pemasok</label>
                                <input type="number" class="form-control no_hp" id="no_hp" name="no_hp" aria-describedby="no_hpHelp">
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button save-button" class="btn btn-primary" id="save-button">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Edit Modal -->
    <form method="post" id="form-edit">
        @csrf 
        @method('patch')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Ubah Pemasok</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <input type="hidden" id="business-id-edit" value="{{ $business->id }}">
                                <label for="nama" class="form-label">Nama Pemasok</label>
                                <input type="text" class="form-control nama" id="nama-edit" name="nama" aria-describedby="namaHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Pemasok</label>
                                <input type="text" class="form-control alamat" id="alamat-edit" name="alamat" aria-describedby="alamatHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP Pemasok</label>
                                <input type="number" class="form-control no_hp" id="no-hp-edit" name="no_hp" aria-describedby="no_hpHelp">
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button save-button" class="btn btn-primary" id="update-button">Simpan</button>
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

        const saveButton = document.getElementById('save-button');
        const updateButton = document.getElementById('update-button');
        const formInput = document.getElementById('form-input');
        const formEdit = document.getElementById('form-edit');

        saveButton.addEventListener('click', function()
        {
            const nama = document.getElementById('nama');
            const alamat = document.getElementById('alamat');
            const no_hp = document.getElementById('no_hp');

            const businessId = document.getElementById('business-id');
            let data = {
                "nama" : nama.value,
                "alamat" : alamat.value,
                "no_hp" : no_hp.value,
            }
            // validasi
            axios.post(`/api/${businessId.value}/supplier`, data)
            .then((res) => {
                console.log(res);
                formInput.submit()
            })
            .catch((err) => {
                console.log(err.response.data.errors);
                if (err.response.data.errors.nama) {
                    nama.classList.add('is-invalid')
                } else {
                    nama.classList.remove('is-invalid')
                }

                if (err.response.data.errors.alamat) {
                    alamat.classList.add('is-invalid')
                } else {
                    alamat.classList.remove('is-invalid')
                }

                if (err.response.data.errors.no_hp) {
                    no_hp.classList.add('is-invalid')
                } else {
                    no_hp.classList.remove('is-invalid')
                }
                
            })
        })

        updateButton.addEventListener('click', function(){
            const nama = document.getElementById('nama-edit');
            const alamat = document.getElementById('alamat-edit');
            const no_hp = document.getElementById('no-hp-edit');
            const businessId = document.getElementById('business-id-edit');

            let data = {
                "nama" : nama.value,
                "alamat" : alamat.value,
                "no_hp" : no_hp.value,
            }
            // validasi
            axios.post(`/api/${businessId.value}/supplier`, data)
            .then((res) => {
                formEdit.submit()
            })
            .catch((err) => {
                if (err.response)
                {
                    if (err.response.data.errors.nama) {
                        nama.classList.add('is-invalid')
                    } else {
                        nama.classList.remove('is-invalid')
                    }

                    if (err.response.data.errors.alamat) {
                        alamat.classList.add('is-invalid')
                    } else {
                        alamat.classList.remove('is-invalid')
                    }

                    if (err.response.data.errors.no_hp) {
                        no_hp.classList.add('is-invalid')
                    } else {
                        no_hp.classList.remove('is-invalid')
                    }
                }
                
                
            })
        })

        const editConfirmation = (businessId, id) => {
            formEdit.setAttribute('action', `/${businessId}/supplier/${id}`);

            axios.get(`/api/${businessId}/supplier/${id}`)
            .then((res) => {
                const nama = document.getElementById('nama-edit');
                const alamat = document.getElementById('alamat-edit');
                const no_hp = document.getElementById('no-hp-edit');
                const businessId = document.getElementById('business-id-edit');

                nama.value = res.data.data.nama;
                alamat.value = res.data.data.alamat;
                no_hp.value = res.data.data.no_hp;
                businessId.value = res.data.data.business_id;
            })
            .catch((err) => {
                console.log(err);
            });
        }

        const deleteConfirmation = (businessId, id) => {
            const deleteConfirmationForm = document.getElementById('delete-confirmation-form');

            deleteConfirmationForm.setAttribute('action', `/${businessId}/supplier/${id}`);
        }

        window.addEventListener('load', function (){
            
        })
    </script>
@endsection
