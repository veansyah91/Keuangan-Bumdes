@extends('layouts.admin')

@section('admin')
    <div class="page-heading">
        <h3>Ubah Sandi</h3>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-12 col-md-6">
                <form action="{{ route('users.change-password.store') }}" method="post">
                    @csrf
                    @method('patch')
                    <div class="mb-3 row">
                        <label for="password" class="col-sm-3 col-form-label">Sandi Baru</label>
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

@section('script')
    <script type="text/javascript">
        
        window.addEventListener('load', function (){
            
        })
    </script>
@endsection
