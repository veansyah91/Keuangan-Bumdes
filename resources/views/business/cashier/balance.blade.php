@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">Transaksi Pulsa dan PLN</div>

                <div class="card-body">
                    <div class="row justify-content-start">
                        <div class="col-12 col-md-6">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto my-auto">
                                    <label for="nama-pelanggan" class="col-form-label">Pelanggan</label>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="nama-pelanggan" placeholder="Pelanggan" aria-label="Pelanggan" aria-describedby="cari-pelanggan">
                                        <button class="btn btn-outline-secondary" type="button" data-business-id="{{ $business->id }}" id="cari-pelanggan" data-bs-toggle="modal" data-bs-target="#cariKonsumenModal">Cari</button>
                                        <input type="text" hidden id="id-pelanggan">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row justify-content-between">
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <form action="#" id="cashier-form">
                                        <div class="mb-3 row">
                                            <label for="server" class="col-sm-4 col-form-label">Server</label>
                                            <div class="col-sm-8">
                                                <select class="form-select" aria-label="Select Server" id="server">
                                                    @if ($servers->isNotEmpty())
                                                        @foreach ($servers as $server)
                                                            <option 
                                                                value="{{ $server->nama }}"
                                                                @if ($loop->first)
                                                                    selected
                                                                @endif
                                                                >
                                                                    {{ $server->nama }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="">Belum Ada Data Server</option>                                                        
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="nama-produk" class="col-sm-4 col-form-label">Produk</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="nama-produk" placeholder="e.x. Pulsa Nelpon 10.000">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="nomor" class="col-sm-4 col-form-label">No HP/Token</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="nomor">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="modal" class="col-sm-4 col-form-label">Harga Modal</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control" id="modal">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="jual" class="col-sm-4 col-form-label">Harga Jual</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control" id="jual">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary disabled" id="add-product-btn">Tambah</button>
                                    </form>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mt-4 mt-md-0">
                            <div class="fw-bold mb-1">
                                Detail
                            </div>
                            <div class="card">
                                <div class="card-body table-responsive" style="overflow-x: scroll">
                                    <table class="table" style="width: 200%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Server</th>
                                                <th>Pelanggan</th>
                                                <th>Produk</th>
                                                <th>No HP/Token</th>
                                                <th>Modal</th>
                                                <th>Jual</th>
                                                <th>Laba</th>
                                            </tr>
                                        </thead>
                                        <tbody id="transaction-detail">
                                            <tr>
                                                <td>1.</td>
                                                <td>TDC</td>
                                                <td>Umum</td>
                                                <td>Pulsa Nelpon 50k</td>
                                                <td>0812 4532 2123</td>
                                                <td class="text-end">Rp. 50.500</td>
                                                <td class="text-end">Rp. 52.000</td>
                                                <td class="text-end">Rp. 1.500</td>
                                            </tr>
                                        </tbody>
                                        <tfoot id="transaction-detail-footer">
                                            <tr>
                                                <th colspan="5" class="text-end">Total</th>
                                                <th class="text-end">Rp. 0</th>
                                                <th class="text-end">Rp. 0</th>
                                                <th class="text-end">Rp. 0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Cari Konsumen Modal -->
    <div class="modal fade" id="cariKonsumenModal" tabindex="-1" aria-labelledby="cariKonsumenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cariKonsumenModalLabel">Cari Konsumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-2" placeholder="Masukkan Nama Konsumen" id="input-customer">
                    <div class="result">
                        <ol class="list-group" id="list-customer">
                            
                        </ol>
                    </div>
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
        const cariPelanggan = document.getElementById('cari-pelanggan');

        // element customer modal
        const inputCustomer = document.getElementById('input-customer');
        const listCustomer = document.getElementById('list-customer');

        const namaPelanggan = document.getElementById('nama-pelanggan');
        const idPelanggan = document.getElementById('id-pelanggan');

        cariPelanggan.addEventListener('click', function()
        {
            setUpListCustomer(cariPelanggan.dataset.businessId, inputCustomer.value);
            inputCustomer.addEventListener('keyup', function(){
                setUpListCustomer(cariPelanggan.dataset.businessId, inputCustomer.value);                
            })
            
        })

        const setUpListCustomer = (businessId, search) => {
            axios.get(`/api/${businessId}/customer?search=${search}`)
                .then(res => {
                    let customers = res.data.data;
                    listCustomer.innerHTML = `<div class="d-flex justify-content-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>`;
                    let list = '';

                    customers.map(customer => {
                        list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">${customer.nama}</div>
                                    <small>${customer.alamat ?? ''}, ${customer.no_hp ?? ''}</small>
                                </div>
                                <button 
                                    class="btn btn-sm btn-primary" 
                                    onclick="selectCustomer('${customer.id}', '${customer.nama}')"
                                    data-bs-dismiss="modal"
                                    >Pilih</button>
                                    
                            </li>`
                    })
                    listCustomer.innerHTML = list;

                })
                .catch(err => {
                    console.log(err);
                })
        }

        const selectCustomer = (customerId, customerName) => {
            namaPelanggan.value = customerName;
            idPelanggan.value = customerId;
        }

        const nomor = document.getElementById('nomor');
        const server = document.getElementById('server');
        const modal = document.getElementById('modal');
        const jual = document.getElementById('jual');
        const namaProduk = document.getElementById('nama-produk');
        const addProductBtn = document.getElementById('add-product-btn')

        nomor.addEventListener('keyup', function(){
            if (nomor.value && modal.value && jual.value && namaProduk.value && server.value && namaPelanggan.value) {
                addProductBtn.classList.remove('disabled');
            } else {
                addProductBtn.classList.add('disabled');
            }
        });

        namaPelanggan.addEventListener('keyup', function(){
            if (nomor.value && modal.value && jual.value && namaProduk.value && server.value && namaPelanggan.value) {
                addProductBtn.classList.remove('disabled');
            } else {
                addProductBtn.classList.add('disabled');
            }
        });

        modal.addEventListener('keyup', function(){
            if (nomor.value && modal.value && jual.value && namaProduk.value && server.value && namaPelanggan.value) {
                addProductBtn.classList.remove('disabled');
            } else {
                addProductBtn.classList.add('disabled');
            }
        });

        jual.addEventListener('keyup', function(){
            if (nomor.value && modal.value && jual.value && namaProduk.value && server.value && namaPelanggan.value) {
                addProductBtn.classList.remove('disabled');
            } else {
                addProductBtn.classList.add('disabled');
            }
        });

        namaProduk.addEventListener('keyup', function(){
            if (nomor.value && modal.value && jual.value && namaProduk.value && server.value && namaPelanggan.value) {
                addProductBtn.classList.remove('disabled');
            } else {
                addProductBtn.classList.add('disabled');
            }
        });

        server.addEventListener('change', function(){
            if (nomor.value && modal.value && jual.value && namaProduk.value && server.value && namaPelanggan.value) {
                addProductBtn.classList.remove('disabled');
            } else {
                addProductBtn.classList.add('disabled');
            }
        });

        const cleanForm = () => {
            nomor.value = '';
            modal.value = '';
            jual.value = '';
            namaProduk.value = '';
            namaPelanggan.value = '';
        }

        let transactions = [];
        
        const cashierForm = document.getElementById('cashier-form');

        cashierForm.addEventListener('submit', function(e){
            e.preventDefault();

            data = {
                nomor : nomor.value,
                modal : parseInt(modal.value),
                jual : parseInt(jual.value),
                namaProduk : namaProduk.value,
                namaPelanggan : namaPelanggan.value,
                server : server.value,
            };

            transactions = [...transactions, data];

            updateTable();

            cleanForm();
        });

        const updateTable = () => {
            let list = '';
            const transactionDetail = document.getElementById('transaction-detail');
            const transactionDetailFooter = document.getElementById('transaction-detail-footer');

            let totalLaba = 0;
            let totalModal = 0;
            let totalJual = 0;
            transactions.map((transaction, index) => {
                let laba = transaction.jual - transaction.modal;

                totalJual += transaction.jual;
                totalModal += transaction.modal;
                totalLaba += laba;

                list += `<tr>
                            <td>${index + 1}.</td>
                            <td>${transaction.server}</td>
                            <td>${transaction.namaPelanggan}</td>
                            <td>${transaction.namaProduk}</td>
                            <td>${transaction.nomor}</td>
                            <td class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(transaction.modal)}</td>
                            <td class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(transaction.jual)}</td>
                            <td class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(laba)}</td>
                        </tr>`;
            })
            transactionDetail.innerHTML = list;
            transactionDetailFooter.innerHTML = `<tr>
                                                    <th colspan="5" class="text-end">Total</th>
                                                    <th class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(totalModal)}</th>
                                                    <th class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(totalJual)}</th>
                                                    <th class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(totalLaba)}</th>
                                                </tr>`
        }

        window.addEventListener('load', function(){
            axios.get('/api/')
                    .then()
                    .catch(err => {
                        console.log(err);
                    })
        })


    </script>
@endsection
