@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">
                    <div class="row justify-content-between">
                        <div class="col-6">
                            Piutang
                        </div>
                        <div class="col-6 text-end">
                            Total : Rp. {{ number_format($sumAccountReceivable,0,",",".") }}
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>Nama</th>
                                        <th>Nomor Nota</th>
                                        <th>Sisa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($accountReceivables->isNotEmpty())
                                        @foreach ($accountReceivables as $accountReceivable)
                                            <tr>
                                                <td>{{ $accountReceivable->nama_konsumen }}</td>
                                                <td class="text-center">
                                                    {{ $accountReceivable->nomor_nota }}
                                                    <button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#invoiceModal" onclick="selectInvoice({{$accountReceivable->invoice_id}})">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                </td>
                                                <td class="text-end">Rp. {{ number_format($accountReceivable->sisa,0,",",".") }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-secondary detail" data-bs-toggle="modal" data-bs-target="#detailModal" data-business="{{ $business->id }}" data-id="{{ $accountReceivable->id }}" data-invoice="{{$accountReceivable->nomor_nota}}">Detail</button>
                                                </td>
                                            </tr>
                                        @endforeach                                    
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="4">
                                                <i>Tidak Ada Data</i>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-end x-overflow-auto">
                                {{ $accountReceivables->links() }}
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Nota Modal--}}
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">Detail Nota</h5>
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
                <div class="modal-footer d-none">
                    <button type="button" class="btn btn-secondary"  data-bs-toggle="modal" data-bs-target="#bayarModal">Bayar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <form method="post" id="form-input">
        @csrf
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">Detail Piutang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <table class="table" id="account-receivable">
                                
                            </table>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal Bayar</th>
                                        <th>Jumlah Bayar</th>
                                    </tr>                                    
                                </thead>
                                <tbody id="payment">
                                    
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                    <div class="modal-footer d-none" id="bayar-button">
                        <button type="button" class="btn btn-secondary"  data-bs-toggle="modal" data-bs-target="#bayarModal">Bayar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Bayar Modal -->
    <form method="post" id="form-input-payment">
        @csrf
        <div class="modal fade" id="bayarModal" tabindex="-1" aria-labelledby="bayarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bayarModalLabel">Bayar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-2">
                            <div class="col-12 mt-2">
                                <div class="mb-3">
                                    <label for="jumlah-bayar" class="form-label">Jumlah Bayar</label>
                                    <input type="hidden" name="accountReceivable" id="account-receivable-input">
                                    <input type="number" class="form-control" id="jumlah-bayar" name="jumlah_bayar" aria-describedby="jumlahHelp" required>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="pay-button" data-bs-dismiss="modal" data-operator="{{ Auth::user()['name'] }}" data-business="{{ $business->nama }}" data-alamat="{{ $identity['nama_desa'] }}">Bayar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script type="text/javascript">

        // let totalPiutang = 0;
        // const jumlahBayar = document.querySelector('#jumlah-bayar');
        const details = Array.from(document.getElementsByClassName('detail'));

        const waktu = () => 
                {
                    date = new Date();
                    millisecond = date.getMilliseconds();
                    detik = date.getSeconds();
                    menit = date.getMinutes();
                    jam = date.getHours();
                    hari = date.getDay();
                    tanggal = date.getDate();
                    bulan = date.getMonth();
                    tahun = date.getFullYear();
                    return `${tanggal}/${bulan+1}/${tahun} ${jam}:${menit}:${detik}`
                }

        details.map((detail, index) => {
            detail.addEventListener('click', function(){
                axios.get(`/api/${detail.dataset.business}/account-receivable/${detail.dataset.id}`)
                .then(res => {
                    const bayarButton = document.querySelector('#bayar-button');
                    res.data.data.accountReceivable.sisa > 0
                    ? bayarButton.classList.remove('d-none')
                    : bayarButton.classList.add('d-none');

                    const accountReceivable = document.getElementById('account-receivable');
                    accountReceivable.innerHTML = `<tbody>
                                                        <tr>
                                                            <td>Nama</td>
                                                            <td>: ${res.data.data.accountReceivable.nama_konsumen}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Nomor Nota</td>
                                                            <td>: ${res.data.data.accountReceivable.nomor_nota}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Sisa</td>
                                                            <td>: Rp. ${Intl.NumberFormat(['ban', 'id']).format(res.data.data.accountReceivable.sisa)}</td>
                                                        </tr>
                                                    </tbody>`;

                    const payment = document.getElementById('payment');

                    let banyakBayar = res.data.data.payment.length;                    
                    
                    if (banyakBayar > 0 ) {
                        let list = '';
                        
                        res.data.data.payment.map(payment => {
                            let waktu = payment.created_at.slice(0, 10);
                            list += `<tr>
                                        <td>${waktu}</td>
                                        <td>Rp. ${Intl.NumberFormat(['ban', 'id']).format(payment.jumlah_bayar)}</td>
                                    </tr>`
                        })
                        payment.innerHTML = list;
                    } else {
                        payment.innerHTML = `<tr>
                                                <td colspan="2" class="text-center"><i>Belum Bayar</i></td>
                                            </tr>`;
                    }

                    let totalPiutang = 0;

                    const inputPayment = document.querySelector('#form-input-payment');
                    const accountReceivableInput = document.querySelector('#account-receivable-input');
                    const jumlahBayar = document.querySelector('#jumlah-bayar');
                    accountReceivableInput.value = detail.dataset.id;
                    
                    totalPiutang = res.data.data.accountReceivable.sisa;
                    jumlahBayar.value = res.data.data.accountReceivable.sisa;

                    const payButton = document.getElementById('pay-button');

                    payButton.addEventListener('click', function(e){
                        e.preventDefault();
                        const printInvoice = document.getElementById('print');
                        let list = '';

                        list += `<div class="text-center fw-bold">
                                    ${payButton.dataset.business}
                                </div>
                                <div class="text-center">
                                    ${payButton.dataset.alamat}
                                </div>`;

                        list += `<table style="font-size: 11px">
                                    <tbody>
                                        <tr>
                                            <td>Kasir</td>
                                            <td>: ${payButton.dataset.operator}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td>: ${waktu()}</td>
                                        </tr>
                                    </tbody>
                                </table>`;

                        list += `<table style="width: 100%;font-size: 12px; font-family: 'Times New Roman', Times, serif;margin-bottom:10px">
                            <tr style="border-top: solid black">
                                <td>Pembayaran Piutang Ke-${banyakBayar}</td>
                                <td>No Nota: ${detail.dataset.invoice}</td>
                            </tr>`;

                        list += `<tr style="border-top: solid black">
                                <td class="text-end">Total :</td>
                                <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(totalPiutang)}</td>
                            </tr>
                            <tr>
                                <td class="text-end">Bayar :</td>
                                <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(jumlahBayar.value)}</td>
                            </tr>
                            <tr style="border-bottom: solid black">
                                <td class="text-end">Sisa :</td>
                                <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(parseInt(totalPiutang) - jumlahBayar.value)}</td>
                            </tr>
                        </table>
                        <div class='row'>

                            <button class="btn btn-sm btn-primary d-print-none" id="print-btn" >
                                cetak
                            </button>

                            <button id="batal-print" class="btn btn-sm btn-secondary d-print-none">
                                batal cetak
                            </button>


                            <button id="new-invoice" class="btn btn-sm btn-success d-print-none d-none">
                                halaman utama
                            </button>

                        </div>`;

                        printInvoice.innerHTML = list;
                        printInvoice.classList.remove('d-none');
                        app.classList.add('d-none');

                        const printBtn = document.getElementById('print-btn');
                        const batalPrint = document.getElementById('batal-print');
                        const newInvoice = document.getElementById('new-invoice');
                        
                        batalPrint.addEventListener('click', function(){
                            printInvoice.classList.add('d-none');
                            app.classList.remove('d-none');
                        });

                        printBtn.addEventListener('click', function(){
                            window.print();
                            batalPrint.classList.add('d-none');
                            newInvoice.classList.remove('d-none');
                            newInvoice.classList.add('d-block');
                        });

                        newInvoice.addEventListener('click', function(){
                            inputPayment.submit();
                        })
                    })

                })
                .catch(err => {
                    console.log(err);
                })
            })
            
        })

        const selectInvoice = (id, jumlah) => {
            const tableBodyDetail = document.getElementById('table-body-detail');
            const tableFooterDetail = document.getElementById('table-footer-detail');
            
            axios.get(`/api/invoice-detail/${id}`)
            .then(res => {
                let list = '';
                let invoicesLists = res.data.data;
                let total = 0;

                invoicesLists.map(invoicesList => {
                    total += invoicesList.pivot.harga * invoicesList.pivot.jumlah;
                    list += `<tr>
                                <td>${invoicesList.nama_produk}</td>
                                <td class="text-end">${invoicesList.pivot.harga}</td>
                                <td class="text-center">${invoicesList.pivot.jumlah}</td>
                                <td class="text-end">${invoicesList.pivot.harga * invoicesList.pivot.jumlah}</td>
                            </tr>`;
                })

                tableBodyDetail.innerHTML = list;
                tableFooterDetail.innerHTML = `<tr>
                                                    <th colspan="3" class="text-end">Grand Total</th>
                                                    <th class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(total)}</th>
                                                </tr>`;
            })
            .catch(err => {
                console.log(err);
            })
        }

        window.addEventListener('load', function (){
            
        })
    </script>
@endsection
