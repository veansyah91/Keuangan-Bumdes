@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Perpanjang Layanan</h3>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('invoice.subscribe.store') }}" method="post">
                    @csrf
                    <div class="mb-3 row">
                        <label for="inputPassword" class="col-sm-3 col-form-label fw-bold">Tanggal</label>
                        <div class="col-sm-5">
                        <input type="date" class="form-control" id="inputPassword" value="{{Date('Y-m-d')}}" name="date" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="inputPassword" class="col-sm-3 col-form-label fw-bold">
                            Paket Perpanjangan
                        </label>
                        <div class="col-sm-5">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="package" id="flexRadioDefault1" value="monthly" checked>
                                <label class="form-check-label" for="flexRadioDefault1">
                                    <div class="fw-bold">
                                        Bulanan
                                    </div>
                                    <div class="text-end">
                                        Rp. 250.000 <small class="text-decoration-line-through" style="font-size: 12px">Rp. 350.000</small>
                                    </div>
                                </label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="radio" name="package" id="flexRadioDefault2" value="yearly">
                                <label class="form-check-label" for="flexRadioDefault2">
                                    <div class="fw-bold">
                                        Tahunan
                                    </div>
                                    <div class="text-end">
                                        Rp. 2.500.000 <small class="text-decoration-line-through" style="font-size: 12px">Rp. 4.200.000</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    @error('package')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <button class="btn btn-primary" type="submit">
                        Buat Invoice
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/invoice-subscribe/index.js">
       
    </script>
@endsection
