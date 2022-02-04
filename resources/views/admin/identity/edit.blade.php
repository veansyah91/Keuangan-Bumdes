@extends('layouts.admin')

@section('admin')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom col-12 col-lg-6">
        <h1 class="h2">Ubah Data Desa</h1>
        <a href="{{ route('identity.index') }}" class="btn btn-secondary">kembali</a>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6">
            <form method="post" enctype="multipart/form-data" action="{{ route('identity.update', [$identity['id']]) }}">
                @csrf
                @method('patch')
                <div class="mb-3 row">
                    <label for="kepala_desa" class="col-sm-3 col-form-label">Kepala Desa</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('kepala_desa') is-invalid @enderror" id="kepala_desa" name="kepala_desa" value="{{ old('kepala_desa', $identity['kepala_desa']) }}">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="ketua" class="col-sm-3 col-form-label">Ketua BUMDes</label>
                    <div class="col-sm-9 my-auto">
                        <input type="text" class="form-control @error('ketua') is-invalid @enderror" id="ketua" name="ketua" value="{{ old('ketua', $identity['ketua']) }}">
                    </div>
                </div>                
                <div class="mb-3 row">
                    <label for="desa" class="col-sm-3 col-form-label">Desa</label>
                    <div class="col-sm-9">
                        <div class="row">
                            <div class="col-8">
                                <input type="text" class="form-control @error('desa') is-invalid @enderror" id="desa" name="desa" value="{{ old('desa', $identity['nama_desa']) }}">
                            </div>
                            <div class="col-4">
                                <button type="button" id="open-cari-modal"  class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#cariDesaModal">Cari Desa</button>
                            </div>
                        </div>
                        
                    </div>
                </div> 
                <div class="mb-3 row">
                    <label for="kecamatan" class="col-sm-3 col-form-label">Kecamatan</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $identity['nama_kecamatan']) }}">
                    </div>
                </div>                
                <div class="mb-3 row">
                    <label for="kabupaten" class="col-sm-3 col-form-label">Kabupaten</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $identity['nama_kabupaten']) }}">
                    </div>
                </div>                
                <div class="mb-3 row">
                    <label for="provinsi" class="col-sm-3 col-form-label">Provinsi</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" value="{{ old('provinsi', $identity['nama_provinsi']) }}">
                    </div>
                </div>                
                <div class="mb-3 row">
                    <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" value="{{ old('alamat', $identity['alamat']) }}">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="image" class="col-sm-3 col-form-label">Logo Desa</label>
                    <div class="col-sm-9">
                        <input type="file" class="form-control" id="image" name="image" onchange="previewImage()">
                    </div>
                </div>
                <div class="mb-2 row">
                    <img class="img-preview img-fluid" @if ($identity['image']) src="{{ asset('storage/' . $identity['image']) }}" @endif >
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <!--Cari Desa Modal Modal -->
    <div class="modal fade" id="cariDesaModal" tabindex="-1" aria-labelledby="cariDesaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cariDesaModalLabel">Cari Desa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-2" placeholder="Masukkan Nama Desa" id="input-village">
                    <div id="result">
                        <ol class="list-group" id="list-village">
                            
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        const listVillage = document.getElementById('list-village');
        const inputVillage = document.getElementById('input-village'); 
        const openCariModal = document.getElementById('open-cari-modal');

        openCariModal.addEventListener('click', async function(){
            const inputDesa = document.getElementById('desa');
            const inputVillage = document.getElementById('input-village'); 

            inputVillage.value = inputDesa.value;
            let villages = await getVillages(inputDesa.value);

            let list = '';

            villages.map(village => {
                list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Desa ${village.desa}</div>
                                    <small>Kecamatan ${village.kecamatan}, ${village.kabupaten}, ${village.provinsi}</small>
                                </div>
                                <button 
                                    class="btn btn-sm btn-primary" 
                                    onclick="selectVillage('${village.desa}', '${village.kecamatan}', '${village.kabupaten}', '${village.provinsi}')"
                                    data-bs-dismiss="modal"
                                    >Pilih</button>
                                    
                            </li>`
            });

            listVillage.innerHTML = list;
        })

        const getVillages = (req) => {
            let url= `/api/villages?village=${req}`;

            return fetch(url)
                .then(response => response.json())
                .then(response => response.data)
        }

        inputVillage.addEventListener('keyup', async function(){
            listVillage.innerHTML = `<div class="d-flex justify-content-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>`;

            let villages = await getVillages(inputVillage.value);

            let list = '';

            villages.map(village => {
                list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Desa ${village.desa}</div>
                                    <small>Kecamatan ${village.kecamatan}, ${village.kabupaten}, ${village.provinsi}</small>
                                </div>
                                <button 
                                    class="btn btn-sm btn-primary" 
                                    onclick="selectVillage('${village.desa}', '${village.kecamatan}', '${village.kabupaten}', '${village.provinsi}')"
                                    data-bs-dismiss="modal"
                                >
                                    Pilih
                                </button>
                            </li>`
            })

            listVillage.innerHTML = list;
        })

        const selectVillage = (desa, kecamatan, kabupaten, provinsi) => {
            const inputDesa = document.getElementById('desa');
            const inputKecamatan = document.getElementById('kecamatan');
            const inputKabupaten = document.getElementById('kabupaten');
            const inputProvinsi = document.getElementById('provinsi');

            inputDesa .value = desa;
            inputKecamatan.value = kecamatan;
            inputKabupaten.value = kabupaten;
            inputProvinsi.value = provinsi;

        }

        function previewImage() {
            const image = document.querySelector('#image');
            const imgPreview = document.querySelector('.img-preview');

            imgPreview.style.display = 'block';

            const srcImage = URL.createObjectURL(image.files[0]);

            imgPreview.src = srcImage;
        }       


        window.addEventListener('load',async function (){
            
        })
    </script>
@endsection
