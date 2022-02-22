@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="d-flex col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.dashboard', $business->id) }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aliran Kas</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-10 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 col-md-6  fs-4 fw-bold">
                            Aliran Kas
                        </div>

                        @role('ADMIN')
                        <div class="col-12 col-md-6 text-end">
                            <Button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Data</Button>
                        </div>
                        @endrole
                    </div>
                </div>

                <div class="card-body">
                    
                    <div class="row justify-content-center">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Uang Masuk</th>
                                    <th>Uang Keluar</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($businessBalanceActivities->isNotEmpty())
                                    @foreach ($businessBalanceActivities as $businessBalanceActivity)
                                        <tr>
                                            <td class="text-center">{{ $businessBalanceActivity->tanggal }}</td>
                                            <td class="text-center">{{ $businessBalanceActivity->keterangan }}</td>
                                            <td class="text-end">Rp. {{ $businessBalanceActivity->uang_masuk ? number_format($businessBalanceActivity->uang_masuk,0,",",".") : 0 }}</td>
                                            <td class="text-end">Rp. {{ $businessBalanceActivity->uang_keluar ? number_format($businessBalanceActivity->uang_keluar,0,",",".") : 0 }}</td>
                                            <td>
                                                @role('ADMIN')
                                                    @if ($businessBalanceActivity->bumdes)
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-link btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <button class="dropdown-item btn btn-outline-danger delete-business-balance-activity" data-bs-toggle="modal" data-bs-target="#deleteModal" data-business-id="{{ $business->id }}" data-id="{{ $businessBalanceActivity->id }}">
                                                                        <i class="bi bi-trash-fill text-danger"></i>
                                                                        Hapus
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <button class="dropdown-item btn btn-outline-danger edit-business-balance-activity" data-bs-toggle="modal" data-bs-target="#editModal" data-business-id="{{ $business->id }}" data-id="{{ $businessBalanceActivity->id }}">
                                                                        <i class="bi bi-pencil-square text-success" ></i>
                                                                        Ubah
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    
                                                @endrole
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="4">Tidak Ada Data</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-end x-overflow-auto">
                            {{ $businessBalanceActivities->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Input Modal -->
    <form action="{{ route('business.business-balance-activity.store', $business->id) }}" method="post" class="form-input">
        @csrf
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control tanggal" id="tanggal" name="tanggal" aria-describedby="namaHelp">
                            </div>
                            <div class="mb-3">
                                <label for="uang-masuk" class="form-label">Uang Masuk</label>
                                <input type="number" class="form-control uang-masuk" id="uang-masuk" name="uang_masuk" aria-describedby="uangMasukHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="uang-keluar" class="form-label">Uang Keluar</label>
                                <input type="number" class="form-control uang-keluar" id="uang-keluar" name="uang_keluar" aria-describedby="uangKeluarHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control keterangan" id="keterangan" name="keterangan" aria-describedby="keteranganHelp" required value="">
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary save-button" id="save-button" data-business-id="{{ $business->id }}">Simpan</button>
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
                        <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control tanggal" id="tanggal" name="tanggal" aria-describedby="namaHelp">
                            </div>
                            <div class="mb-3">
                                <label for="uang-masuk" class="form-label">Uang Masuk</label>
                                <input type="number" class="form-control uang-masuk" id="uang-masuk" name="uang_masuk" aria-describedby="uangMasukHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="uang-keluar" class="form-label">Uang Keluar</label>
                                <input type="number" class="form-control uang-keluar" id="uang-keluar" name="uang_keluar" aria-describedby="uangKeluarHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control keterangan" id="keterangan" name="keterangan" aria-describedby="keteranganHelp" required value="">
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary save-button" id="save-button" data-business-id="{{ $business->id }}">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Delete Confirmation Modal  --}}
    <form method="post" id="form-delete">
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
        const tanggal = Array.from(document.getElementsByClassName('tanggal'));
        const uangMasuk = Array.from(document.getElementsByClassName('uang-masuk'));
        const uangKeluar = Array.from(document.getElementsByClassName('uang-keluar'));
        const keterangan = Array.from(document.getElementsByClassName('keterangan'));

        const editBusinessBalanceActivities = Array.from(document.getElementsByClassName('edit-business-balance-activity'));
        const deleteBusinessBalanceActivities = Array.from(document.getElementsByClassName('delete-business-balance-activity'));

        uangKeluar.map((uang, index) => {
            uang.addEventListener('keyup', function(){
                uangMasuk[index].value = 0;
            })
        })

        uangMasuk.map((uang, index) => {
            uang.addEventListener('keyup', function(){
                uangKeluar[index].value = 0;
            })
        })

        saveButtons.map((saveButton, index) => {

            saveButton.addEventListener('click', function(e){
                e.preventDefault();

                data = {
                    tanggal: tanggal[index].value,
                    uangMasuk: uangMasuk[index].value,
                    uangKeluar: uangKeluar[index].value,
                    keterangan: keterangan[index].value,
                }

                axios.post(`/api/${saveButton.dataset.businessId}/dashboard/business-balance-activity`, data)
                .then(res => {
                    console.log(res);
                    formInput[index].submit();
                })
                .catch(err => {
                    if (err.response) {
                        if (err.response.data.errors.tanggal) {
                            tanggal[index].classList.add('is-invalid')
                        } else {
                            tanggal[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.uangMasuk) {
                            uangMasuk[index].classList.add('is-invalid')
                        } else {
                            uangMasuk[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.uangKeluar) {
                            uangKeluar[index].classList.add('is-invalid')
                        } else {
                            uangKeluar[index].classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.keterangan) {
                            keterangan[index].classList.add('is-invalid')
                        } else {
                            keterangan[index].classList.remove('is-invalid')
                        }
                    }
                })
            })
        })

        editBusinessBalanceActivities.map((editBusinessBalanceActivity, index) => {
            editBusinessBalanceActivity.addEventListener('click', function() {
                formInput[1].setAttribute('action', `/${editBusinessBalanceActivity.dataset.businessId}/dashboard/business-balance-activity/${editBusinessBalanceActivity.dataset.id}`)
                axios.get(`/api/${editBusinessBalanceActivity.dataset.businessId}/dashboard/business-balance-activity/${editBusinessBalanceActivity.dataset.id}`)
                .then(res => {
                    tanggal[1].value = res.data.data.tanggal;
                    keterangan[1].value = res.data.data.keterangan;
                    tanggal[1].value = res.data.data.tanggal;
                    uangMasuk[1].value = res.data.data.uang_masuk;
                    uangKeluar[1].value = res.data.data.uang_keluar;

                })
                .catch(err => {
                    console.log(err);
                });

            })
        })

        deleteBusinessBalanceActivities.map((deleteBusinessBalanceActivity, index) => {
            deleteBusinessBalanceActivity.addEventListener('click', function(){
                const formDelete = document.getElementById('form-delete');

                formDelete.setAttribute('action', `/${deleteBusinessBalanceActivity.dataset.businessId}/dashboard/business-balance-activity/${deleteBusinessBalanceActivity.dataset.id}`);
            })
        })

    </script>
@endsection
