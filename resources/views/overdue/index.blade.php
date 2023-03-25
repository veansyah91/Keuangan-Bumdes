@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="page-heading">
        <h3>Jatuh Tempo</h3>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-3">
                        No Ref
                    </div>
                    <div class="col-4">
                        : {{ $subscribe['no_ref'] }}
                    </div>
                </div>
                <div class="row mt-3 justify-content-center">
                    <div class="col-3">
                        Jatuh Tempo
                    </div>
                    <div class="col-4">
                        : {{ $subscribe['date_format'] }}
                        @php
                            $now = Date('Y-m-d');
                        @endphp
                        @if ($now > $subscribe['due_date'])
                            <span class="badge bg-danger">Kadaluarsa</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection
