@extends('layouts.admin')

@section('admin')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Desa</h1>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-12">
                @if (!$identity)
                    <center>
                        <i>Data Desa Belum Dimasukkan</i>                    
                    </center>
                    <center class="mt-3">
                        <a class="btn btn-primary" href="{{ route('identity.create') }}">Tambah Data Desa</a>
                    </center>
                @else
                    @if ($identity['logo_usaha'] && $identity['image'] && $identity->nama_bumdes)
                        <div class="col-12 mt-1 mb-3">
                            <a href="{{ route('identity.edit', [$identity['id']]) }}" class="btn btn-sm btn-outline-primary">Ubah Data Desa</a>
                        </div>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </symbol>
                        </svg>
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                            <div>
                                Silakan Melengkapi Data Identitas Desa <a href="{{ route('identity.edit', [$identity['id']]) }}" class="btn btn-sm btn-light">Ubah Data Desa</a>
                            </div>
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="row">
                            @if ($identity['image'])
                                <div class="col-md-6 col-12 text-center my-auto">
                                    <div>
                                        <img src="{{ asset('storage/' . $identity['image']) }}" alt="logo_desa" class="img-fluid mt-2 w-md-25 w-50 p-3" alt="logo-kabupaten">
                                        <div class="my-auto fw-bold fst-italic  d-sm-block d-md-none">
                                            Logo Kabupaten
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($identity['logo_usaha'])
                                <div class="col-md-6 col-12 text-center my-auto">
                                    <div class="my-auto">
                                        <img src="{{ asset('storage/' . $identity['logo_usaha']) }}" alt="logo_usaha" class="img-fluid mt-2 w-md-25 w-50 p-3" alt="logo_usaha">
                                        <div class="my-auto fw-bold fst-italic  d-sm-block d-md-none">
                                            Logo BUMDes
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12 text-center my-auto d-md-block d-none">
                                <div class="my-auto fw-bold fst-italic">
                                    Logo Kabupaten
                                </div>
                            </div>
                            <div class="col-md-6 col-12 text-center my-auto d-md-block d-none">
                                <div class="my-auto fw-bold fst-italic">
                                    Logo BUMDes
                                </div>
                            </div>
                        </div>
    
                        <div class="col-12 fs-2 fw-bold mt-2 text-center">
                            {{ $identity->nama_bumdes }}
                        </div>
                        <div class="col-12 fs-3 fw-bold mt-2 text-center">
                            Desa {{ $identity->nama_desa }}
                        </div>
                        <div class="col-12 fw-bold text-center border-bottom pb-2 mb-2">
                            Kecamatan {{ $identity->nama_kecamatan }}, {{ $identity->nama_kabupaten }}, {{ $identity->nama_provinsi }}
                        </div>
                        <hr>
                        <div class="col-12 mt-3 text-center fs-4 fw-bold">
                            Kepala Desa : {{ $identity->kepala_desa }}
                        </div>
                        <div class="col-12 mt-3 text-center fs-4 fw-bold">
                            Ketua BUMDes : {{ $identity->ketua }}
                        </div>
                    </div>
                    
                @endif
            </div>
        </div>
    </div>

    
@endsection
