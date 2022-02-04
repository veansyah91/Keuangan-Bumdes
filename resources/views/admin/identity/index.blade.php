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
                    <div class="row">
                        <div class="col-12 mt-1 mb-3">
                            <a href="{{ route('identity.edit', [$identity['id']]) }}" class="btn btn-sm btn-outline-primary">Ubah Data Desa</a>
                        </div>

                        @if ($identity['image'])
                            <div class="col-md-12 text-center d-none d-md-block">
                                <img src="{{ asset('storage/' . $identity['image']) }}" alt="logo_desa" class="img-fluid mt-2 w-25 p-3">
                            </div>
                        @endif
    
                        <div class="col-12 fs-2 fw-bold mt-2 text-center">
                            Desa {{ $identity->nama_desa }}
                        </div>
                        <div class="col-12 fw-bold text-center border-bottom pb-2">
                            Kecamatan {{ $identity->nama_kecamatan }}, {{ $identity->nama_kabupaten }}, {{ $identity->nama_provinsi }}
                        </div>
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
