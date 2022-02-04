@extends('layouts.admin')

@section('admin')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Uang Keluar</h1>
    </div>

    <div class="page-content">

        <div class="border-bottom py-2 mb-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Data</button>
        </div>

        
        <form action="{{ route('outcome.index') }}" method="GET">
            <div class="row">
                <div class="col-12">
                    <h5>Filter Berdasarkan Tanggal</h5>
                </div>
            </div>
            <div class="row mt-2">                
                <div class="col-12 col-md-6 ">
                    <div class="mb-3">
                        <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" value="{{ $tanggal_awal }}" id="tanggal-awal" name="tanggal_awal" aria-describedby="tanggal_awalHelp">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control"  value="{{ $tanggal_akhir }}" id="tanggal-akhir" name="tanggal_akhir" aria-describedby="tanggal_akhirHelp">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-dark">Filter</button>
        </form>

        <div class="row justify-content-end">
            <div class="col-1 text-right">
                <a class="btn btn-sm btn-success" id="to-excel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-spreadsheet" viewBox="0 0 16 16">
                        <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v4h10V2a1 1 0 0 0-1-1H4zm9 6h-3v2h3V7zm0 3h-3v2h3v-2zm0 3h-3v2h2a1 1 0 0 0 1-1v-1zm-4 2v-2H6v2h3zm-4 0v-2H3v1a1 1 0 0 0 1 1h1zm-2-3h2v-2H3v2zm0-3h2V7H3v2zm3-2v2h3V7H6zm3 3H6v2h3v-2z"/>
                    </svg>
                    Excel
                </a>
            </div>
            <div class="col-1 text-right">
                <a class="btn btn-sm btn-danger" id="to-pdf">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-pdf" viewBox="0 0 16 16">
                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                    <path d="M4.603 12.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.701 19.701 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.716 5.716 0 0 1-.911-.95 11.642 11.642 0 0 0-1.997.406 11.311 11.311 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.27.27 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.647 12.647 0 0 1 1.01-.193 11.666 11.666 0 0 1-.51-.858 20.741 20.741 0 0 1-.5 1.05zm2.446.45c.15.162.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.881 3.881 0 0 0-.612-.053zM8.078 5.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                    </svg>
                    PDF
                </a>
            </div>
            
            
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <table id="myTable" class="table table-striped ">
                    <thead class="table-primary">
                        <tr>
                            <th>Jumlah</th>
                            <th>Tanggal Masuk</th>
                            <th>Keterangan</th>
                            <th>Operator</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($outcomes->isNotEmpty())
                            @foreach ($outcomes as $outcome)
                                <tr>
                                    <td>Rp. {{ number_format($outcome->jumlah,0,",",".") }}</td>
                                    <td>{{ $outcome->tanggal_keluar }}</td>
                                    <td>{{ $outcome->keterangan }}</td>
                                    <td>{{ $outcome->operator }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $outcome->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"><i class="bi bi-trash"></i></button>
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ubahModal" onclick="ubahModalFunc({{ $outcome->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="detailModalFunc({{ $outcome->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><i class="bi bi-info-square"></i></button>
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

                <div class="d-flex justify-content-end x-overflow-auto">
                    {{ $outcomes->links() }}
                </div>
                
            </div>
        </div>
    </div>

    <!-- Input Modal -->
    <form action="{{ route('outcome.store') }}" method="post" enctype="multipart/form-data">
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
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" aria-describedby="jumlahHelp" required value="{{ old('jumlah') }}">
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="tanggal_keluar" class="form-label">Tanggal Keluar</label>
                                <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar" aria-describedby="tanggal_keluarHelp" required value="{{ old('tanggal_keluar') }}">
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" aria-describedby="tanggal_keluarHelp" required value="{{ old('keterangan') }}">
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="image" class="form-label">Foto Nota</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" aria-describedby="imageHelp" onchange="previewImageInput()">
                                @error('image')
                                    <div id="imageHelp" class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <img class="img-fluid" id="img-preview-input">
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
    <form method="post" id="edit-form" enctype="multipart/form-data">
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
                                <label for="edit-jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="edit-jumlah" name="jumlah" aria-describedby="jumlahHelp" required>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="edit-tanggal-keluar" class="form-label">Tanggal Keluar</label>
                                <input type="date" class="form-control" id="edit-tanggal-keluar" name="tanggal_keluar" aria-describedby="tanggal_keluarHelp" required>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="edit-keterangan" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="edit-keterangan" name="keterangan" aria-describedby="keteranganHelp" required>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="mb-3">
                                <label for="edit-image" class="form-label">Foto Nota</label>
                                <input type="file" class="form-control" id="edit-image" name="image" aria-describedby="imageHelp" onchange="previewImageEdit()">
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <img class="img-fluid" id="img-preview-edit">
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

    <!-- Show Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4 fw-bold">
                            Jumlah
                        </div>
                        <div class="col-8" id="show-jumlah">
                            
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4 fw-bold">
                            Tanggal Keluar
                        </div>
                        <div class="col-8" id="show-tanggal-keluar">
                            
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4 fw-bold">
                            Keterangan
                        </div>
                        <div class="col-8" id="show-keterangan">
                            
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4 fw-bold">
                            Foto Nota
                        </div>
                        <div class="col-8">
                            <img class="img-fluid" id="show-img-preview">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <form method="POST" id="deleteForm">
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

        const toPdf = document.getElementById('to-pdf');
        const toExcel = document.getElementById('to-excel');
        const tanggalAwal = document.getElementById('tanggal-awal');
        const tanggalAkhir = document.getElementById('tanggal-akhir');

        const getData = (id) => {
            let url= `/api/outcome/${id}`;

            return fetch(url)
                .then(response => response.json())
                .then(response => response.data)
        }

        const ubahModalFunc = async (id) => {
            const editForm = document.getElementById('edit-form');
            const editJumlah = document.getElementById('edit-jumlah');
            const editTanggalKeluar = document.getElementById('edit-tanggal-keluar');
            const editKeterangan= document.getElementById('edit-keterangan');
            const imagePreviewEdit= document.getElementById('img-preview-edit');

            editForm.setAttribute('action', `/outcome/${id}`);

            let data = await getData(id);

            editJumlah.value = data.jumlah;
            editTanggalKeluar.value = data.tanggal_keluar;
            editKeterangan.value = data.keterangan;

            if (data.image) {
                imagePreviewEdit.setAttribute('src', `/storage/${data.image}`);
            }
        }
        
        function convertDateDBtoIndo(string) {
            bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September' , 'Oktober', 'November', 'Desember'];
        
            tanggal = string.split("-")[2];
            bulan = string.split("-")[1];
            tahun = string.split("-")[0];
        
            return tanggal + " " + bulanIndo[Math.abs(bulan)] + " " + tahun;
        }

        const detailModalFunc = async (id) => {
            let data = await getData(id);

            let showImagePreview = document.getElementById('show-img-preview');
            let showJumlah = document.getElementById('show-jumlah');
            let showTanggalKeluar = document.getElementById('show-tanggal-keluar');
            let showKeterangan = document.getElementById('show-keterangan');

            let jumlahUang = data.jumlah;
            showJumlah.innerText = ': Rp. ' + jumlahUang.toLocaleString("id-ID");
            showTanggalKeluar.innerText = ': ' + convertDateDBtoIndo(data.tanggal_keluar);
            showKeterangan.innerText = ': ' + data.keterangan;

            if (data.image) {
                showImagePreview.setAttribute('src', `/storage/${data.image}`);
            }

        }

        const deleteConfirmation = (id) => {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.setAttribute('action', `/outcome/${id}`);
        }

        function previewImageInput() {
            const image = document.querySelector('#image');
            const imgPreview = document.querySelector('#img-preview-input');

            imgPreview.style.display = 'block';

            const srcImage = URL.createObjectURL(image.files[0]);

            imgPreview.src = srcImage;
        }  

        function previewImageEdit() {
            const image = document.querySelector('#edit-image');
            const imgPreviewEdit = document.querySelector('#img-preview-edit');

            imgPreviewEdit.style.display = 'block';

            const srcImage = URL.createObjectURL(image.files[0]);

            imgPreviewEdit.src = srcImage;
        }  

        window.addEventListener('load', function (){
            toPdf.setAttribute('href', `/outcome-pdf?tanggal_awal=${tanggalAwal.value}&tanggal_akhir=${tanggalAkhir.value}`);

            toExcel.setAttribute('href', `/outcome-excel?tanggal_awal=${tanggalAwal.value}&tanggal_akhir=${tanggalAkhir.value}`)
        })
    </script>
@endsection

