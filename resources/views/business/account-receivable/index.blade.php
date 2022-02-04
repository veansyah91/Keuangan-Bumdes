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
                                                <td class="text-center">{{ $accountReceivable->nomor_nota }}</td>
                                                <td class="text-end">Rp. {{ number_format($accountReceivable->sisa,0,",",".") }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="detail({{ $business->id }}, {{ $accountReceivable->id }})">Detail</button>
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
                        <button type="submit" class="btn btn-secondary">Bayar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script type="text/javascript">
        const detail = (businessId, id) => {
            axios.get(`/api/${businessId}/account-receivable/${id}`)
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
                
                if (res.data.data.payment.length > 0 ) {
                    let list = '';
                    
                    res.data.data.payment.map(payment => {
                        let waktu = payment.created_at.slice(0, 10);
                        list = `<tr>
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

                const inputPayment = document.querySelector('#form-input-payment');
                const accountReceivableInput = document.querySelector('#account-receivable-input');
                const jumlahBayar = document.querySelector('#jumlah-bayar');
                accountReceivableInput.value = id;
                jumlahBayar.value = res.data.data.accountReceivable.sisa;

            })
            .catch(err => {
                console.log(err);
            })
        }

        window.addEventListener('load', function (){
            
        })
    </script>
@endsection
