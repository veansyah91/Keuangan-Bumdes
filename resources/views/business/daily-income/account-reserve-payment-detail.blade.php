@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="d-flex col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.daily-income.index', $business->id) }}">Pendapatan Hari Ini</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pembayaran Piutang</li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">
                    <div class="row">
                        <div class="col-6">
                            Pembayaran Piutang
                        </div>
                        <div class="col-6 text-end">
                            Rp. {{ number_format($accountReservePayments->sum('jumlah_bayar'),0,",",".") }}
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center">Operator</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Jumlah</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($accountReservePayments->isNotEmpty())
                                @foreach ($accountReservePayments as $accountReservePayment)
                                    <tr>
                                        <td class="text-center">{{ $accountReservePayment->operator }}</td>
                                        <td class="text-center">{{ $accountReservePayment->accountReceivable->nama_konsumen }}</td>
                                        <td class="text-end">Rp. {{ number_format($accountReservePayment->jumlah_bayar,0,",",".") }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3">
                                        <i>Belum Ada Data</i>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>                
            </div>
        </div>
    </div>

    <!--Detail Nota Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Nota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="table-body-detail">
                            
                        </tbody>
                        <tfoot id="table-footer-detail">
                            
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">

</script>
@endsection
