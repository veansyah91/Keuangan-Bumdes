@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Faktur Pembaruan Layanan</h3>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('invoice.subscribe.create') }}" class="btn btn-primary">Perbarui Layanan</a>
                <ul class="list-group mt-3">
                    @if (count($invoices) > 0)
                        @foreach ($invoices as $invoice)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="fw-bold" style="font-size: 20px">
                                            INV{{ $invoice->no_ref }}
                                        </div>
                                        <div>
                                            Jenis Layanan : {{ $invoice->package == 'yearly' ? 'Tahunan' : 'Bulanan' }}
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="text-end" style="font-size: 25px">
                                            Rp. {{ number_format($invoice->value, 0, '', '.')}}
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

                                        <div>
                                            Status : <span class="{{ $statusColor }}">{{ $status }}</span> 
                                        </div>
                                        
                                    </div>

                                    <div class="col-md-4 col-12 text-end">
                                        @role('DEV')
                                            @if ($invoice->is_waiting)
                                            <form action="{{ route('invoice.subscribe.confirm', $invoice->id) }}" method="post">
                                                @csrf
                                                @method('put')
                                                <button type="submit" class="btn btn-primary">Terima Pembayaran</button>
                                            </form>
                                            @endif
                                        @endrole
                                        @role('ADMIN')
                                            <a href="{{ route('invoice.subscribe.detail', $invoice->id) }}" class="btn btn-secondary">Rincian</a>
                                        @endrole
                                    </div>

                                </div>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-center fst-italic disabled">Tidak Ada Data</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/invoice-subscribe/index.js">
       
    </script>
@endsection
