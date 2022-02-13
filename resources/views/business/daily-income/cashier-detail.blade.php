@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="d-flex col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.daily-income.index', $business->id) }}">Pendapatan Hari Ini</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kasir</li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">
                    <div class="row">
                        <div class="col-6">
                            Kasir
                        </div>
                        <div class="col-6 text-end">
                            Rp. {{ number_format($invoices->sum('jumlah'),0,",",".") }}
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center">Operator</th>
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

</script>
@endsection
