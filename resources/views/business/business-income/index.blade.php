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
                            <button 
                                class="btn btn-outline-success"
                                id="export-excel-btn"
                                >
                                <i class="bi bi-file-spreadsheet-fill"></i>
                                Excel
                            </button>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#pdfModal"><i class="bi bi-file-pdf-fill"></i>PDF</button>
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#filterModal" id="select-filter"><i class="bi bi-filter"></i>Filter</button>
                        </div>

                        {{-- to excel  --}}
                        <form action="{{ route('business.income.excel', $business->id) }}" method="get" id="export-excel-form">
                            <input type="hidden" name="berdasarkan" value="{{ $berdasarkan }}">
                            <input type="hidden" name="ke" value="{{ $tanggalSekarang }}">
                            <input type="hidden" name="dari" value="{{ $tanggalAkhir }}">
                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                        </form>
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
                        <div class="row border-bottom mb-3">
                            <div class="col-12">
                                <div class="mb-3 row">
                                    <label for="berdasarkan" class="col-sm-3 col-form-label">Berdasarkan</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" aria-label="berdasarkan" id="berdasarkan" name="berdasarkan">
                                            <option value="date">Tanggal</option>
                                            <option value="month">Bulan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="by-date">
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

                        @php
                            $year = intval(Date('Y'));
                            $month = intval(Date('m'));
                        @endphp

                        <div class="row d-none" id="by-month">
                            <div class="col-6">
                                <div class="mb-3 row">
                                    <label for="bulan" class="col-sm-3 col-form-label">Bulan</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" aria-label="Default select example" id="bulan" name="bulan">
                                            @for ($i = 1; $i < 13; $i++)
                                                <option value={{ $i }}>{{ MonthHelper::index($i) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3 row">
                                    <label for="bulan" class="col-sm-3 col-form-label">Tahun</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" aria-label="Default select example" id="bulan" name="tahun">
                                            @for ($i = $year; $i > $year - 10; $i--)
                                                <option value={{ $i }}>{{ $i }}</option>
                                            @endfor
                                        </select>
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
                        <input type="hidden" name="dari" value="{{ $tanggalAkhir }}">
                        <input type="hidden" name="ke" value="{{ $tanggalSekarang }}">
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

    {{-- PDF Modal --}}
    <form method="get" action="{{ url('/' . $business->id . '/business-income/pdf?berdasarkan=' . $berdasarkan . '&dari=' . $tanggalAkhir . '&ke=' . $tanggalSekarang. '&bulan=' . $bulan . '&tahun=' . $tahun) }}">
        <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title text-center" id="pdfModalLabel">Simpan Ke PDF</h3>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="berdasarkan" value="{{ $berdasarkan }}">
                        <input type="hidden" name="dari" value="{{ $tanggalAkhir }}">
                        <input type="hidden" name="ke" value="{{ $tanggalSekarang }}">
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <div class="fs-5 fw-bold">
                            Pembuat
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="jabatan-pembuat" class="form-label">Jabatan</label>
                                    <input type="text" class="form-control" id="jabatan-pembuat" name="jabatan_pembuat" aria-describedby="jabatanPembuatHelp" value="Kepala Unit">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="nama-pembuat" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama-pembuat" name="nama_pembuat" aria-describedby="namaPembuatHelp">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="fs-5 fw-bold">
                            Penerima
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="jabatan-penerima" class="form-label">Jabatan</label>
                                    <input type="text" class="form-control" id="jabatan-penerima" name="jabatan_penerima" aria-describedby="jabatanPenerimaHelp" value="Bendahara BUMDes">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="nama-penerima" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama-penerima" name="nama_penerima" aria-describedby="namaPenerimaHelp">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="submit-save-button">Export PDF</button>
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

    const byDate = document.getElementById('by-date');
    const byMonth = document.getElementById('by-month');
    const berdasarkan = document.getElementById('berdasarkan');
    const bulan = document.getElementById('bulan');
    const tahun = document.getElementById('tahun');

    function setDefault() {
        
        if (berdasarkan.value == 'date') {
            byDate.classList.remove('d-none');
            byMonth.classList.add('d-none');
        }
        else {
            byDate.classList.add('d-none');
            byMonth.classList.remove('d-none');

            const d = new Date();
            let thisMonth = d.getMonth() + 1;

            bulan.value = thisMonth;
        }
    }

    berdasarkan.addEventListener('change', function(){
        setDefault();
    })

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

    const exportExcelBtn = document.getElementById('export-excel-btn');
    const exportExcelForm = document.getElementById('export-excel-form');

    exportExcelBtn.addEventListener('click', function(){
        exportExcelForm.submit();
    })

    window.addEventListener('load', function() {
        setDefault();
        
    })

</script>
@endsection
