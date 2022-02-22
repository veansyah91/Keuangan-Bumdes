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
                                        
                                        <div class="mb-3 row">
                                            <label for="nama-produk" class="col-sm-4 col-form-label">Produk</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="nama-produk" data-id-product="" placeholder="e.x. Pulsa Nelpon 10.000">
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
                                        <button type="button" class="btn btn-primary" id="add-product">Tambah</button>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="fw-bold mb-1">
                                Detail
                            </div>
                            <div class="card">
                                <div class="card-body table-responsive">
                                    <table class="table">
                                        <tbody class="list-group" id="detail-product">
                                            
                                        </tbody>
                                    </table>
                                </div>                                
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row justify-content-between">
                        <div class="col-12 col-lg-6">
                            <table class="fs-3 table">
                                <tr>
                                    <td>Total Belanja : </td>
                                    <td id="amount"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <button class="btn btn-lg btn-success disabled" id="bayar-btn" data-bs-toggle="modal" data-bs-target="#bayarModal">Bayar</button>
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
    </script>
@endsection
