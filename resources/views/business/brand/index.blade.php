@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Brand') }}</div>

                <div class="card-body">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Data</button>


                    <div class="row justify-content-center">
                        <div class="col-6">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($brands->isNotEmpty())
                                        @foreach ($brands as $brand)
                                            <tr>
                                                <td>{{ $brand->nama }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $business->id }}, {{ $brand->id }})">hapus</button>
                                                </td>
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
                                {{ $brands->links() }}
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Input Modal -->
    <form action="{{ route('business.brand.store', $business->id) }}" method="post" id="form-input">
        @csrf
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <input type="hidden" id="business-id" value="{{ $business->id }}">
                                <label for="nama" class="form-label">Nama Brand</label>
                                <input type="text" class="form-control" id="nama" name="nama" aria-describedby="namaHelp" required>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="save-button">Simpan</button>
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
        const formInput = document.getElementById('form-input');

        saveButton.addEventListener('click', function()
        {
            const nama = document.getElementById('nama');
            const businessId = document.getElementById('business-id');
            let data = {
                "nama" : nama.value,
            }
            // validasi
            axios.post(`/api/${businessId.value}/brand`, data)
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
                
            })

            
        })

        const deleteConfirmation = (businessId, id) => {
            const deleteConfirmationForm = document.getElementById('delete-confirmation-form');

            deleteConfirmationForm.setAttribute('action', `/${businessId}/brand/${id}`);
        }

        window.addEventListener('load', function (){
            
        })
    </script>
@endsection
