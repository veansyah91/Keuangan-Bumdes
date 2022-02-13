@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Kasir') }}</div>

                <div class="card-body">
                    <a class="btn btn-primary" href="{{ route('business.cashier.index', $business->id) }}">Nota Baru</a>
                    <hr>
                    <div class="row justify-content-start">
                        <div class="col-12 col-md-6">
                            <div class="row fs-3">
                                <label for="nomor-nota" class="col-sm-6 col-form-label">Nomor Nota :</label>
                                <div class="col-sm-6">
                                    <input type="text" readonly class="form-control-plaintext" id="nomor-nota" value="{{ $invoice }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="input-group mb-3">
                                <button class="btn btn-outline-secondary" type="button" data-business-id="{{ $business->id }}" id="cari-pelanggan" data-bs-toggle="modal" data-bs-target="#cariKonsumenModal">Cari</button>
                                <input type="text" class="form-control" id="nama-pelanggan" placeholder="Pelanggan" aria-label="Pelanggan" aria-describedby="cari-pelanggan">
                                
                                <input type="text" hidden id="id-pelanggan">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row justify-content-between">
                        <div class="col-12 col-md-6">
                            <div class="fw-bold mb-1">
                                Tambah Item
                            </div>
                            <div class="card">
                                <div class="card-body">
                                        <form method="post" id="form-search-product">
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
                                        </form>
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

    <!--invoice Modal -->
    <div class="modal fade" id="bayarModal" tabindex="-1" aria-labelledby="bayarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bayarModalLabel">Bayar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="result">
                        <ol class="list-group" id="list-invoice-pay">
                            
                        </ol>
                    </div>
                    <div id="amount">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary disabled" id="invoice-print" data-bs-dismiss="modal" data-operator="{{ Auth::user()['name'] }}" data-business-name="{{ $business['nama'] }}" data-alamat="{{ $identity['nama_desa'] }}"><i class="bi bi-printer"></i></button>
                    <button type="button" class="btn btn-success disabled" id="invoice-save" data-bs-dismiss="modal" data-operator="{{ Auth::user()['name'] }}"><i class="bi bi-save"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        let listproducts = [];
        let detailProducts = [];
        let total = 0;
        let sisaBayar = 0;

        const app = document.getElementById('app');
        const nomorNota = document.getElementById('nomor-nota');
        const cariPelanggan = document.getElementById('cari-pelanggan');
        const namaPelanggan = document.getElementById('nama-pelanggan');
        const cariProduk = document.getElementById('cari-produk');
        
        const idPelanggan = document.getElementById('id-pelanggan');

        // element customer modal
        const inputCustomer = document.getElementById('input-customer');
        const listCustomer = document.getElementById('list-customer');

        //element product modal
        const inputCode = document.getElementById('input-code');
        const listProduct = document.getElementById('list-product');

        //detail Produk
        const detailProduct = document.getElementById('detail-product');

        const formSearchInput = document.getElementById('form-search-product');
        const kode = document.getElementById('kode');
        const errCariKode = document.getElementById('err-cari-kode');
        const namaProduk = document.getElementById('nama-produk');
        const idProduk = document.getElementById('product-id');
        const harga = document.getElementById('harga');
        const jumlah = document.getElementById('jumlah');
        const addProduct = document.getElementById('add-product');
        const amount = document.getElementById('amount');

        const bayarBtn = document.getElementById('bayar-btn');
        const listInvoicePay = document.getElementById('list-invoice-pay');

        const invoicePrint = document.getElementById('invoice-print');
        const invoiceSave = document.getElementById('invoice-save');

        formSearchInput.addEventListener('submit', async function(e) {
            e.preventDefault();
            await getListProduct(cariProduk.dataset.businessId, kode.value);
            let product = listproducts.length > 0 ? listproducts[0] : false;
            if (product && kode.value) {
                namaProduk.value = product.nama_produk;
                kode.value = product.kode;
                harga.value = product.jual;
            } else {
                errCariKode.classList.remove('d-none');
                kode.classList.add('is-invalid');
            }
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

        cariPelanggan.addEventListener('click', function()
        {
            setUpListCustomer(cariPelanggan.dataset.businessId, inputCustomer.value);
            inputCustomer.addEventListener('keyup', function(){
                setUpListCustomer(cariPelanggan.dataset.businessId, inputCustomer.value);                
            })
            
        })

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
            axios.get(`/api/stock/${id}`)
            .then(res => {
                kode.value = kodeBarang;
                namaProduk.value = nama;
                harga.value = jual;
                idProduk.value = id;

                res.data.status == 200 ? jumlah.setAttribute('max', res.data.data.jumlah) : '';
            })
            .catch(err => {
                console.log(err);
            });

            // reset 
            errCariKode.classList.add('d-none');
            kode.classList.remove('is-invalid')
        }

        cariProduk.addEventListener('click', function()
        {
            inputCode.value = kode.value
            createListProduct(cariProduk.dataset.businessId, inputCode.value);
            
            inputCode.addEventListener('keyup', function()
            {
                setTimeout(() => {
                    createListProduct(cariProduk.dataset.businessId, inputCode.value);
                }, 200);
                
            })
        })

        const createListInvoiceItem = (data) => {
            let list = '';
            detailProducts.map((detailProduct, index) => {
                let currency = Intl.NumberFormat(['ban', 'id']).format(detailProduct.harga);
                let total = Intl.NumberFormat(['ban', 'id']).format(detailProduct.jumlah * detailProduct.harga);
                list += `<tr>
                                <td class="ms-2 me-auto">
                                    <div key="${index}">${detailProduct.namaProduk}</div>
                                </td>
                                <td class="ms-2 me-auto">
                                    <div>${currency} x ${detailProduct.jumlah}</div>
                                </td>
                                <td class="ms-2 me-auto">
                                    <div>${total}</div>
                                </td>
                                <td class="align-middle">
                                    <button 
                                    class="btn btn-sm btn-danger" 
                                    onclick="deleteInvoiceList(${index}, ${detailProduct.harga * detailProduct.jumlah})"
                                    >Hapus</button>
                                </td>
                            </tr>`
            });

            detailProduct.innerHTML = list
        }

        addProduct.addEventListener('click', function(){
            if (!namaPelanggan.value) {
                namaPelanggan.classList.add('is-invalid');
                return
            }

            if (!kode.value) {
                errCariKode.classList.remove('d-none');
                kode.classList.add('is-invalid');
                return
            }

            let jumlahOrder = jumlah.value > parseInt(jumlah.getAttribute('max')) ? parseInt(jumlah.getAttribute('max')) : parseInt(jumlah.value);
            let data = [{
                kode : kode.value,
                namaProduk : namaProduk.value,
                idProduk : parseInt(idProduk.value),
                harga : parseInt(harga.value),
                jumlah : jumlahOrder
            }]

            total += harga.value * jumlahOrder;
            let currency = Intl.NumberFormat(['ban', 'id']).format(total);
            amount.innerHTML = `Rp. ${currency}`;

            detailProducts = [...detailProducts, ...data];

            createListInvoiceItem(detailProducts);

            // reset product of invoice
            kode.value = '';
            namaProduk.value = '';
            harga.value = '';
            jumlah.value = 1;
            errCariKode.classList.add('d-none');
            kode.classList.remove('is-invalid');
            namaPelanggan.classList.remove('is-invalid');
            bayarBtn.classList.remove('disabled');

        })

        const deleteInvoiceList = (index, jumlah) => {
            total -= jumlah;
            let currency = Intl.NumberFormat(['ban', 'id']).format(total);
            amount.innerHTML = `Rp. ${currency}`;
            detailProducts.splice(index, 1);

            total < 1 ? bayarBtn.classList.add('disabled') : '';
            createListInvoiceItem(detailProducts);
            // reset product of invoice
            kode.value = '';
            namaProduk.value = '';
            harga.value = '';
            jumlah.value = 1;
        }

        bayarBtn.addEventListener('click', function(){
            let list = '';  

            detailProducts.map(product => {
                let currency = Intl.NumberFormat(['ban', 'id']).format(product.harga * product.jumlah)
                list += `<li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div>${product.namaProduk}</div>
                                </div>

                                <div class="ms-2 me-auto">
                                    <div>${product.harga} x ${product.jumlah}</div>
                                </div>

                                <div class="ms-2">
                                    <div>${currency}</div>
                                </div>
                                    
                            </li>`

                
            });

            let totalCurrency = Intl.NumberFormat(['ban', 'id']).format(total);
            list += `<li class="list-group-item d-flex justify-content-between align-items-start fs-4">
                        <div class="ms-2 me-auto fw-bold">
                            <div>Jumlah</div>
                        </div>

                        <div class="ms-2 fw-bold">
                            <div>Rp. ${totalCurrency}</div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start fs-4">
                        <div class="ms-2 me-auto fw-bold">
                            <div>Bayar</div>
                        </div>

                        <div class="ms-2 fw-bold">
                            <div><input id="input-bayar" type="text" class="form-control input-bayar text-end" aria-label="Input Bayar" aria-describedby="inputGroup-sizing-default"></div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start fs-4">
                        <div class="ms-2 me-auto fw-bold">
                            <div>Sisa</div>
                        </div>

                        <div class="ms-2 fw-bold">
                            <div>Rp. <span id="sisa-bayar-value"></span></div>
                        </div>
                    </li>`

            listInvoicePay.innerHTML = list;
            
            const inputBayar = document.getElementById('input-bayar');
            const sisaBayarValue = document.getElementById('sisa-bayar-value');

            inputBayar.addEventListener('keyup', function(){
                
                sisaBayar = inputBayar.value - total;

                let currency = Intl.NumberFormat(['ban', 'id']).format(sisaBayar);

                sisaBayarValue.innerText = currency;
                inputBayar.value ? invoicePrint.classList.remove('disabled') : invoicePrint.classList.add('disabled');
                inputBayar.value ? invoiceSave.classList.remove('disabled') : invoiceSave.classList.add('disabled');
            });
        });

        invoiceSave.addEventListener('click', function(){
            let data = {
                nomorNota: nomorNota.value,
                namaPelanggan: namaPelanggan.value,
                products: detailProducts,
                total: parseInt(total),
                sisa: parseInt(sisaBayar),
                operator: invoiceSave.dataset.operator
            }

            console.log(data);

            axios.post(`/api/${cariProduk.dataset.businessId}/cashier`, data)
            .then(res => {
                console.log(res.data.data);
                window.location=`/${cariProduk.dataset.businessId}/cashier`;
            })
            .catch(err => {
                console.log(err);
            })
        })

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
                                <td>: ${nomorNota.value}</td>
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

            detailProducts.map(product => {
                list += `<tr>
                            <td colspan="2">${product.namaProduk}</td>
                        </tr>
                        <tr>
                            <td>${Intl.NumberFormat(['ban', 'id']).format(product.harga)} x ${product.jumlah}</td>
                            <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(product.harga * product.jumlah)}</td>
                        </tr>`
            })

            list += `<tr style="border-top: solid black">
                        <td class="text-end">Total :</td>
                        <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(total)}</td>
                    </tr>
                    <tr>
                        <td class="text-end">Bayar :</td>
                        <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(total + sisaBayar)}</td>
                    </tr>
                    <tr style="border-bottom: solid black">
                        <td class="text-end">Sisa :</td>
                        <td class="text-end">${Intl.NumberFormat(['ban', 'id']).format(sisaBayar)}</td>
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

            printInvoice.innerHTML = list

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
                window.location=`/${cariProduk.dataset.businessId}/cashier`;
            })

            printBtn.addEventListener('click', function(){
                
                let data = {
                    nomorNota: nomorNota.value,
                    namaPelanggan: namaPelanggan.value,
                    products: detailProducts,
                    total: total,
                    sisa: sisaBayar,
                    operator: invoiceSave.dataset.operator
                }

                printStatus
                ? window.print()
                : axios.post(`/api/${cariProduk.dataset.businessId}/cashier`, data)
                    .then(res => {
                        printStatus = true;
                        window.print();

                        batalPrint.classList.add('d-none');
                        newInvoice.classList.remove('d-none');
                        newInvoice.classList.add('d-block');
                    })
                    .catch(err => {
                        console.log(err);
                    })

                
            })
        });

        window.addEventListener('load', function (){
            let currency = Intl.NumberFormat(['ban', 'id']).format(total);
            amount.innerHTML = `Rp. ${currency}`;
        })

    </script>
@endsection
