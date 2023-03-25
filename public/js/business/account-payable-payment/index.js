const searchInput = document.querySelector('#search-input');

const selectFilter = document.querySelector('#select-filter');
const dateRange = document.querySelector('#date-range');
const startFilter = document.querySelector('#start-filter');
const endFilter = document.querySelector('#end-filter');

const countData = document.querySelector('#count-data');
const prevBtn = document.querySelector('#prev-page');
const nextBtn = document.querySelector('#next-page');

const listData = document.querySelector('#list-data');

const dateDetail = document.querySelector('#date-detail');
const noRefDetail = document.querySelector('#no-ref-detail');
const contactNameDetail = document.querySelector('#contact-name-detail');
const contactAddressDetail = document.querySelector('#contact-address-detail');
const descriptionDetail = document.querySelector('#description-detail');
const creditAccountDetail = document.querySelector('#credit-account-detail');
const authorDetail = document.querySelector('#author-detail');
const authorDetailLabel = document.querySelector('#author-detail-label');
const contentDetail = document.querySelector('#content-detail');
const totalDetail = document.querySelector('#total-detail');
const qtyDetail = document.querySelector('#qty-detail');

let search = '';
let page = 1;
let deleteId = null;
let filterBy = '';
let date_from, date_to, thisWeek, thisMonth, thisYear;

let dataId = null;

//input
    let isUpdate = false;
    let formData = {
        date: '',
        description: '',
        no_ref: '',
        contact: {
            id: null,
            name: ''
        },
        invoice: {
            id: null,
            no_ref: ''
        },
        value: 0,
        credit: {
            id: null,
            name: ''
        }
    }
//

//data account payable per invoice
    let invoices = []
//

function setDefaultValue()
{
    formData = {
        date: dateNow(),
        description: '',
        no_ref: '',
        contact: {
            id: null,
            name: ''
        },
        invoice: {
            id: null,
            no_ref: ''
        },
        value: 0,
        credit: {
            id: null,
            name: ''
        }
    }
}

async function searchForm(event){
    event.preventDefault();
    search = searchInput.value;
    await showAccountPayablePayment();
}

function setDefault(){
    search = '';
    page = 1;
    deleteId = null;

    filterBy = 'this month';
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = 1;
    thisYear = '';
}

function setDefaultComponentValue(){
    dateRange.classList.add('d-none');
    document.querySelector('#date-input').value = formData.date;
    document.querySelector('#no-ref').value = formData.no_ref;
    document.querySelector('#contact').value = formData.contact.name;
    document.querySelector('#description').value = formData.description;
    document.querySelector('#invoice').value = formData.invoice.no_ref;
    document.querySelector('#value').value = formatRupiah(formData.value.toString());
    document.querySelector('#account').value = formData.credit.name;

    document.querySelector('#btn-submit').removeAttribute('disabled');
    document.querySelector('#btn-submit-label').innerHTML = `Simpan`;
}

async function prevButton(){
    page--;
    await showAccountPayablePayment();
}

async function nextButton(){
    page++;
    await showAccountPayablePayment();
}

function validationInput()
{
    let isValidated = false;

    if (formData.no_ref && formData.value > 0 && formData.contact.name && formData.credit.name && formData.invoice.no_ref && formData.date && formData.description ) {
        isValidated = true
    }

    let btnSubmitInvoice = document.querySelector('#btn-submit');

    isValidated 
    ? btnSubmitInvoice.classList.remove('d-none')
    : btnSubmitInvoice.classList.add('d-none')
}

function changeValue(value){
    formData.value = parseInt(toPrice(value.value));
}

function changeDateInput(value){
    formData.date = value.value;
}

function selectContact(value)
{
    formData.contact = {
        id : parseInt(value.dataset.id),
        name : value.dataset.name
    }
    formData.description = `Pembayaran Utang Kepada ${formData.contact.name}`;
    document.querySelector('#contact').value = formData.contact.name;
    document.querySelector('#description').value = formData.description;
    validationInput();
}

async function showSearchContact(search){
    let contactList = await getContacts(search);

    document.querySelector('#invoice-dropdown-input').classList.remove('position-relative');
    document.querySelector('#account-dropdown-input').classList.remove('position-relative');
    
    let list = '';
    contactList.data.map(contact => {
        let sisa = parseInt(contact.account_payables_sum_credit) - parseInt(contact.account_payables_sum_debit);
        list += sisa > 0 ? `
        <button type="button" class="list-group-item list-group-item-action" onclick="selectContact(this)" data-name="${contact.name}" data-id="${contact.id}">
            <div class="row justify-content-between">
                <div class="col">
                    <div>${contact.name}</div>
                </div>
                <div class="col">
                    <div class="text-success text-end"><small> Sisa: Rp. ${formatRupiah(sisa.toString())}</small></div>
                </div>
            </div>
        </button>
        ` : ''
    })

    document.querySelector('#contact-list').innerHTML = list ? list : `
    <button type="button" class="list-group-item list-group-item-action disabled" disabled>
            <div class="row justify-content-between">
                <div class="col">
                    <div>Tidak Ada Data</div>
                </div>
            </div>
        </button>
    ` 
}

function changeContactDropdown(value){
    setTimeout(() => {
        showSearchContact(value.value);
    }, 200);
}

function changeContact(value)
{
    value.value = formData.contact.name;
}

async function showContactDropdown(value){
    document.querySelector('#contact-list').classList.remove('d-none');
    
    showSearchContact(value.value);
}

function selectInvoice(value){
    formData.invoice = {
        id: parseInt(value.dataset.id),
        no_ref: value.dataset.noRef
    }
    formData.value = parseInt(value.dataset.value);

    document.querySelector('#invoice').value = formData.invoice.no_ref;
    document.querySelector('#value').value = formatRupiah(formData.value.toString());
    validationInput();
}

async function showSearchInvoice(search){
    let invoiceList = await getInvoices(formData.contact.id, search);

    document.querySelector('#invoice-dropdown-input').classList.add('position-relative');
    document.querySelector('#account-dropdown-input').classList.remove('position-relative');
    
    let list = '';
    invoiceList.data.map(invoice => {
        let sisa = parseInt(invoice.account_payables_sum_credit) - parseInt(invoice.account_payables_sum_debit);
        list += sisa > 0 ? `
        <button type="button" class="list-group-item list-group-item-action" onclick="selectInvoice(this)" data-no-ref="${invoice.no_ref}" data-id="${invoice.id}" data-value="${sisa}">
            <div class="row justify-content-between">
                <div class="col">
                    <div>${invoice.no_ref}</div>
                </div>
                <div class="col">
                    <div class="text-success text-end"><small> Sisa: Rp. ${formatRupiah(sisa.toString())}</small></div>
                </div>
            </div>
        </button>
        ` : ''
    })

    document.querySelector('#invoice-list').innerHTML = list ? list : `
    <button type="button" class="list-group-item list-group-item-action disabled" disabled>
            <div class="row justify-content-between">
                <div class="col">
                    <div>Tidak Ada Data</div>
                </div>
            </div>
        </button>
    ` 
}

function changeInvoiceDropdown(value){
    setTimeout(() => {
        showSearchInvoice(value.value);
    }, 200);
}

function changeInvoice(value)
{
    value.value = formData.invoice.no_ref;
}

async function showPurchaseGoodsDropdown (value){
    document.querySelector('#invoice-list').classList.remove('d-none');
    
    showSearchInvoice(value.value);
}

function selectAccount(value){
    formData.credit = {
        id: parseInt(value.dataset.id),
        name: value.dataset.name
    }

    document.querySelector('#account').value = formData.credit.name;
    validationInput();
}

async function showSearchAccount(search){
    let accountList = await getAccounts(search);

    let list = '';
    accountList.data.map(account => {
        list += account.name == 'Utang Dagang' ? '' : `
        <button type="button" class="list-group-item list-group-item-action" onclick="selectAccount(this)" data-name="${account.name}" data-id="${account.id}">
            <div class="row justify-content-between">
                <div class="col">
                    <div>${account.name}</div>
                </div>
            </div>
        </button>
        `
    })

    document.querySelector('#account-list').innerHTML = list ? list : `
    <button type="button" class="list-group-item list-group-item-action disabled" disabled>
            <div class="row justify-content-between">
                <div class="col">
                    <div>Tidak Ada Data</div>
                </div>
            </div>
        </button>
    ` 
}

function changeAccountDropdown(value){
    setTimeout(() => {
        showSearchAccount(value.value);
    }, 200);
}

function changeAccount(value)
{
    value.value = formData.credit.name;
}

async function showAccountDropdown(value){
    document.querySelector('#account-list').classList.remove('d-none');
    document.querySelector('#account-dropdown-input').classList.add('position-relative');
    
    showSearchAccount(value.value);
}

async function setDefaultNoRef(){
    formData.no_ref = await newRef();
    document.querySelector('#no-ref').value = formData.no_ref;
    validationInput();
}

async function addData(){
    isUpdate = false;
    setDefaultValue();
    setDefaultComponentValue();
    setDefaultNoRef();
    validationInput();
    document.querySelector('#createModalLabel').innerHTML = 'Tambah Pembayaran Utang';
    document.querySelector('#date-input').value = formData.date;
}

async function editData(id)
{
    isUpdate = true;
    dataId = id;
    document.querySelector('#createModalLabel').innerHTML = 'Ubah Pembayaran Utang';

    try {
        let res = await getSingleAccountPayablePayment(id);

        formData = {
            date: res.date,
            description: res.description,
            no_ref: res.no_ref,
            contact: {
                id: res.contact.id,
                name: res.contact.name
            },
            invoice: {
                id: res.purchaseGoods.id,
                no_ref: res.purchaseGoods.no_ref
            },
            value: res.value,
            credit: {
                id: res.credit.id,
                name: res.credit.name
            }
        }

        validationInput();

        setDefaultComponentValue();
    } catch (error) {
        console.log(error);
    }
}

async function submitAccountPayable(event)
{
    event.preventDefault();
    document.querySelector('#btn-submit').setAttribute('disabled','disabled');
    document.querySelector('#btn-submit-label').innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>`;

    try {
        let res = ''
        
        let message = '';
        if (isUpdate) {
            res = await updateAccountPayablePayment(formData, dataId);

            message = `Pembayaran Utang Kepada ${formData.contact.name} Berhasil Diubah`;
        } else {
            res = await postAccountPayablePayment(formData);

            message = `Pembayaran Utang Kepada ${formData.contact.name} Berhasil Ditambahkan`;
            setDefaultValue();
        
        }
        myToast(message, 'success');

        setDefaultComponentValue();
        await showAccountPayablePayment();
        validationInput();
    } catch (error) {
        console.log(error);
    }
}

async function showAccountPayablePayment(){
    try {
        let response = await getAccountPayablePayment(search,page, date_from, date_to, thisWeek, thisMonth, thisYear);
        
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

        let total = 0;

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
            total += res.value;
            list += `
            <div class="d-flex d-md-none justify-content-between border-top border-bottom py-2">
                <div style="width:5%" class="my-auto">
                    <div class="btn-group dropstart">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${index}">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleAccountPayablePayment(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createModal" onclick="editData(${res.id})">
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteAccountPayablePayment(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-danger">
                                        <div class="col-2"><i class="bi bi-trash"></i></div>
                                        <div class="col-3">Hapus</div>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div style="width:50%">
                    <div class="font-bold">
                        ${res.contact.name}
                    </div>
                    <div class="">
                        <small>Kode: ${res.no_ref}  </small>
                    </div>
                </div>
                <div style="width:40%" class="my-auto text-end">
                    Rp. ${formatRupiah(res.value.toString())}
                </div>
                
            </div>

            <div class="d-md-flex d-none justify-content-between border-top border-bottom py-2 px-1 content-data">
                <div style="width:5%" class="px-2 my-auto">
                    <div class="dropdown">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${index}">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleAccountPayablePayment(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                            ${res.source ? '' : `<li>
                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createModal" onclick="editData(${res.id})">
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteAccountPayablePayment(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-danger">
                                        <div class="col-2"><i class="bi bi-trash"></i></div>
                                        <div class="col-3">Hapus</div>
                                    </div>
                                </button>
                            </li>`}
                        </ul>
                    </div>
                </div>
                <div style="width:20%" class="px-2 my-auto">${dateReadable(res.date)}</div>
                <div style="width:20%" class="px-2 my-auto">${res.no_ref}</div>
                <div style="width:20%" class="px-2 my-auto">${res.contact.name}</div>
                <div style="width:20%" class="px-2 my-auto text-end">${formatRupiah(res.value.toString())}</div>
                
            </div>`
            });

            listData.innerHTML = list;
        }

        
    } catch (error) {
        console.log(error);
    }
    
}

function deleteAccountPayablePayment(id){
    deleteId = id;
}

async function submitDeleteAccountPayablePayment(){
    try {
        let response = await destroyAccountPayablePayment(deleteId);
        
        let message = `Data Berhasil Dihapus`;
        
        myToast(message, 'success');

        setDefault();
        await showAccountPayablePayment();

    } catch (error) {
        console.log(error);
    }
}

async function showSingleAccountPayablePayment(id){
    try {
        let res = await getSingleAccountPayablePayment(id);

        dateDetail.innerHTML = `: ${dateReadable(res.date)}`;
        document.querySelector('#author-detail').innerHTML = `: ${res.author}`;
        noRefDetail.innerHTML = `: ${res.no_ref}`;

        authorDetail.innerHTML = `: ${res.author}`;
        contactNameDetail.innerHTML = `: ${res.contact.name}`;
        contactAddressDetail.innerHTML = `: ${res.contact.address ? res.contact.address :  ''}`;
        
        contentDetail.innerHTML = `
        <div class="row mt-2 text-gray">
            <div class="col-4 text-start p-2 border border-white">
                ${res.purchaseGoods.no_ref}
            </div>
            <div class="col-4 text-start p-2 border border-white">
                ${dateReadable(res.purchaseGoods.date)}
            </div>
            <div class="col-4 text-end p-2 border border-white">
                ${formatRupiah(res.value.toString())}
            </div>
        </div>
        `;
        totalDetail.innerHTML = ` ${formatRupiah(res.value.toString())}`;


    } catch (error) {
        console.log(error);
    }
}

function goToPrintAccountPayablePayments(){
    window.open(`/${business}/account-payable-payment/print?search=${search}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`)
}

function filterButton(){
    selectFilter.value = filterBy;
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
    await showAccountPayablePayment();

}


window.addEventListener('load', async function(){
    setDefault();
    setDefaultValue();
    setDefaultComponentValue();
    await showAccountPayablePayment();
})