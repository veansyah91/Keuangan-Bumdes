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

let formData = {
    no_ref : '',
    name: '',
    credit_account : {
        id: null,
        name: ''
    },
    date: '',
    value: 0,
    salvage: 0,
    useful_life: 0,
    is_active: true
}

//component modal input
const createModalLabel = document.querySelector('#createModalLabel');
const noRefInput = document.querySelector('#no-ref-input');
const nameInput = document.querySelector('#name-input');
const debitAccountInput = document.querySelector('#debit-account-input');
const dateInput = document.querySelector('#date-input');
const valueInput = document.querySelector('#value-input');
const salvageInput = document.querySelector('#salvage-input');
const usefulLifeInput = document.querySelector('#useful-life-input');

const btnSubmit = document.querySelector('#btn-submit');
const btnSubmitLabel = document.querySelector('#btn-submit-label');


let search = '';
let page = 1;
let isUpdate = false;
let deleteId = null;
let updateId = null;

async function searchForm(event){
    event.preventDefault();

    search = searchInput.value;

    await showFixedAsset();
}

function setDefault(){
    search = '';
    page = 1;
    deleteId = null;
    updateId = null;
    isUpdate = false;

    formData = {
        no_ref : '',
        name: '',
        credit_account : {
            id: null,
            name: ''
        },
        date: '',
        value: 0,
        salvage: 0,
        useful_life: 0,
    }
    validateInputData();
}

function validateInputData(){
    let is_validated = false;

    if (formData.no_ref && formData.name  && formData.credit_account.name && formData.date && formData.value > 0 && formData.useful_life > 0)    {
        is_validated = true
    }

    if (is_validated) {
        btnSubmit.classList.remove('d-none');
    } else {
        btnSubmit.classList.add('d-none');
    }
}

const loadingStatus = () => {
    return `
        <button type="button" colspan="5" class="text-center">
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        </button>
    `;
}

function showAccountList (accounts){
    let list = ''

    accounts.map(account => {
        list += `
            <button type="button" class="list-group-item list-group-item-action" onclick="selectSubClassification(this)" data-name="${account.name}" data-id="${account.id}">
            ${account.name}
        </button>
        `
    });

    document.querySelector('#debit-account-list').innerHTML = list;
}

function changeAccountValue(value){
    value.value = formData.credit_account.name;
}

function selectSubClassification(value){
    formData.credit_account.id = parseInt(value.dataset.id);
    formData.credit_account.name = value.dataset.name;

    debitAccountInput.value = formData.credit_account.name;
    validateInputData();
}

async function changeAccountDropdown(value){
    document.querySelector('#debit-account-list').innerHTML = loadingStatus();

    setTimeout(async () => {
        let res = await getAccounts(value.value);
    
        showAccountList(res.data);
    }, 150);
    
}

async function showAccountDropdown(){
    document.querySelector('#debit-account-list').classList.remove('d-none');
    document.querySelector('#debit-account-list').innerHTML = loadingStatus();
    let res = await getAccounts(formData.credit_account.name);
    
    showAccountList(res.data);
}

function setDefaultComponentValue(){
    nameInput.value = formData.name;
    noRefInput.value = formData.no_ref;
    debitAccountInput.value = formData.credit_account.name;
    dateInput.value = formData.date;
    valueInput.value = formatRupiah(formData.value.toString());
    salvageInput.value = formatRupiah(formData.salvage.toString());
    usefulLifeInput.value = formatRupiah(formData.useful_life.toString());

    btnSubmit.classList.add('d-none');

    btnSubmitLabel.innerHTML = `Simpan`;
    btnSubmit.removeAttribute('disabled');
}

function dateInputChange(value){
    formData.date = value.value;    
    validateInputData();
}

function nameInputChange(value){
    formData.name = value.value;    
    validateInputData();
}

function valueInputChange(value){
    formData.value = parseInt(toPrice(value.value));    
    validateInputData();
}

function salvageInputChange(value){
    formData.salvage = parseInt(toPrice(value.value));    
    validateInputData();
}

function useFulInputChange(value){
    formData.useful_life = parseInt(toPrice(value.value));    
    validateInputData();
}

const submitFixedAsset = async (event) => {
    event.preventDefault();

    btnSubmit.setAttribute('disabled','disabled');
    btnSubmitLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>`;
    if (isUpdate) {
        let result = await putFixedAsset(formData, updateId);
    
        let message = `${result.name} Berhasil Diubah`;
        myToast(message, 'success');
        await showFixedAsset();
        setDefaultComponentValue();
        validateInputData();
    } else {
        try {
            let result = await postFixedAsset(formData);

            let message = `${result.name} Berhasil Ditambahkan`;
                
            myToast(message, 'success');

            setDefault();
            setDefaultComponentValue();
            formData.no_ref = await getNewNoRef();
            noRefInput.value = formData.no_ref;

            formData.date = dateNow();
            dateInput.value = formData.date;
            await showFixedAsset();
        } catch (error) {
            console.log(error);
        }
        
    }                
}

async function prevButton(){
    page--;
    await showFixedAsset();
}

async function nextButton(){
    page++;
    await showFixedAsset();
}

async function showFixedAsset(){
    try {
        let url = `/api/fixed-assets?search=${search}&page=${page}`;

        let response = await getFixedAsset(url);

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
            list += `
            <div class="d-flex d-md-none justify-content-between border-top border-bottom py-2">
                <div style="width:10%" class="my-auto">
                    <div class="btn-group dropstart">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${index}">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleFixedAsset(${res.id})" >
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
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteFixedAsset(${res.id})" >
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
                        ${res.name}
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
                <div style="width:1%" class="px-2 my-auto">
                    <div class="dropdown">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${i}">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleFixedAsset(${res.id})" >
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
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteFixedAsset(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-danger">
                                        <div class="col-2"><i class="bi bi-trash"></i></div>
                                        <div class="col-3">Hapus</div>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div style="width:14%" class="px-2 my-auto">${dateReadable(res.date)}</div>
                <div style="width:20%" class="px-2 my-auto">${res.name}</div>
                <div style="width:12%" class="px-2 my-auto text-end">${formatRupiah(res.value.toString())}</div>
                <div style="width:12%" class="px-2 my-auto text-end">${formatRupiah(res.useful_life.toString())}</div>
                <div style="width:7%" class="font-bold px-2 my-auto text-center ${res.is_active ? 'text-success' : 'text-danger'}">${res.is_active ? 'Aktif' : 'Tidak Aktif'}</div>
            </div>`
            });

            listData.innerHTML = list;
        }

        
    } catch (error) {
         console.log(error);
    }
    
}

const addData = async () => {
    document.querySelector('#is-active').classList.add('d-none');
    noRefInput.removeAttribute('disabled');
    formData = {
        no_ref : '',
        name: '',
        credit_account : {
            id: null,
            name: ''
        },
        date: '',
        value: 0,
        salvage: 0,
        useful_life: 0,
    }
    isUpdate = false;

    setDefaultComponentValue();

    createModalLabel.innerHTML = "Tambah Data";

    formData.no_ref = await getNewNoRef();
    noRefInput.value = formData.no_ref;

    formData.date = dateNow();
    dateInput.value = formData.date;
}

const changeIsActiveInput = (value) => {
    formData.is_active = !formData.is_active;
    document.querySelector('#is-active-input-label').innerHTML = formData.is_active ? 'Aktif' : 'Tidak Aktif';
}

const editData = async (id) => {
    document.querySelector('#is-active').classList.remove('d-none');
    createModalLabel.innerHTML = "Ubah Data";
    noRefInput.setAttribute('disabled', 'disabled');
    let url = `/api/fixed-asset/${id}/edit`;
    let res = await getFixedAsset(url);

    isUpdate = true;
    updateId = id;
    formData = {
        no_ref : res.no_ref,
        name: res.name,
        credit_account : {
            id: res.credit_account.id,
            name: res.credit_account.name,
        },
        date: res.date,
        value: parseInt(res.value),
        salvage: parseInt(res.salvage),
        useful_life: parseInt(res.useful_life),
        is_active: res.is_active < 1? false : true,
    }

    res.is_active 
    ? document.querySelector('#is-active-input').setAttribute('checked', 'checked')
    : document.querySelector('#is-active-input').removeAttribute('checked')

    document.querySelector('#is-active-input-label').innerHTML = formData.is_active ? 'Aktif' : 'Tidak Aktif';

    setDefaultComponentValue();
    validateInputData();

}

function deleteFixedAsset(id){
    deleteId = id;
}

async function submitDeleteFixedAsset(){  
    try {
        let response = await destroyFixedAsset(deleteId);

        let message = `${response.name} Berhasil Dihapus`;

        
        myToast(message, 'success');

        setDefault();
        await showFixedAsset();

    } catch (error) {
        myToast(error.response.data.errors.message, 'danger');
    }
}

const submitDepreciate = async (event) => {
    event.preventDefault();
    let depreciateButtonSubmit = document.querySelector('#depreciate-btn-submit');
    let depreciateButtonSubmitLabel = document.querySelector('#depreciate-btn-submit-label');

    depreciateButtonSubmit.setAttribute('disabled','disabled');
    depreciateButtonSubmitLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>`;
    try {
        await fixedAssetDepreciation();
        let message = `Penyusutan Semua Harta Tetap Telah Dilakukan Hingga Akhir Bulan Ini`;

        myToast(message, 'success');

    } catch (error) {
        console.log(error);
        myToast(error, 'danger');
    }

    depreciateButtonSubmit.removeAttribute('disabled');
    depreciateButtonSubmitLabel.innerHTML = `Lakukan Penyusutan`;
    
    
}

async function showSingleFixedAsset(id){
    try {
        let url = `/api/fixed-asset/${id}`;
        let res = await getFixedAsset(url);

        console.log(res);
        document.querySelector('#name-detail').innerHTML = res.name;
        document.querySelector('#no-ref-detail').innerHTML = res.no_ref;
        document.querySelector('#date-detail').innerHTML = dateReadable(res.date);
        document.querySelector('#value-detail').innerHTML = formatRupiah(res.value.toString());
        document.querySelector('#salvage-detail').innerHTML = formatRupiah(res.salvage.toString());
        document.querySelector('#useful-life-detail').innerHTML = res.useful_life + ' bulan';

        document.querySelector('#author-detail').innerHTML = res.author;

        accountCodeDetails = document.getElementsByClassName('account-code-detail');
        accountNameDetails = document.getElementsByClassName('account-name-detail');
        debitDetails = document.getElementsByClassName('debit-detail');
        creditDetails = document.getElementsByClassName('credit-detail');

        accountCodeDetails[0].innerHTML = res.debit_account.code;
        accountNameDetails[0].innerHTML = res.debit_account.name;
        debitDetails[0].innerHTML = `Rp. ${formatRupiah(res.value.toString(0))}`;
        creditDetails[0].innerHTML = 'Rp. ' + 0;

        accountCodeDetails[1].innerHTML = res.credit_account.code;
        accountNameDetails[1].innerHTML = res.credit_account.name;
        debitDetails[1].innerHTML = 'Rp. ' + 0;
        creditDetails[1].innerHTML = 'Rp. ' + formatRupiah(res.value.toString(0));

        document.querySelector('#total-debit-detail').innerHTML = 'Rp. ' + formatRupiah(res.value.toString(0));
        document.querySelector('#total-credit-detail').innerHTML = 'Rp. ' + formatRupiah(res.value.toString(0));

        let list = '';
        res.depreciates.map((depreciate, index) => {
            list += `
                <tr>
                    <td class="text-center">${dateReadable(depreciate.date)}</td>
                    <td class="text-end">${formatRupiah(depreciate.credit.toString())}</td>
                    <td class="text-end">${formatRupiah((res.value - (depreciate.credit * (index+1))).toString())}</td>
                </tr>
            `
        })

        document.querySelector('#depreciation-list').innerHTML = list;

    } catch (error) {
        console.log(error);
    }
}



window.addEventListener('load', async function(){
    setDefault();
    setDefaultComponentValue();
    await showFixedAsset();
})