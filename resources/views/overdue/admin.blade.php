@extends('layouts.admin')

@section('admin')
    <div class="page-heading">
        <h3>Jatuh Tempo</h3>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-12 fw-bold">
                        No Ref
                    </div>
                    <div class="col-lg-4 col-12">
                        : {{ $subscribe['no_ref'] }}
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-3 col-12 fw-bold">
                        Jatuh Tempo
                    </div>
                    <div class="col-lg-4 col-12">
                        : {{ $subscribe['date_format'] }} 

                        @php
                            $now = Date('Y-m-d');
                        @endphp
                        @if ($now > $subscribe['due_date'])
                            <span class="badge bg-danger">Kadaluarsa</span>
                        @endif
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <a href="{{ route('invoice.subscribe.create') }}" class="btn btn-primary">Perbarui Layanan</a>
                        @if (SubscribeInvoiceHelper::invoice())
                            <a href="{{ route('invoice.subscribe.index') }}" class="btn btn-success">Rincian Pembayaran</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection
