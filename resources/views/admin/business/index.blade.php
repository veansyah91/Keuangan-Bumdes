@extends('layouts.admin')

@section('admin')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Unit Usaha</h1>
    </div>

    <div class="page-content">

        <div class="border-bottom py-2 mb-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Data</button>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <table id="myTable" class="table table-striped ">
                    <thead class="table-primary">
                        <tr>
                            <th>Nama Usaha</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($businesses->isNotEmpty())
                            @foreach ($businesses as $business)
                                <tr>
                                    <td>{{ $business->nama }}</td>
                                    <td>{{ $business->kategori }}</td>
                                    <td class="@if ($business->status == 'active') text-primary
                                        @else
                                            text-danger
                                        @endif">{{ $business->status }}</td>
                                    
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ubahModal" onclick="ubahModalFunc({{ $business->id }})"><i class="bi bi-pencil-square"></i></button>
                                        <a href="{{ route('business.dashboard', $business->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-door-open"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="text-center fst-italic">
                                <td colspan="3">Data Kosong</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>

    <!-- Input Modal -->
    <form action="{{ route('business.store') }}" method="post">
        @csrf
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Unit Usaha</label>
                                <input type="text" class="form-control" id="nama" name="nama" aria-describedby="namaHelp" required>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="tanggal_masuk" class="form-label">Jenis Usaha</label>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="kategori" id="kategori5" value="Lainnya" checked>
                                            <label class="form-check-label" for="kategori5">
                                                Non Credit
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="kategori" id="kategori6" value="Kredit" disabled>
                                            <label class="form-check-label" for="kategori6">
                                                Credit 
                                            </label>
                                            <small class="badge bg-danger">Segera</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Edit Modal -->
    <form method="post" id="edit-form">
        @csrf
        @method('patch')
        <div class="modal fade" id="ubahModal" tabindex="-1" aria-labelledby="ubahModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ubahModalLabel">Ubah Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Unit Usaha</label>
                                <input type="text" class="form-control" id="nama-edit" name="nama" aria-describedby="namaHelp" required>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="tanggal_masuk" class="form-label">Jenis Usaha</label>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-edit" type="radio" name="kategori" id="kategori-edit-1" value="Retail">
                                            <label class="form-check-label" for="kategori-edit-1">
                                                Retail
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-edit" type="radio" name="kategori" id="kategori-edit-2" value="Restoran">
                                            <label class="form-check-label" for="kategori-edit-2">
                                                Restoran / Cafe
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-edit" type="radio" name="kategori" id="kategori-edit-3" value="Pulsa">
                                            <label class="form-check-label" for="kategori-edit-3">
                                                Pulsa
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-edit" type="radio" name="kategori" id="kategori-edit-4" value="Restoran">
                                            <label class="form-check-label" for="kategori-edit-4">
                                                Kredit
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-edit" type="radio" name="kategori" id="kategori-edit-5" value="Lainnya">
                                            <label class="form-check-label" for="kategori-edit-5">
                                                Lainnya
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3 row">
                                <label for="status" class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-10">
                                    <select class="form-select" aria-label="Default select example" id="status" name="status">
                                        <option value="active">Active</option>
                                        <option value="deactive">Deactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Ubah</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
@endsection

@section('script')
    <script type="text/javascript">

        const getData = (id) => {
            let url= `/api/business/${id}`;

            return fetch(url)
                .then(response => response.json())
                .then(response => response.data)
        }

        const ubahModalFunc = async (id) => {
            const editForm = document.getElementById('edit-form');
            const namaEdit = document.getElementById('nama-edit');
            const kategoriEdit = Array.from(document.getElementsByClassName('kategori-edit'));
            const status = document.getElementById('status');

            editForm.setAttribute('action', `/business/${id}`);

            let data = await getData(id);

            namaEdit.value = data.nama;

            kategoriEdit.map(ke => {
                if (ke.value == data.kategori) {
                    ke.setAttribute('checked' , '')
                }
            });

            status.value = data.status;
        }

        const deleteConfirmation = (id) => {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.setAttribute('action', `/business/${id}`);
        }

        window.addEventListener('load', function (){
            
        })
    </script>
@endsection

