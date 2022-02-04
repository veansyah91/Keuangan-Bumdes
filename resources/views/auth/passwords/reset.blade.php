@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <form action="{{ route('users.change-password.store') }}" method="post">
                    @csrf
                    @method('patch')
                    <div class="mb-3 row">
                        <label for="password" class="col-sm-3 col-form-label fw-bold">Sandi Baru</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}">
                            @error('password') 
                                <div class="invalid-feedback">
                                    <i class="bx bx-radio-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <button class="btn btn-primary">Ubah</button>
                </form>
                
            </div>
        </div>
    </div>
@endsection
