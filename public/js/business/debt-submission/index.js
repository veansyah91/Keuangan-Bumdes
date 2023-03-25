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
    contact : {
        id: null,
        nik: '',
        name: '',
    },
    value : 0,
    due_date: '',
    tenor: 1
}

//component modal input
const createModalLabel = document.querySelector('#createModalLabel');
const dateInput = document.querySelector('#date-input');
const contactInput = document.querySelector('#contact-input');
const nameInput = document.querySelector('#name-input');
const contactList = document.querySelector('#contact-list');
const noRefInput = document.querySelector('#no-ref-input');
const valueInput = document.querySelector('#value-input');
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
    const nextMonth = new Date(addMonths(date, 1));
    var day = nextMonth.getDate();
    var month = nextMonth.getMonth() + 1;
    var year = nextMonth.getFullYear();

    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    formData = {
        no_ref : '',
        date: dateNow(),
        contact : {
            id: null,
            nik: '',
            name: '',
        },
        value : 0,
        status: 0,
        due_date: year + "-" + month + "-" + day,
        tenor: 1
    }
    
    validateInputData();
}

function validateInputData(){
    let is_validated = false;

    if (formData.no_ref && formData.contact.name && formData.value > 0 && formData.tenor > 0)    {
        is_validated = true
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
    document.querySelector('#tenor-input').value = formData.tenor;
    document.querySelector('#installment-input').value = formatRupiah(Math.round(formData.value / formData.tenor).toString());

    btnSubmit.classList.add('d-none');

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

const submitData = async (event) => {
    event.preventDefault();

    btnSubmit.setAttribute('disabled','disabled');
    btnSubmitLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>`;

    if (isUpdate) {
        let result = await putData(formData, updateId);
    
        let message = `Pengajuan Pinjaman ${result.no_ref} Berhasil Diubah`;

        myToast(message, 'success');

        
        validateInputData();
    } else {
        let result = await postData(formData);

        let message = `Pengajuan Pinjaman ${result.no_ref} Berhasil Ditambahkan`;
            
        myToast(message, 'success');

        setDefault();
    }            
    await showData();  
    let url = `/api/${business}/no-ref-debt-submission-recomendation?date=${formData.date}`;

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
        let url = `/api/${business}/debt-submission?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}${statusQuery}`;

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
    
            switch (res.status) {
                case 'approved':
                    status = 'Disetujui';
                    statusColor = 'text-primary';
                    break;
                case 'rejected':
                    status = 'Ditolak';
                    statusColor = 'text-danger';
                    break;
                default:
                    status = 'Menunggu';
                    statusColor = 'text-success';
                    break;
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
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#createModal" onclick="editData(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#updateStatusModal" onclick="editData(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-secondary">
                                        <div class="col-2"><i class="bi bi-ui-radios"></i></div>
                                        <div class="col-3">Ubah Status</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteData(${res.id})" >
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
                        ${res.contact_name}
                    </div>
                    <div class="">
                        <small>Kode: ${res.no_ref}  </small>
                    </div>
                </div>
                <div style="width:40%" class="my-auto text-end">
                    <div class="font-bold">
                        ${formatRupiah(res.value.toString())}
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
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                           <li>
                                <button class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#createModal" onclick="editData(${res.id})">
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#updateStatusModal" onclick="editData(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-secondary">
                                        <div class="col-2"><i class="bi bi-ui-radios"></i></div>
                                        <div class="col-3">Ubah Status</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteData(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-danger">
                                        <div class="col-2"><i class="bi bi-trash"></i></div>
                                        <div class="col-3">Hapus</div>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div style="width:10%" class="px-2 my-auto">${dateReadable(res.date)}</div>
                <div style="width:15%" class="px-2 my-auto">${res.no_ref}</div>
                <div style="width:20%" class="px-2 my-auto">${res.contact.phone}</div>
                <div style="width:15%" class="px-2 my-auto">${res.contact_name}</div>
                <div style="width:20%" class="px-2 my-auto text-end">${formatRupiah(res.value.toString())}</div>
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
    let url = `/api/${business}/no-ref-debt-submission-recomendation?date=${value.value}`;

    let noRef = await getNewNoRef(url);

    formData.date = value.value;
    formData.no_ref = noRef;

    setDefaultComponentValue();
    validateInputData();

}

const addData = async () => {
    isUpdate = false;
    setDefault();
    
    let url = `/api/${business}/no-ref-debt-submission-recomendation?date=${formData.date}`;

    let noRef = await getNewNoRef(url);

    formData.no_ref = noRef;

    setDefaultComponentValue();

    createModalLabel.innerHTML = "Tambah Data";
}

const editData = async (id) => {
    createModalLabel.innerHTML = "Ubah Data";
    let url = `/api/${business}/debt-submission/${id}`;
    let res = await getData(url);
    isUpdate = true;
    updateId = id;

    formData = {
        no_ref : res.no_ref,
        date: res.date,
        contact : {
            id: res.contact.id,
            nik: res.contact.detail.nik,
            name: res.contact.name
        },
        value : res.value,
        status: res.status,
        due_date: res.due_date,
        tenor: res.tenor
    }

    //set status
    const pendingStatus = document.querySelector('#pending');
    const rejectedStatus = document.querySelector('#rejected');
    const approvedStatus = document.querySelector('#approved');

    //clear status check
    pendingStatus.removeAttribute('checked');
    rejectedStatus.removeAttribute('checked');
    approvedStatus.removeAttribute('checked');

    switch (formData.status) {
        case 'rejected':
            rejectedStatus.setAttribute('checked', 'checked');
            break;
        case 'approved':
            rejectedStatus.setAttribute('checked', 'checked');
            break;
        default:
            pendingStatus.setAttribute('checked', 'checked');
            break;
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
        let url = `/api/${business}/debt-submission/${id}`;
        let res = await getData(url);

        let status;
        let statusColor;

        switch (res.status) {
            case 'approved':
                status = 'Disetujui';
                statusColor = 'text-primary';
                break;
            case 'rejected':
                status = 'Ditolak';
                statusColor = 'text-danger';
                break;
            default:
                status = 'Menunggu';
                statusColor = 'text-success';
                break;
        }
        
        document.querySelector('#status-detail').innerHTML = `
            Status: <span class="fw-bold ${statusColor}">${status}</span>
        `;

        document.querySelector('#name-detail').innerHTML = `${res.contact_name}`;

        document.querySelector('#date-detail').innerHTML = `: ${dateReadable(res.date)}`;

        document.querySelector('#author-detail').innerHTML = `: ${res.author}`;

        document.querySelector('#value-detail').innerHTML = `: ${formatRupiah(res.value.toString())}`;

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
    await showListData(value.value);
}

function changeContact(value)
{
    value.value = formData.contact.saving_ref;
}

async function changeContactDropdown(value){
    setTimeout(async () => {
        await showListData(value.value)
    }, 200);
}

function updateDueDate()
{
    let refDate = new Date(formData.date);

    const nextMonth = new Date(addMonths(refDate, formData.tenor));
    var day = nextMonth.getDate();
    var month = nextMonth.getMonth() + 1;
    var year = nextMonth.getFullYear();

    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    formData.due_date = year + "-" + month + "-" + day;

    termInput.value = formData.due_date;
}

async function changeTotal(value)
{
    formData.value = parseInt(toPrice(value.value));
    document.querySelector('#installment-input').value = formatRupiah((formData.value / formData.tenor).toString());

    updateDueDate();

    validateInputData()
}

async function changeTenor(value)
{
    formData.tenor = parseInt(toPrice(value.value));
    document.querySelector('#installment-input').value = formatRupiah(Math.round(formData.value / formData.tenor).toString());
    updateDueDate();

    validateInputData()
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
    validateInputData()
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

window.addEventListener('load', async function(){
    setDefault();
    setDefaultComponentValue();
    await showData();
})