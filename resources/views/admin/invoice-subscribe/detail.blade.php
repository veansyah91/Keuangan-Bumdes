@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Detail Faktur Perpanjang Layanan</h3>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2 col-6 fw-bold">
                        Tanggal Faktur
                    </div>
                    <div class="col-4">
                        : {{ $invoice['date_format'] }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-lg-2 col-6 fw-bold">
                        No Ref
                    </div>
                    <div class="col-4">
                        : INV{{ $invoice['no_ref'] }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-lg-2 col-6 fw-bold">
                        Paket
                    </div>
                    <div class="col-4">
                        : {{ $invoice['package'] == 'yearly' ? 'Tahunan' : 'Bulanan' }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-lg-2 col-6 fw-bold">
                        Harga
                    </div>
                    <div class="col-4">
                        : Rp. {{ number_format($invoice->value, 0, '', '.')}}
                    </div>
                </div>
                @php
                    $status = 'Belum Bayar';
                    $statusColor = 'text-warning';
                    
                    if($invoice->is_paid){
                        $status = 'Lunas';
                        $statusColor = 'text-success';
                    }

                    if($invoice->is_waiting){
                        $status = 'Menunggu Konfirmasi';
                        $statusColor = 'text-secondary';
                    }

                @endphp
                <div class="row mt-2">
                    <div class="col-lg-2 col-6 fw-bold">
                        Status
                    </div>
                    <div class="col-4" id="payment-status">
                        : <span class="{{ $statusColor }}" >{{ $status }}</span> 
                    </div>
                </div>

                <div class="mt-4 fw-bold">
                    Silakan Lakukan Pembayaran Via Transfer Bank:
                </div>
                <div class="row">
                    <div class="col-4 text-center">
                        <img src="{{ asset('images/logo-bank/bri.png') }}" class="d-none d-md-block img-fluid mt-2 p-3" alt="logo-bri" style="width: 75%">
                        <img src="{{ asset('images/logo-bank/bri.png') }}" class="d-block d-md-none img-fluid mt-2 p-3" alt="logo-bri" style="width: 100%">
                    </div>
                    <div class="col-8 my-auto">
                        <div>216001010680503</div>
                        <div>an. Ferdi Yansyah</div>
                    </div>
                </div>

                <div>
                    <a target="_blank" href="{{ route('invoice.subscribe.print', $invoice->id) }}" class="btn btn-primary">Cetak Faktur</a>
                    @if (!$invoice->is_paid || $invoice->is_waiting)
                        <button type="button" class="btn btn-success" id="send-wa" onclick="confirmationReqest(this)" data-id="{{ $invoice->id }}">
                            Konfirmasi Pembayaran Via WA
                        </button>
                    @endif
                    
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/invoice-subscribe/detail.js"></script>
    <script src="/js/invoice-subscribe/api.js"></script>
@endsection
