@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">
                    <div class="row">
                        <div class="col-6">
                            Pengeluaran
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah</button>
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah</th>
                                        <th>Operator</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($expenses->isNotEmpty())
                                        @foreach ($expenses as $expense)
                                            <tr>
                                                <td class="text-center">{{ $expense->tanggal_keluar }}</td>
                                                <td>{{ $expense->keterangan }}</td>
                                                <td class="text-end">Rp. {{ number_format($expense->jumlah,0,",",".") }}</td>
                                                <td class="text-center">{{ $expense->operator }}</td>
                                                <td>
                                                    @if (Auth::user()['name'] == $expense->operator || Auth::user()->hasRole('writer'))
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-link btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <button class="dropdown-item btn btn-outline-danger delete-expense" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $expense->id }}">
                                                                        <i class="bi bi-trash-fill text-danger"></i>
                                                                        Hapus
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <button class="dropdown-item btn btn-outline-danger edit-expense" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $expense->id }}">
                                                                        <i class="bi bi-pencil-square text-success" ></i>
                                                                        Ubah
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
{{-- 
                                                    @role('ADMIN')
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-link btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <button class="dropdown-item btn btn-outline-danger delete-expense" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $expense->id }}">
                                                                        <i class="bi bi-trash-fill text-danger"></i>
                                                                        Hapus
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <button class="dropdown-item btn btn-outline-danger edit-expense" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $expense->id }}">
                                                                        <i class="bi bi-pencil-square text-success" ></i>
                                                                        Ubah
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endrole --}}
                                                    
                                                    
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center fst-italic" colspan="3">Tidak Ada Data</td>
                                        </tr>
                                    @endif
                                    
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-end x-overflow-auto">
                                {{ $expenses->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Input Modal -->
    <form action="{{ route('business.expense.store', $business->id) }}" method="post" class="form-input">
        @csrf
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control tanggal" id="tanggal" name="tanggal" aria-describedby="namaHelp" required value="{{ Date('d-m-Y') }}">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control jumlah" id="jumlah" name="jumlah" aria-describedby="namaHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control keterangan" id="keterangan" name="keterangan" aria-describedby="namaHelp" required>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input kas" type="checkbox" value="1" name="kas"  id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Menggunakan Uang Kas / Harian
                                </label>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button save-button" class="btn btn-primary" id="save-button" data-business-id="{{ $business->id }}">Simpan</button>
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
                        <h5 class="modal-title" id="editModalLabel">Ubah Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control tanggal" id="tanggal" name="tanggal" aria-describedby="namaHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control jumlah" id="jumlah" name="jumlah" aria-describedby="namaHelp" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control keterangan" id="keterangan" name="keterangan" aria-describedby="namaHelp" required>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button save-button" class="btn btn-primary" data-business-id="{{ $business->id }}">Ubah</button>
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
    const saveButton = document.getElementById('save-button');

    // form input
    const tanggal = Array.from(document.getElementsByClassName('tanggal'));
    const jumlah = Array.from(document.getElementsByClassName('jumlah'));
    const keterangan = Array.from(document.getElementsByClassName('keterangan'));
    const formInput = Array.from(document.getElementsByClassName('form-input'));

    const saveButtons = Array.from(document.getElementsByClassName('save-button'));

    saveButtons.map((saveButton, index) => {
        saveButton.addEventListener('click', function(e){
            e.preventDefault();

            data = {
                tanggal: tanggal[index].value,
                jumlah: parseInt(jumlah[index].value),
                keterangan: keterangan[index].value,
            };

            axios.post(`/api/${saveButton.dataset.businessId}/expense`, data)
            .then(res => {
                formInput[index].submit();
            })
            .catch(err => {
                if (err.response)
                    {
                        if (err.response.data.errors.tanggal) {
                            tanggal.classList.add('is-invalid')
                        } else {
                            tanggal.classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.jumlah) {
                            jumlah.classList.add('is-invalid')
                        } else {
                            jumlah.classList.remove('is-invalid')
                        }

                        if (err.response.data.errors.keterangan) {
                            keterangan.classList.add('is-invalid')
                        } else {
                            keterangan.classList.remove('is-invalid')
                        }
                    }
            })
        })
    })
    

    const deleteExpenses = Array.from(document.getElementsByClassName('delete-expense'));

    deleteExpenses.map(deleteExpense=> {
        deleteExpense.addEventListener('click', function(){
            const formDelete = document.querySelector('#form-delete');
            formDelete.setAttribute('action', `/${saveButton.dataset.businessId}/expense/${deleteExpense.dataset.id}`);
            
        })
    })

    const editExpense = Array.from(document.getElementsByClassName('edit-expense'));

    editExpense.map(edit => {
        edit.addEventListener('click', function(){

            formInput[1].setAttribute('action', `/${saveButton.dataset.businessId}/expense/${edit.dataset.id}`);
            console.log(edit.dataset.id);
            axios.get(`/api/expense/${edit.dataset.id}`)
            .then(res => {
                tanggal[1].value = res.data.data.tanggal_keluar;
                jumlah[1].value = res.data.data.jumlah;
                keterangan[1].value = res.data.data.keterangan;
            })
            .catch(err => {
                console.log(err);
            })
        })
    })
    

</script>
@endsection
