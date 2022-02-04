@extends('layouts.admin')

@section('admin')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card border">
                <div class="card-body border-start border-4 border-primary fs-2 fw-bold">
                    <span class="text-primary">Saldo: </span>
                    Rp. {{ number_format($saldo,0,",",".") }}
                </div>
            </div>
        </div>        
    </div>
@endsection
