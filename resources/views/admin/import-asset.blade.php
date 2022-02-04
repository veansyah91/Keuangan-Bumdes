@extends('layouts.admin')

@section('admin')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Import Asset</h1>
    </div>

    <section class="border-top border-bottom">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1">
            <h1 class="h4">Wilayah</h1>
        </div>

        @if (session('success'))
            <div class=" mt-3 alert alert-success d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif
    
        <div class="row">
            <div class="col-12 col-md-6">
                <ol class="list-group mb-2 pb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Provinsi</div>

                            @if (!$provinsi)
                            <form action="{{ route('import-asset.province') }}" method="POST" enctype="multipart/form-data" id="export-province-form">
                                    @csrf
                                    <input class="form-control @error('provinsiFile') is-invalid @enderror" type="file" id="provinsiFile" name="provinsiFile">
                            </form>
                            @else
                                <small class="fst-italic">Data Telah Diinput</small>
                            @endif
                            
                        </div>
                        @if ($provinsi)
                            <span class="fs-4 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg>
                            </span>
                        @else
                            <button 
                                class="btn btn-success btn-sm my-auto"
                                onclick="document.getElementById('export-province-form').submit();">Import</button>
                        @endif
                        
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Kabupaten / Kota</div>
                            @if (!$kabupaten)
                                <form action="{{ route('import-asset.regency') }}" method="POST" enctype="multipart/form-data" id="export-regency-form">
                                    @csrf
                                    <input class="form-control @error('kabupatenFile') is-invalid @enderror" type="file" id="kabupatenFile" name="kabupatenFile">
                                </form>
                            @else
                                <small class="fst-italic">Data Telah Diinput</small>
                            @endif
                        </div>

                        @if ($kabupaten)
                            <span class="fs-4 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg>
                            </span>
                        @else
                            <button 
                                class="btn btn-success btn-sm my-auto"
                                onclick="document.getElementById('export-regency-form').submit();"
                            >
                                Import
                            </button>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Kecamatan</div>
                            @if (!$kecamatan)
                                <form action="{{ route('import-asset.district') }}" method="POST" enctype="multipart/form-data" id="export-district-form">
                                    @csrf
                                    <input class="form-control @error('kecamatanFile') is-invalid @enderror" type="file" id="kecamatanFile" name="kecamatanFile">
                                </form>
                            @else
                                <small class="fst-italic">Data Telah Diinput</small>
                            @endif
                        </div>
                        @if ($kecamatan)
                            <span class="fs-4 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg>
                            </span>
                        @else
                            <button 
                                class="btn btn-success btn-sm my-auto"
                                onclick="document.getElementById('export-district-form').submit();"
                            >
                                Import
                            </button>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Desa / Kelurahan</div>
                            @if (!$desa)
                                <form action="{{ route('import-asset.village') }}" method="POST" enctype="multipart/form-data" id="export-village-form">
                                    @csrf
                                    <input class="form-control @error('desaFile') is-invalid @enderror" type="file" id="villageFile" name="desaFile">
                                    @error('desaFile')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </form>
                            @else
                                <small class="fst-italic">Data Telah Diinput</small>
                            @endif
                        </div>

                        @if ($desa)
                            <span class="fs-4 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg>
                            </span>
                        @else
                            <button 
                                class="btn btn-success btn-sm my-auto" 
                                onclick="document.getElementById('export-village-form').submit();"
                            >
                                Import
                            </button>
                        @endif
                        
                    </li>
                </ol>
            </div>
            
        </div>
    </section>

    
@endsection
