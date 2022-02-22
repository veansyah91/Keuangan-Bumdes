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
                            Pendapatan
                        </div>
                        <div class="col-6 text-end">
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#filterModal"><i class="bi bi-file-spreadsheet-fill"></i>Excel</button>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#pdfModal"><i class="bi bi-file-pdf-fill"></i>PDF</button>
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#filterModal"><i class="bi bi-filter"></i>Filter</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="list-group">
                                @foreach ($tanggal as $t)
                                    @php
                                        $date = Carbon\Carbon::parse($t)->locale('id');
                                    @endphp
                                    <div class="list-group-item list-group-item-action" aria-current="true">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">{{ $date->translatedFormat('jS F Y') }}</h5>
                                            <div class="fs-4 fw-bold">
                                                Rp. {{ number_format(BusinessIncomeHelper::getCashier($t, $business->id) + BusinessIncomeHelper::getAccountReservePayment($t, $business->id),0,",",".") }} 

                                                @if (BusinessIncomeHelper::getCashier($t, $business->id) + BusinessIncomeHelper::getAccountReservePayment($t, $business->id) > 0)
                                                    @if (BusinessIncomeHelper::getStatusClosing($t, $business->id))
                                                        <i class="bi bi-check-circle-fill text-primary"></i>
                                                        @role('ADMIN')
                                                            <button class="btn btn-sm btn-outline-success save-balance" data-date="{{ $t }}" data-amount="{{ BusinessIncomeHelper::getCashier($t, $business->id) + BusinessIncomeHelper::getAccountReservePayment($t, $business->id) }}" data-bs-toggle="modal" data-bs-target="#saveModal">
                                                                update
                                                            </button>        
                                                        @endrole
                                                    @else
                                                        <button class="btn btn-link save-balance" data-date="{{ $t }}" data-amount="{{ BusinessIncomeHelper::getCashier($t, $business->id) + BusinessIncomeHelper::getAccountReservePayment($t, $business->id) }}" data-bs-toggle="modal" data-bs-target="#saveModal">
                                                            <i class="bi bi-question-circle-fill text-danger"></i>
                                                        </button>                                                                                                            
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div class="w-50">
                                            <table class="table">
                                                <tr>
                                                    <td>Kasir</td>
                                                    <td>: Rp. {{ number_format(BusinessIncomeHelper::getCashier($t, $business->id),0,",",".") }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Piutang</td>
                                                    <td>: Rp. {{ number_format(BusinessIncomeHelper::getAccountReservePayment($t, $business->id),0,",",".") }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        
                                    </div>
                                @endforeach
                                
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    {{-- Modal Filter --}}
    <form action="/{{ $business->id }}/business-income" method="get">
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    Tampilkan Berdasarkan 
                                </label>
                            </div>
    
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3 row">
                                        <label for="dari" class="col-sm-2 col-form-label">Dari</label>
                                        <div class="col-sm-10">
                                            <input type="date" class="form-control" id="dari" name="dari" value="{{ request('dari') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3 row">
                                        <label for="ke" class="col-sm-2 col-form-label">Ke</label>
                                        <div class="col-sm-10">
                                            <input type="date" class="form-control" id="ke" name="ke" value="{{ request('ke') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Save Modal --}}
    <form method="post" action="{{ route('business.business-income.update-business-balance', $business->id) }}">
        @csrf
        @method('patch')
        <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <input type="hidden" id="date" name="tanggal" class="date">
                        <input type="hidden" id="amount" name="jumlah" class="amount">
                        <input type="hidden" id="dari" name="dari" value="{{ $tanggalAkhir }}">
                        <input type="hidden" id="ke" name="ke" value="{{ $tanggalSekarang }}">
                        <h3 class="modal-title text-center" id="saveModalLabel">Anda Yakin Simpan Kelas Kas?</h3>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="submit-save-button">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



@endsection

@section('script')
<script type="text/javascript">
    
    const saveBalances = Array.from(document.getElementsByClassName('save-balance'));
    const date = document.getElementsByClassName('date');
    const amount = document.getElementsByClassName('amount');

    saveBalances.map(saveBalance => {
        saveBalance.addEventListener('click', function(){
            date[0].value = saveBalance.dataset.date;
            amount[0].value = saveBalance.dataset.amount;
        })
    })

    const updateBalances = Array.from(document.getElementsByClassName('update-balance'));
    updateBalances.map(updateBalance => {
        updateBalance.addEventListener('click', function(){
            date[1].value = updateBalance.dataset.date;
            amount[1].value = updateBalance.dataset.amount;
        })
    })

</script>
@endsection
