@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center" id="card-index">
        <div class="col-md-10 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">
                    <div class="row justify-content-between">
                        <div class="col-6">
                            Belum Bayar
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>Nama</th>
                                        <th>Nomor Nota</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="primary-table">
                                    
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center d-none" id="card-detail">
        <div class="col-md-8 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">
                    
                    <div class="row">
                        <div class="col-12 col-md-6">
                            Detail
                        </div>
                        <div class="col-12 col-md-6 text-end">
                            <button type="button" class="btn btn-secondary" onclick="back()">kembali</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="fw-bold mb-1">
                        Tambah Item
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="mb-3 row">
                                <label for="kode" class="col-sm-4 col-form-label">Kode / SN</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button" data-business-id="{{ $business->id }}" id="cari-produk" data-bs-toggle="modal" data-bs-target="#cariProdukModal">Cari</button>
                                        <input type="text" class="form-control" placeholder="Kode" aria-label="kode" aria-describedby="cari-kode" id="kode">
                                    </div>
                                    <small id="err-cari-kode" class="text-danger d-none">
                                        masukkan kode dengan benar
                                    </small>
                                </div>                                                
                            </div>
                            <div class="mb-3 row">
                                <label for="nama-produk" class="col-sm-4 col-form-label">Nama Produk</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control-plaintext" id="nama-produk" data-id-product="">
                                    <input type="text" hidden id="product-id">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="harga" class="col-sm-4 col-form-label">Harga</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="harga">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="jumlah" class="col-sm-4 col-form-label">Jumlah</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="jumlah" value="1" min="1">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary disabled" id="add-product">Tambah</button>
                        </div>
                    </div>
                    <div class="row border-top mt-1">
                        <div class="col-12 table-responsive">
                            <table class="table" id="account-receivable">
                                <thead>
                                    <tr class="text-center">
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-detail-body">
                                    
                                    
                                </tbody>

                                <tfoot id="invoice-detail-foot">
                                    <tr>
                                        <th colspan="3" class="text-end">Total</th>
                                        <th class="text-end" id="total-order">Rp. ${Intl.NumberFormat(['ban', 'id']).format(total)}</th>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="3">
                                            <label for="jumlah-bayar" class="col-form-label">Bayar</label>
                                        </th>
                                        <th class="text-end">
                                            <input type="number" class="form-control text-end" id="jumlah-bayar" value="0">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="3">Sisa:</th>
                                        <th class="text-end" id="sisa-bayar">100000</th>
                                    </tr>
                                </tfoot>
                                    
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn btn-primary disabled" id="invoice-print" data-operator="{{ Auth::user()['name'] }}" data-business-name="{{ $business['nama'] }}" data-alamat="{{ $identity['nama_desa'] }}">Bayar</button>
                </div>
            </div>
            
        </div>
    </div>

    {{-- Delete Confirmation Modal  --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-center" id="deleteModalLabel">Anda Yakin Hapus Data Ini?</h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger" id="submit-delete-button" data-bs-dismiss="modal">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!--Cari Produk Modal -->
    <div class="modal fade" id="cariProdukModal" tabindex="-1" aria-labelledby="cariProdukModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cariProdukModalLabel">Cari Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-2" placeholder="Masukkan Kode / Nama Produk" id="input-code">
                    <div class="result">
                        <ol class="list-group" id="list-product">
                            
                                    
                            
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
        const primaryTable = document.getElementById('primary-table');
        const cardDetail = document.querySelector('#card-detail');
        const cardIndex = document.querySelector('#card-index');

        const invoiceDetailBody = document.getElementById('invoice-detail-body');
        const invoiceDetailFoot = document.getElementById('invoice-detail-foot');
        const totalOrder = document.getElementById('total-order');
        
        let focusId = 0;
        let selectInvoice = 0;
        let invoiceDetailList = [];
        let newOrder = [];

        const sisaBayar = document.getElementById('sisa-bayar');
        const jumlahBayar = document.getElementById('jumlah-bayar');

        const invoicePrint = document.getElementById('invoice-print');

        const resetShow = () => {
            cardIndex.classList.remove('d-none');
            cardIndex.classList.add('d-block');

            cardDetail.classList.add('d-none');
            cardDetail.classList.remove('d-block');
        }

        const loadPrimaryTable = async () => {
            resetShow();
            let pathUrl = window.location.pathname;
            let businessId = pathUrl[1];

            primaryTable.innerHTML = `<tr class="text-center">
                                        <td colspan="3">
                                            <div class="d-flex justify-content-center">
                                                <div class="spinner-border" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>`;
            await axios.get(`/api/${businessId}/pay-later`)
            .then(res => {
                let list = '';

                res.data.data.accountReceivables.map(data => {
                    list += `<tr>
                                <td>${data.nama_konsumen}</td>
                                <td class="text-center">
                                    ${data.nomor_nota}
                                </td>
                                <td class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(data.sisa)}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-secondary" onclick="detail(${data.id})">Detail</button>
                                </td>
                            </tr>`
                });

                primaryTable.innerHTML = list;
            })
            .catch(err => {
                console.log(err);
            })
        }

        const back = () => {
            resetShow();
            loadPrimaryTable();
        }

        const setLoading = () => {
            
            invoiceDetailBody.innerHTML = `<tr class="text-center">
                                                <td colspan="4">
                                                    <div class="d-flex justify-content-center">
                                                        <div class="spinner-border" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>`
            totalOrder.innerHTML = `<div class="d-flex justify-content-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>`;
        }

        const detail = (id) => {
            focusId = id;
            cardDetail.classList.remove('d-none');
            cardDetail.classList.add('d-block');

            cardIndex.classList.remove('d-block');
            cardIndex.classList.add('d-none');

            setLoading();
            loadDetail(focusId);
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

        const loadDetail = (id) => {
            
            kode.value = '';
            namaProduk.value = '';
            harga.value = 0;
            idProduk.value = 0;
            jumlahBayar.value = 0;

            axios.get(`/api/pay-later/detail/${id}`)
            .then(res => {
                console.log(res.data.data);
                invoiceDetailList = res.data.data.invoice.products;
                selectInvoice = res.data.data.invoice;
                let listDetail = '';
                let total = 0;
                res.data.data.invoice.products.map(detail => {
                    total += detail.pivot.jumlah * detail.pivot.harga;
                    listDetail += `<tr>
                                        <td>${detail.nama_produk}</td>
                                        <td class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(detail.pivot.harga)}</td>
                                        <td class="text-center">${detail.pivot.jumlah}</td>
                                        <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(detail.pivot.jumlah * detail.pivot.harga)}</td>
                                        <td class="text-center">
                                            <button class="text-danger btn btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteOrder(${id}, ${detail.pivot.invoice_id}, ${detail.pivot.product_id})"><i class="bi bi-x-circle-fill"></i></button>
                                        </td>
                                    </tr>`
                });
                invoiceDetailBody.innerHTML = listDetail;

                totalOrder.innerHTML = `Rp. ${Intl.NumberFormat(['ban', 'id']).format(total)}`;  
                sisaBayar.innerHTML = `Rp. ${Intl.NumberFormat(['ban', 'id']).format(jumlahBayar.value - total)}`;

                jumlahBayar.addEventListener('keyup', function(){
                    jumlahBayar.value > 0 ? invoicePrint.classList.remove('disabled') : invoicePrint.classList.add('disabled');
                    sisaBayar.innerHTML = `Rp. ${Intl.NumberFormat(['ban', 'id']).format(jumlahBayar.value - total)}`;
                });

                //print Menu

                invoicePrint.addEventListener('click', function(){
                    const printInvoice = document.getElementById('print');
                    let list = '';

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
                                        <td>: ${selectInvoice.nomor}</td>
                                    </tr>
                                    <tr>
                                        <td>Kasir</td>
                                        <td>: ${invoicePrint.dataset.operator}</td>
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

                    res.data.data.invoice.products.map(product => {
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
                            <tr>
                                <td class="text-end">Bayar :</td>
                                <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(jumlahBayar.value)}</td>
                            </tr>
                            <tr style="border-bottom: solid black">
                                <td class="text-end">Sisa :</td>
                                <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(jumlahBayar.value - total)}</td>
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
                            </div>
                            `

                    printInvoice.innerHTML = list;

                    printInvoice.classList.remove('d-none');
                    app.classList.add('d-none');

                    const printBtn = document.getElementById('print-btn');
                    const batalPrint = document.getElementById('batal-print');
                    const newInvoice = document.getElementById('new-invoice');

                    let printStatus = false;
                    
                    batalPrint.addEventListener('click', function(){
                        console.log("batal cetak");
                        printInvoice.classList.add('d-none');
                        app.classList.remove('d-none');
                    })

                    newInvoice.addEventListener('click', function(){
                        window.location=`/${cariProduk.dataset.businessId}/pay-later`;
                    })

                    printBtn.addEventListener('click', function(){
                        
                        let data = {
                            total: parseInt(total),
                        }

                        printStatus
                        ? window.print()
                        : axios.post(`/api/cashier/${selectInvoice.id}/update`, data)
                            .then(res => {
                                printStatus = true;
                                console.log(res);
                                window.print();
                                batalPrint.classList.add('d-none');
                                newInvoice.classList.remove('d-none');
                                newInvoice.classList.add('d-block');
                            })
                            .catch(err => {
                                console.log(err);
                            });
                        
                    })
                });

            })
            .catch(err => {
                console.log(err);
            })
        }

        const showUpdateList = () => {
            let list = '';
            const invoiceUpdateBody = document.getElementById('invoice-update-body');
            const invoiceUpdateFoot = document.getElementById('invoice-update-foot');
            let total = 0;

            console.log(invoiceDetailList);

            invoiceDetailList.map(detail => {
                total += detail.pivot.harga * detail.pivot.jumlah;
                list += `<tr>
                            <td class="text-center">${detail.nama_produk}</td>
                            <td class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(detail.pivot.harga)}</td>
                            <td class="text-center">${detail.pivot.jumlah}</td>
                            <td class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(detail.pivot.harga * detail.pivot.jumlah)}</td>
                            <td class="text-center">
                                <button class="text-danger btn btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteOrder(${detail.pivot.invoice_id}, ${detail.pivot.product_id})"><i class="bi bi-x-circle-fill"></i></button>
                            </td>
                        </tr>`
            });

            invoiceUpdateBody.innerHTML = list;
            invoiceUpdateFoot.innerHTML = `<tr>
                                                <th class="text-end" colspan="3">Total:</th>
                                                <th class="text-end">Rp. ${Intl.NumberFormat(['ban', 'id']).format(total)}</th>
                                            </tr>`
        }

        const deleteOrder = (id, invoiceId, productId) => {
            const submitDeleteButton = document.getElementById('submit-delete-button');

            submitDeleteButton.addEventListener('click', function(){
                setLoading();
                axios.delete(`/api/invoice-detail/${invoiceId}/${productId}`)
                .then(res => {
                    
                    loadDetail(focusId);
                })
                .catch(err => {
                    console.log(err);
                })
            })
        }

        const cariProduk = document.getElementById('cari-produk');

        const errCariKode = document.getElementById('err-cari-kode');
        const kode = document.getElementById('kode');
        const namaProduk = document.getElementById('nama-produk');
        const idProduk = document.getElementById('product-id');
        const harga = document.getElementById('harga');
        const jumlah = document.getElementById('jumlah');

        

        kode.addEventListener('keyup', function(){
            console.log(kode.value);

            kode.value ? addProduct.classList.remove('disabled') : addProduct.classList.add('disabled');
        })

        //element product modal
        const inputCode = document.getElementById('input-code');
        const listProduct = document.getElementById('list-product');
        cariProduk.addEventListener('click', function()
        {
            inputCode.value = kode.value
            createListProduct(cariProduk.dataset.businessId, inputCode.value);
            
            inputCode.addEventListener('keyup', function()
            {
                createListProduct(cariProduk.dataset.businessId, inputCode.value);
            })
        })

        const createListProduct = async (businessid, productCode) => {
            listProduct.innerHTML = `<div class="d-flex justify-content-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>`;
            let list = '';           

            await getListProduct(businessid, productCode);
            let products = listproducts;

            products.map(product=>{
                list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">${product.nama_produk}</div>
                                    <small>${product.kode}</small>
                                </div>
                                <button 
                                    class="btn btn-sm btn-primary" 
                                    onclick="selectProduct('${product.id}','${product.nama_produk}', '${product.kode}', '${product.jual}')"
                                    data-bs-dismiss="modal"
                                    >Pilih</button>
                                    
                            </li>`
            })
            listProduct.innerHTML = list;
        }

        const selectProduct = (id, nama, kodeBarang, jual) => {
            kode.value = kodeBarang;
            namaProduk.value = nama;
            harga.value = jual;
            idProduk.value = id;

            // reset 
            errCariKode.classList.add('d-none');
            kode.classList.remove('is-invalid');

            addProduct.classList.remove('disabled');
        }

        const getListProduct = async (businessid, productCode) => 
        {
            await axios.get(`/api/${businessid}/product?search=${productCode}`)
            .then(res => {
                listproducts = [];
                listproducts = [...listproducts, ...res.data.data]
            })
            .catch(err => {
                console.log(err);
            })
        }

        const addProduct = document.getElementById('add-product');

        addProduct.addEventListener('click', function(){
            let data = {
                'harga' : harga.value,
                'jumlah' : jumlah.value,
                'productId' : idProduk.value,
                'invoiceId' : selectInvoice.id,
                'id' : focusId
            }

            setLoading();

            axios.post(`/api/cashier/add-order`, data)
            .then(res => {
                loadDetail(focusId);
                addProduct.classList.add('disabled');
            })
            .catch(err => {
                console.log();
            })
        });

        window.addEventListener('load', async function (){                                    
            loadPrimaryTable();
        })
    </script>
@endsection
