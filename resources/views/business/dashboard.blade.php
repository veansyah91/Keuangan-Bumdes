@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <div class="row justify-content-start fs-3 fw-bold">
                        <div class="col-md-4 col-12">
                            Saldo
                        </div>
                        <div class="col-md-8 col-12">
                            : Rp. {{ $businessBalance ? number_format($businessBalance['sisa'],0,",",".") : 0 }}
                            <div class="btn-group">
                                <button type="button" class="btn btn-link btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button class="dropdown-item btn btn-outline-danger edit-expense" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $business->id }}">
                                            <i class="bi bi-list text-primary"></i>
                                            Aktivitas
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection

@section('script')
    <script type="text/javascript">
        function printScreen() {
            console.log('print');
            window.print();
        }
    </script>
@endsection
