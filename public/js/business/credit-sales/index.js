const searchInput = document.querySelector('#search-input');

const countData = document.querySelector('#count-data');
const prevBtn = document.querySelector('#prev-page');
const nextBtn = document.querySelector('#next-page');

const listData = document.querySelector('#list-data');

const dateDetail = document.querySelector('#date-detail');
const noRefDetail = document.querySelector('#no-ref-detail');
const descriptionDetail = document.querySelector('#description-detail');
const detailDetail = document.querySelector('#detail-detail');
const authorDetail = document.querySelector('#author-detail');
const authorDetailLabel = document.querySelector('#author-detail-label');
const contentDetail = document.querySelector('#content-detail');
const totalDebitDetail = document.querySelector('#total-debit-detail');
const totalCreditDetail = document.querySelector('#total-credit-detail');

const dateRange = document.querySelector('#date-range');
const startFilter = document.querySelector('#start-filter');
const endFilter = document.querySelector('#end-filter');

const selectStatus = document.querySelector('#select-status');

let formData = {
    no_ref : '',
    date: '',
    account: {
        id: null,
        name: ''
    },
    contact : {
        id: null,
        nik: '',
        name: '',
    },
    product: {
        id: null,
        name: '',
        unit_price: 0
    },
    credit_application:{
        id: null,
        no_ref: '',
    },
    value: 0,
    profit: 0,
    other_cost: 0,
    installment: 0,
    tenor: 1,
    downpayment: 0,
    due_date: ''
}

//component modal input
const createModalLabel = document.querySelector('#createModalLabel');
const dateInput = document.querySelector('#date-input');
const contactInput = document.querySelector('#contact-input');
const nameInput = document.querySelector('#name-input');
const productInput = document.querySelector('#product-input');
const tenorInput = document.querySelector('#tenor-input');
const contactList = document.querySelector('#contact-list');
const productList = document.querySelector('#product-list');
const noRefInput = document.querySelector('#no-ref-input');
const valueInput = document.querySelector('#value-input');
const installmentInput = document.querySelector('#installment-input');
const downpaymentInput = document.querySelector('#downpayment-input');
const profitInput = document.querySelector('#profit-input');
const costInput = document.querySelector('#cost-input');
const unitPriceInput = document.querySelector('#unit-price-input');
const termInput = document.querySelector('#term-input');
const accountInput = document.querySelector('#account-input');
const btnSubmit = document.querySelector('#btn-submit');
const btnSubmitLabel = document.querySelector('#btn-submit-label');

let search = '';
let page = 1;
let isUpdate = false;
let deleteId = null;
let updateId = null;

let statusQuery = '';

let date_from, date_to, thisWeek, thisMonth, thisYear;

async function searchForm(event){
    event.preventDefault();

    search = searchInput.value;

    await showData();
}

function setDefault(){
    search = '';
    page = 1;
    deleteId = null;
    updateId = null;
    isUpdate = false;

    selectStatus.value = 'all';
    statusQuery = '';

    filterBy = 'this month';
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = 1;
    thisYear = '';

    const date = new Date();
    const nextMonth = new Date(addMonths(date, formData.downpayment > 0 ? formData.tenor : formData.tenor - 1));
    var day = nextMonth.getDate();
    var month = nextMonth.getMonth() + 1;
    var year = nextMonth.getFullYear();

    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    formData = {
        no_ref : '',
        date: dateNow(),
        account: {
            id: null,
            name: ''
        },
        contact : {
            id: null,
            nik: '',
            name: '',
        },
        product: {
            id: null,
            name: '',
            unit_price: 0
        },
        credit_application:{
            id: null,
            no_ref: '',
        },
        value: 0,
        profit: 0,
        other_cost: 0,
        installment: 0,
        tenor: 1,
        downpayment: 0,

        due_date: year + "-" + month + "-" + day
    }
    
    validateInputData();
}

function validateInputData(){
    let is_validated = false;

    if (formData.no_ref && formData.contact.name && formData.product.id && formData.profit > 0 && formData.installment > 0)    {
        is_validated = true;
        if (formData.downpayment > 0 && !formData.account.id) {
            is_validated = false;
        }
    }

    if (is_validated) {
        btnSubmit.classList.remove('d-none');
    } else {
        btnSubmit.classList.add('d-none');
    }
}

function setDefaultComponentValue(){
    dateInput.value = formData.date;
    contactInput.value = formData.contact.nik;
    nameInput.value = formData.contact.name;
    noRefInput.value = formData.no_ref;
    valueInput.value = formatRupiah(formData.value.toString());
    termInput.value = formData.due_date;
    tenorInput.value = formData.tenor;
    unitPriceInput.value = formatRupiah(formData.product.unit_price.toString());
    profitInput.value = formatRupiah(formData.profit.toString());
    installmentInput.value = formatRupiah(formData.installment.toString());
    costInput.value = formatRupiah(formData.other_cost.toString());
    downpaymentInput.value = formatRupiah(formData.downpayment.toString());
    productInput.value = formData.product.name;

    document.querySelector('#credit-application-input').value = formData.credit_application.no_ref;
    document.querySelector('#account-input').value = formData.account.name;

    btnSubmitLabel.innerHTML = `Simpan`;
    btnSubmit.removeAttribute('disabled');
}

async function typeInputChange(value){
    formData.type = value.value;

    let res = await getNewNoRef(formData.type);
    formData.no_ref = res;
    noRefInput.value = res;
    validateInputData();
}

function changeTerm(value){
    formData.due_date = value.value;
}

function goToPrintInvoice(id)
{
    window.open(`/${business}/credit-sales/${id}/print-detail`)
}

const submitData = async (event) => {
    event.preventDefault();

    btnSubmit.setAttribute('disabled','disabled');
    btnSubmitLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>`;

    if (isUpdate) {
        let result = await putData(formData, updateId);
        
        let message = `Penjualan Kredit ${formData.no_ref} Berhasil Diubah`;
        
        myToast(message, 'success');
        goToPrintInvoice(result.id);
        
        validateInputData();
    } else {
        let result = await postData(formData);

        let message = `Penjualan Kredit ${result.no_ref} Berhasil Ditambahkan`;
            
        myToast(message, 'success');
        goToPrintInvoice(result.id);

        setDefault();
    }                
    await showData();  
    let url = `/api/${business}/no-ref-credit-sales-recomendation?date=${formData.date}`;

    let noRef = await getNewNoRef(url);

    formData.no_ref = noRef;  
    setDefaultComponentValue();

}

async function prevButton(){
    page--;
    await showData();
}

async function nextButton(){
    page++;
    await showData();
}

async function showData(){
    try {
        let url = `/api/${business}/credit-sales?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}${statusQuery}`;

        let response = await getData(url);
        
        countData.innerHTML = `${response.data.length ? (page * response.per_page)-(response.per_page-1) : 0}-${response.data.length + (page * response.per_page)-response.per_page} dari ${response.total}`;

        if (page == 1) {
            prevBtn.classList.add('disabled');
            prevBtn.setAttribute('disabled','disabled');
        }else{
            prevBtn.classList.remove('disabled');
            prevBtn.removeAttribute('disabled');
        }

        if (page == response.last_page) {
            nextBtn.classList.add('disabled');
            nextBtn.setAttribute('disabled','disabled');
        }else{
            nextBtn.classList.remove('disabled');
            nextBtn.removeAttribute('disabled');
        }

        if (response.data.length < 1) {
            listData.innerHTML = `
                            <div class="row mt-2 text-gray">
                                <div class="col-12 text-center p-2 border border-white fst-italic">
                                    Tidak Ada Data
                                </div>
                            </div>`
        } else {
            let list = '';
            response.data.map((res, index) => {
            let status;
            let statusColor;
    
            if (res.is_paid_off) {
                status = 'Selesai';
                statusColor = 'text-primary';
            } else {
                status = 'Berjalan';
                statusColor = 'text-success';
            }
            
            list += `
            <div class="d-flex d-md-none justify-content-between border-top border-bottom py-2">
                <div style="width:10%" class="my-auto">
                    <div class="btn-group dropstart">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${index}">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleContact(${res.id})" >
                                    <div class="d-flex gap-3 align-items-center justify-conter-start text-info">
                                        <div><i class="bi bi-search"></i></div>
                                        <div>Detail</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/${business}/credit-sales/${res.id}/card" target="_blank">
                                    <div class="d-flex gap-3 align-items-center justify-conter-start text-secondary">
                                        <div><i class="bi bi-card-list"></i></div>
                                        <div>Kartu Pembayaran</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#createModal" onclick="editData(${res.id})" >
                                    <div class="d-flex gap-3 align-items-center justify-conter-start text-success">
                                        <div><i class="bi bi-pencil-square"></i></div>
                                        <div>Ubah</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteData(${res.id})" >
                                    <div class="d-flex gap-3 align-items-center justify-conter-start text-danger">
                                        <div><i class="bi bi-trash"></i></div>
                                        <div>Hapus</div>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div style="width:50%" class="my-auto">
                    <div class="font-bold">
                        ${res.contact_name}
                    </div>
                    <div class="">
                        <small>Kode: ${res.no_ref}</small>
                    </div>
                </div>
                <div style="width:40%" class="my-auto text-end">
                    <div>
                        <small>Nilai (IDR) :</small>
                    </div><div class="font-bold">
                        ${formatRupiah(res.invoice.value.toString())}
                    </div>
                    <div>
                        <small>Status: <span class="${statusColor}"> ${status}  </span></small>
                    </div>
                </div>
                
            </div>

            <div class="d-md-flex d-none justify-content-between border-top border-bottom py-2 px-1 content-data">
                <div style="width:5%" class="px-2 my-auto">
                    <div class="dropdown">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleContact(${res.id})" >
                                    <div class="d-flex gap-3 align-items-center justify-conter-start text-info">
                                        <div><i class="bi bi-search"></i></div>
                                        <div>Detail</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/${business}/credit-sales/${res.id}/card" target="_blank">
                                    <div class="d-flex gap-3 align-items-center justify-conter-start text-secondary">
                                        <div><i class="bi bi-card-list"></i></div>
                                        <div class="col">Kartu Pembayaran</div>
                                    </div>
                                </a>
                            </li>
                           <li>
                                <button class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#createModal" onclick="editData(${res.id})">
                                    <div class="d-flex gap-3 align-items-center justify-conter-start text-success">
                                        <div><i class="bi bi-pencil-square"></i></div>
                                        <div>Ubah</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteData(${res.id})" >
                                    <div class="d-flex gap-3 align-items-center justify-conter-start text-danger">
                                        <div><i class="bi bi-trash"></i></div>
                                        <div>Hapus</div>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div style="width:10%" class="px-2 my-auto">${dateReadable(res.date)}</div>
                <div style="width:15%" class="px-2 my-auto">${res.no_ref}</div>
                <div style="width:15%" class="px-2 my-auto">${res.contact.phone}</div>
                <div style="width:15%" class="px-2 my-auto">${res.contact_name}</div>
                <div style="width:15%" class="px-2 my-auto text-center">${res.tenor}</div>
                <div style="width:15%" class="px-2 my-auto text-end">${formatRupiah(res.invoice.value.toString())}</div>
                <div style="width:14%" class="px-2 my-auto text-center ${statusColor}">${status}</div>
                
            </div>`
            });

            listData.innerHTML = list;
        }

    } catch (error) {
         console.log(error);
    }
    
}

async function changeDate(value){
    let url = `/api/${business}/no-ref-credit-sales-recomendation?date=${value.value}`;

    let noRef = await getNewNoRef(url);

    formData.date = value.value;
    formData.no_ref = noRef;

    let refDate = new Date(formData.date);

    const nextMonth = new Date(addMonths(refDate, formData.tenor));
    var day = nextMonth.getDate();
    var month = nextMonth.getMonth() + 1;
    var year = nextMonth.getFullYear();

    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    formData.due_date = year + "-" + month + "-" + day;

    updateDueDate();
    changeFormValue();
    setDefaultComponentValue();
}

const addData = async () => {
    isUpdate = false;
    setDefault();
    
    let url = `/api/${business}/no-ref-credit-sales-recomendation?date=${formData.date}`;

    let noRef = await getNewNoRef(url);

    formData.no_ref = noRef;

    setDefaultComponentValue();

    createModalLabel.innerHTML = "Tambah Data";
}

const editData = async (id) => {
    createModalLabel.innerHTML = "Ubah Data";
    let url = `/api/${business}/credit-sales/${id}`;
    let res = await getData(url);

    isUpdate = true;
    updateId = id;

    let installment = Math.round((res.invoice.value - res.downpayment) / res.tenor);

    formData = {
        no_ref : res.no_ref,
        date: res.date,
        account: {
            id: res.account ? res.account.id : null,
            name: res.account ? res.account.name : ''
        },
        contact : {
            id: res.contact.id,
            nik: res.contact.detail.nik,
            name: res.contact.name
        },
        product: {
            id: res.invoice.products[0].id,
            name: res.invoice.products[0].name,
            unit_price: res.invoice.products[0].unit_price
        },
        credit_application:{
            id: res.credit_application.id,
            no_ref: res.credit_application.no_ref,
        },
        profit: res.credit_application.profit,
        other_cost: res.credit_application.other_cost ?? 0,
        installment: installment,
        tenor: res.credit_application.tenor,
        downpayment: res.downpayment,
        value : res.invoice.value,
        status: res.status,
        due_date: res.due_date,
    }

    if (formData.downpayment > 0) {
        document.querySelector('#account-input-dropdown').classList.remove('d-none');
    } else {
        document.querySelector('#account-input-dropdown').classList.add('d-none');
    }

    setDefaultComponentValue();
    validateInputData();

}

function setStatus(value)
{
    formData.status = value.value;
}

function deleteData(id){
    deleteId = id;
}

async function submitDeleteData(){  
    try {
        let response = await destroyData(deleteId);

        let message = `Pengajuan Pinjaman ${response.no_ref} Berhasil Dihapus`;
        
        myToast(message, 'success');

        setDefault();
        await showData();

    } catch (error) {
        myToast(error.response.data.errors.message, 'danger');
    }
}

async function showSingleContact(id){
    try {
        let url = `/api/${business}/credit-sales/${id}`;
        let res = await getData(url);

        let status;
        let statusColor;

        if (res.is_paid_off) {
            status = 'Selesai';
            statusColor = 'text-primary';
        } else {
            status = 'Berjalan';
            statusColor = 'text-success';
        }

        formData = {
            no_ref : res.no_ref,
            date: res.date,
            account: {
                id: res.account?.id,
                name: res.account?.name
            },
            contact : {
                id: parseInt(res.contact.id),
                nik: res.contact.detail.nik,
                name: res.contact.name,
                phone: res.contact.phone
            },
            credit_application:{
                id: res.credit_application?.id,
                no_ref: res.credit_application?res.credit_application.no_ref : '',
            },
            product: {
                id: res.invoice.products[0].id,
                name: res.invoice.products[0].name,
                unit_price: res.invoice.products[0].unit_price
            },
            value: res.debit,
            profit: res.credit_application?.profit,
            other_cost: res.credit_application?.other_cost,
            installment: Math.round((res.invoice.value - res.downpayment) / res.tenor),
            tenor:  res.credit_application?.tenor,
            downpayment: res.credit_application?.downpayment,
            due_date: res.due_date
        }
        
        document.querySelector('#status-detail').innerHTML = `
            Status: <span class="fw-bold ${statusColor}">${status}</span>
        `;

        document.querySelector('#name-detail').innerHTML = `${res.contact_name}`;

        document.querySelector('#product-name-detail').innerHTML = `: ${res.invoice.products[0].name}`;
        document.querySelector('#unit-price-detail').innerHTML = `: ${formatRupiah(res.invoice.products[0].unit_price.toString())}`;

        document.querySelector('#tenor-detail').innerHTML = `: ${formatRupiah(res.tenor.toString())}`;

        document.querySelector('#downpayment-detail').innerHTML = `: ${formatRupiah(res.downpayment.toString())}`;

        let installment = Math.round((res.invoice.value - res.downpayment) / res.tenor);

        document.querySelector('#installment-detail').innerHTML = `: ${formatRupiah(installment.toString())}`;


        document.querySelector('#date-detail').innerHTML = `: ${dateReadable(res.date)}`;

        document.querySelector('#author-detail').innerHTML = `: ${res.author}`;

        document.querySelector('#value-detail').innerHTML = `: ${formatRupiah(res.invoice.value.toString())}`;

        document.querySelector('#term-detail').innerHTML = `: ${dateReadable(res.due_date)}`;

        document.querySelector('#phone-detail').innerHTML = res.contact.phone??'-';
        document.querySelector('#nkk-detail').innerHTML = res.contact.detail?.nkk ?? '-';
        document.querySelector('#nik-detail').innerHTML = res.contact.detail?.nik ?? '-';
        document.querySelector('#address-detail').innerHTML = res.contact.address?? '-';
        document.querySelector('#village-detail').innerHTML = res.contact.detail?.village ?? '-';
        document.querySelector('#district-detail').innerHTML = res.contact.detail?.district ?? '-';
        document.querySelector('#regency-detail').innerHTML = res.contact.detail?.regency ?? '-';
        document.querySelector('#province-detail').innerHTML = res.contact.detail?.province ?? '-';

    } catch (error) {
        console.log(error);
    }
}

async function showListData(value){
    let url = `/api/contact-with-detail?search=${value}&type=Customer`
    let res = await getData(url);

    let list = '';

    res.map(contact=>{
        list += `
        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" onclick="selectData(this)" data-name="${contact.contact.name}" data-id="${contact.contact.id}" data-ref="${contact.contact.detail.nik}">
            <div class="fw-bold">
                ${contact.contact.detail.nik}
            </div>
            <div>
                ${contact.contact.name}
            </div>
            
        </button>
        `
    });

    contactList.innerHTML = list ? list : `
    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" disabled>
        No Contact
    </button>
    `;
}

async function selectData(value)
{
    formData.contact.id = parseInt(value.dataset.id);
    formData.contact.name = value.dataset.name;
    formData.contact.nik = value.dataset.ref;

    contactInput.value = value.dataset.ref;
    nameInput.value = value.dataset.name;

    validateInputData();
}

function changeNoRef(value)
{
    formData.no_ref = value.value;
    validateInputData();
}

async function showContactDropdown(value){
    contactList.classList.remove('d-none');
    document.querySelector('#product-input-dropdown').classList.remove('position-relative');
    await showListData(value.value);
}

function changeContact(value)
{
    value.value = formData.contact.nik;
}

async function changeContactDropdown(value){
    setTimeout(async () => {
        await showListData(value.value)
    }, 200);
}

async function showListProduct(value){
    let url = `/api/${business}/product?search=${value}&type=Customer`
    let res = await getData(url);
    let list = '';

    res.data.map(product=>{
        list += `
        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" onclick="selectProduct(this)" data-name="${product.name}" data-id="${product.id}" data-value="${product.unit_price}">
            <div class="mb-auto">
                ${product.name}
            </div>
            <div>
                <div><small>Nilai Perolehan</small></div>
                <div class="fw-bold">${formatRupiah(product.unit_price.toString())}</div>
            </div>            
        </button>
        `
    });

    productList.innerHTML = list ? list : `
    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" disabled>
        No Product
    </button>
    `;
}

async function showProductDropdown(value){
    productList.classList.remove('d-none');
    document.querySelector('#product-input-dropdown').classList.add('position-relative');
    await showListProduct(value.value);
}

function changeProduct(value)
{
    value.value = formData.product.name;
}

async function changeProductDropdown(value){
    setTimeout(async () => {
        await showListProduct(value.value)
    }, 200);
}

function selectProduct(value){
    formData.product = {
        id: value.dataset.id,
        name: value.dataset.name,
        unit_price: parseInt(value.dataset.value)
    }

    productInput.value = formData.product.name;
    unitPriceInput.value = formatRupiah(formData.product.unit_price.toString());
    validateInputData();
}

function changeFormValue()
{
    formData.value = ((formData.profit / 100) * formData.product.unit_price * formData.tenor) + formData.other_cost + formData.product.unit_price;


    formData.installment = Math.round((formData.value - formData.downpayment) / formData.tenor);
}

async function changeTotal(value)
{
    formData.product.unit_price = parseInt(toPrice(value.value));

    changeFormValue();
    setDefaultComponentValue();
    validateInputData();
}

function changeProfit(value)
{
    formData.profit = parseInt(toPrice(value.value));

    changeFormValue();
    setDefaultComponentValue();
    validateInputData();

}

function changeCost(value)
{
    formData.other_cost = parseInt(toPrice(value.value));

    changeFormValue();
    setDefaultComponentValue();
    validateInputData();
}

function changeDownpayment(value)
{
    formData.downpayment = parseInt(toPrice(value.value));

    updateDueDate();
    
    if (formData.downpayment > 0) {
        document.querySelector('#account-input-dropdown').classList.remove('d-none');
    } else {
        document.querySelector('#account-input-dropdown').classList.add('d-none');
    }

    changeFormValue();
    setDefaultComponentValue();
    validateInputData();
}

function updateDueDate()
{
    let refDate = new Date(formData.date);

    const nextMonth = new Date(addMonths(refDate, formData.downpayment > 0 ? formData.tenor : formData.tenor - 1));
    var day = nextMonth.getDate();
    var month = nextMonth.getMonth() + 1;
    var year = nextMonth.getFullYear();

    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    formData.due_date = year + "-" + month + "-" + day;
}

function changeTenor(value)
{
    formData.tenor = parseInt(value.value);
    
    updateDueDate();

    changeFormValue();
    setDefaultComponentValue();
    validateInputData();
}

async function showListAccount(value){
    let {data: res} = await getAccounts(value, 1);

    let list = '';

    res.map(account=>{
        list += `
        <button type="button" class="list-group-item list-group-item-action justify-content-between" onclick="selectAccount(this)" data-name="${account.name}" data-id="${account.id}" data-code="${account.code}">
            <div>
                ${account.name}
            </div>
            
        </button>
        `
    });

    document.querySelector('#account-list').innerHTML = list ? list : `
    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" disabled>
        No Account
    </button>
    `;
}

async function showAccountDropdown(value)
{
    document.querySelector('#account-list').classList.remove('d-none');
    await showListAccount(value.value);
}

async function changeAccountDropdown(value)
{
    setTimeout(async () => {
        await showListAccount(value.value);
    }, 200);
}

function selectAccount(value){
    formData.account = {
        id: value.dataset.id,
        name: value.dataset.name
    }

    accountInput.value = formData.account.name; 
    validateInputData()
}

function changeAccount (value){
    value.value = formData.account.name;
    validateInputData();
}

function selectCreditApplication(value){
    formData = {...formData,
        contact : {
            id: value.dataset.nameId,
            nik: value.dataset.nik,
            name: value.dataset.name,
        },
        product: {
            id: value.dataset.productId,
            name: value.dataset.productName,
            unit_price: parseInt(value.dataset.productUnitPrice)
        },
        credit_application:{
            id: value.dataset.id,
            no_ref: value.dataset.ref,
        },
        value: parseInt(value.dataset.value),
        profit: parseInt(value.dataset.profit),
        other_cost: parseInt(value.dataset.otherCost),
        installment: Math.round((parseInt(value.dataset.value) - parseInt(value.dataset.downpayment)) / parseInt(value.dataset.tenor)),
        tenor: parseInt(value.dataset.tenor),
        downpayment: parseInt(value.dataset.downpayment),
    }

    let refDate = new Date(formData.date);

    const nextMonth = new Date(addMonths(refDate, formData.tenor));
    var day = nextMonth.getDate();
    var month = nextMonth.getMonth() + 1;
    var year = nextMonth.getFullYear();

    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    formData.due_date = year + "-" + month + "-" + day;

    if (formData.downpayment > 0) {
        document.querySelector('#account-input-dropdown').classList.remove('d-none');
    } else {
        document.querySelector('#account-input-dropdown').classList.add('d-none');
        
    }

    setDefaultComponentValue();
    validateInputData();
}

async function showCreditApplication(value){
    let url = `/api/${business}/credit-application?search=${value}&status=pending`;

    let {data: res} = await getData(url);

    let list = '';

    res.map(contact=>{
        list += `
        <button type="button" 
            class="list-group-item list-group-item-action d-flex justify-content-between" 
            onclick="selectCreditApplication(this)" 
            data-name="${contact.contact.name}" 
            data-nik="${contact.contact.detail.nik}" 
            data-name-id="${contact.contact.id}" 
            data-id="${contact.id}" 
            data-ref="${contact.no_ref}" 
            data-value="${contact.value}" 
            data-profit="${contact.profit}" 
            data-tenor="${contact.tenor}" 
            data-downpayment="${contact.downpayment ?? 0}" 
            data-due-date="${contact.due_date}" 
            data-other-cost="${contact.other_cost ?? 0}" 
            data-date="${contact.date}"
            data-product-id="${contact.product.id}"
            data-product-name="${contact.product.name}"
            data-product-unit-price="${contact.product.unit_price}"
            >
            <div>
                ${contact.no_ref}
            </div>
            <div>
                ${contact.contact.name}
            </div>
            
        </button>
        `
    });

    document.querySelector('#credit-application-list').innerHTML = list ? list : `
    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" disabled>
        No Contact
    </button>
    `;
}

async function showCreditApplicationDropdown(value){
    document.querySelector('#credit-application-list').classList.remove('d-none');
    document.querySelector('#nik-input-dropdown').classList.remove('position-relative');
    await showCreditApplication(value.value);
}

function filterButton(){
    document.querySelector('#select-filter').value = filterBy;
}

function changeFilter(value){
    filterBy = value.value;

    filterBy == 'custom' ? dateRange.classList.remove('d-none') : dateRange.classList.add('d-none');

    if (filterBy == 'custom') {
        dateRange.classList.remove('d-none');
        startFilter.value = dateNow();
        endFilter.value = dateNow();
    } else {
        dateRange.classList.add('d-none');
    }
}

function changeStatus(value){
    statusQuery = `&status=${value.value}`;

    if (value.value == 'all') {
        statusQuery = '';
    }
}

function deleteCreditApplication(){
    formData.credit_application = {
        id: null,
        no_ref: '',
    }

    setDefaultComponentValue();
    validateInputData();
}

function setQuery(){
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = '';
    thisYear = '';

    switch (filterBy) {
        case 'today':
            date_from = dateNow();
            date_to = dateNow();
            break;
        case 'this week':
            thisWeek = true;
            break;
        case 'this month':
            thisMonth = true;
            break;
        case 'this year':
            thisYear = true;
            break;
        default:
            date_from = startFilter.value;
            date_to = endFilter.value;
            break;
    }
}

async function submitFilter(){
    setQuery();
    await showData();

}

function sendWa(){
    //set link send to wa
    let waLink = 'https://web.whatsapp.com/send';

    //validation phone number
    let phoneNumber = formData.contact.phone;

    if (phoneNumber[0] == '0') {
        let temp = phoneNumber.substring(1, phoneNumber.length);
        phoneNumber = '62' + temp;
    }

    let message = `*FAKTUR PENJUALAN KREDIT*%0A-------------------------------------------------------%0A*Kepada:*%0A*Nama:* ${formData.contact.name.toUpperCase()}%0A*NIK:* ${formData.contact.nik.toUpperCase()}%0A*No HP:* ${formData.contact.phone.toUpperCase()}%0A-------------------------------------------------------%0A*No Ref:* ${formData.no_ref}%0A*Produk:* ${formData.product.name}%0A*Nilai Penjualan:* Rp. ${formatRupiah(formData.value.toString())}%0A*Tenor:* ${formatRupiah(formData.tenor.toString())}%0A*Angsuran Per Bulan:* Rp. ${formatRupiah(formData.installment.toString())}%0A*DP:* Rp. ${formatRupiah(formData.downpayment.toString())}%0A*Tanggal:* ${dateReadable(formData.date)}%0A*Jatuh Tempo:* ${dateReadable(formData.due_date)}%0A%0A %0AUnit Usaha ${businessName.toUpperCase()}%0ABUMDES ${identity.toUpperCase()}%0A
    `

    //value
    let content = `${waLink}?phone=${phoneNumber}&text=${message}`;

    
    /* Whatsapp Window Open */
    window.open(content, '_blank');
}

window.addEventListener('load', async function(){
    setDefault();
    setDefaultComponentValue();
    await showData();
})