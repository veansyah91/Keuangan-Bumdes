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
                    @if ($business->kategori == "Restoran")
                        <div class="row">
                            <div class="col-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Operator</th>
                                            <th class="text-center">Tanggal Nota</th>
                                            <th class="text-center">Nomor Nota</th>
                                            <th class="text-center">Nama Pelanggan</th>
                                            <th class="text-center">Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($invoices->isNotEmpty())
                                            @foreach ($invoices as $invoice)
                                                <tr>
                                                    <td class="text-center">{{ $invoice->operator }}</td>
                                                    <td class="text-center">{{ $invoice->created_at->toDateString() }}</td>
                                                    <td class="text-center">{{ $invoice->nomor }}</td>
                                                    <td>{{ $invoice->nama_konsumen }}</td>
                                                    <td class="text-end">Rp. {{ number_format($invoice->jumlah,0,",",".") }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="selectInvoice({{$invoice->id}}, {{$invoice->jumlah}})">Detail</button>
                                                    </td>
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
                    @else
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
                    @endif
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
                    <button type="button" class="btn btn-primary" id="invoice-print" data-bs-dismiss="modal" data-operator="{{ Auth::user()['name'] }}" data-business-name="{{ $business['nama'] }}" data-alamat="{{ $identity['nama_desa'] }}"><i class="bi bi-printer"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">

    let invoice = [];

    const selectInvoice = (id, jumlah) => {
        const tableBodyDetail = document.getElementById('table-body-detail');
        const tableFooterDetail = document.getElementById('table-footer-detail');
        
        axios.get(`/api/invoice-detail/${id}`)
        .then(res => {
            let list = '';
            invoice = res.data.data;
            let total = 0;
            console.log(res.data.data);

            invoice.products.map(invoicesList => {
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
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-end">Bayar</th>
                                                <th class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(jumlah)}</th>
                                            </tr>`;
        })
        .catch(err => {
            console.log(err);
        })
    }

    const waktu = () => {
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

    const invoicePrint = document.getElementById('invoice-print');

    invoicePrint.addEventListener('click', function(){
        const printInvoice = document.getElementById('print');
        let list = '';
        console.log(invoice);

        list += `<div class="text-center fw-bold">
                    ${invoicePrint.dataset.businessName}
                </div>
                <div class="text-center">
                    ${invoicePrint.dataset.alamat}
                </div>`;

        list += `<table style="font-size: 11px">
                    <tbody>
                        <tr>
                            <td>Nomor Nota</td>
                            <td>: ${invoice.nomor}</td>
                        </tr>
                        <tr>
                            <td>Kasir</td>
                            <td>: ${invoice.operator}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: ${waktu()}</td>
                        </tr>
                    </tbody>
                </table>`;

        list += `<table style="width: 100%;font-size: 12px; font-family: 'Times New Roman', Times, serif;margin-bottom:10px">
            <tr style="border-top: solid black">
                <td></td>
            </tr>`

        let total = 0;
        invoice.products.map(product => {
            total += product.pivot.harga * product.pivot.jumlah;
            list += `<tr>
                        <td colspan="2">${product.nama_produk}</td>
                    </tr>
                    <tr>
                        <td>${Intl.NumberFormat(['ban', 'id']).format(product.pivot.harga)} x ${product.pivot.jumlah}</td>
                        <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(product.pivot.harga * product.pivot.jumlah)}</td>
                    </tr>`
        })

        list += `<tr style="border-top: solid black">
                    <td class="text-end">Total :</td>
                    <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(total)}</td>
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
                            nota baru
                        </button>
                </div>`

        printInvoice.innerHTML = list

        printInvoice.classList.remove('d-none');
        app.classList.add('d-none');

        const printBtn = document.getElementById('print-btn');
        const batalPrint = document.getElementById('batal-print');
        const newInvoice = document.getElementById('new-invoice');
        
        batalPrint.addEventListener('click', function(){
            console.log("batal cetak");
            printInvoice.classList.add('d-none');
            app.classList.remove('d-none');
        })

        newInvoice.addEventListener('click', function(){
            window.location=`/${cariProduk.dataset.businessId}/cashier`;
        })

        printBtn.addEventListener('click', function(){

            window.print();

        })
    });
</script>
@endsection
