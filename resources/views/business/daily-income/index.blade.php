@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">
                    <div class="row">
                        <div class="col-6">
                            Pendapatan Hari Ini
                        </div>
                        <div class="col-6 text-end">
                            Rp. {{ number_format($invoices->sum('jumlah') + $accountReservePayments->sum('jumlah_bayar'),0,",",".") }}
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4  class="card-title">Pendapatan Kasir</h4>
                                </div>
                                <div class="card-body text-center ">
                                    <h3 class="fw-bold">Rp. {{ number_format($invoices->sum('jumlah'),0,",",".") }}</h3>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="{{ route('business.daily-income.cashier-detail', $business->id) }}" class="btn btn-primary">Detail</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mt-md-0 mt-2">
                            <div class="card">
                                <div class="card-header">
                                    <h4  class="card-title">Pembayaran Piutang</h4>
                                </div>
                                <div class="card-body text-center align-middle">
                                    <h3 class="fw-bold">Rp. {{ number_format($accountReservePayments->sum('jumlah_bayar'),0,",",".") }}</h3>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="{{ route('business.daily-income.account-reserve-payment-detail', $business->id) }}" class="btn btn-primary">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($invoices->sum('jumlah') + $accountReservePayments->sum('jumlah_bayar') > 0)
                    <div class="card-footer">
                        <form action="{{ route('business.daily-income.closing-income', $business->id) }}" method="post">
                            @csrf
                            <input type="hidden" value="{{ $invoices->sum('jumlah') + $accountReservePayments->sum('jumlah_bayar') }}" name="jumlah">
                            <button class="btn btn-success" type="submit">
                                {{ $closing ? 'Perbarui Kas' : 'Simpan Ke Kas' }}
                            </button>
                        </form>
                    </div>
                @endif
                
                
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">

</script>
@endsection
