const successAlert = document.querySelector('#success-alert');
const successAlertMessage = document.querySelector('#success-alert-message');

const searchContactInput = document.querySelector('#search-contact-dropdown');
const contactList = document.querySelector('#contact-list');
const contactInput = document.querySelector('#contact-input');

const toAccountList = document.querySelector('#to-account-list');
const toAccountInput = document.querySelector('#to-account-input');

const dateInput = document.querySelector('#date-input');
const descriptionInput = document.querySelector('#description-input');
const noRefInput = document.querySelector('#no-ref-input');
const detailInput = document.querySelector('#detail-input');

const listInputContent = document.querySelector('#list-input-content');
const btnSubmitRevenue = document.querySelector('#btn-submit-revenue');

const totalLabel = document.querySelector('#total-label');
const differencetotalCreditComponent = document.querySelector('#difference-total-credit');

const submitButtonLabel = document.querySelector('#submit-button-label');
const btnSubmit = document.querySelector('#btn-submit');

let totalInputList = 1;
let differencetotalCredit = 0;
let totalInput = 0;

let formData = {
    contact:'',
    to_account: {
        id:0,
        name: '',
    },
    date: '',
    desc: '',
    no_ref: '',
    detail: '',
    value: 0,
    listInput:[{},{}]
}

const setDefaultValue = async () => {
    totalInputList = 1;
    differencetotalCredit = 0;
    totalInput = 0;

    let res = await getNewNoRefContact();

    formData = {
        contact:'',
        to_account: {
            id:0,
            name: '',
        },
        date: dateNow(),
        desc: '',
        no_ref:  res,
        detail: '',
        value: 0,
        listInput:[
            {
                accountId:null,
                accountName:'',
                total:0,
            },
        ]
    }

    setValueInputComponent();
}

const validationInput = () => {

    let isValidated = false;

    if (formData.desc && formData.no_ref && formData.contact && formData.to_account.name) {
        isValidated = true
        
        formData.listInput.map(list => {
            if (!list.accountName || list.total == 0) {
                isValidated &= false;
            }
        })
    }
    isValidated 
    ? btnSubmitRevenue.classList.remove('d-none')
    : btnSubmitRevenue.classList.add('d-none')
    
}

const setValueInputComponent = () => {
    contactInput.value = formData.contact;
    dateInput.value = formData.date;
    descriptionInput.value = formData.desc;
    noRefInput.value = formData.no_ref;
    detailInput.value = formData.detail;

    totalLabel.innerHTML = 'Rp.0';

    btnSubmitRevenue.classList.add('d-none');

    submitButtonLabel.innerHTML = `Simpan`;
    btnSubmit.removeAttribute('disabled');

    let list = '';
    for (let index = 0; index < totalInputList; index++) {
        list += componentListInput(index)
    }
    listInputContent.innerHTML = list;
}

const selectContact = (value) => {
    formData.contact = value.dataset.name;

    contactInput.value = formData.contact;
    formData.desc = `Pendapatan Dari: ${formData.contact}`;
    descriptionInput.value = formData.desc;

    validationInput();
}

const componentContactDropdownList = async (value) => {
    let contacts = await getContacts(value);

    let list = '';
    contacts.map(contact => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectContact(this)" data-name="${contact.name}" data-id="${contact.id}">
        ${contact.name}
    </button>`
    });

    return list;
}

const showContactDropdown = (value) => {
    contactList.innerHTML = `
        <button type="button" class="list-group-item list-group-item-action text-center">
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </button>`;    
    setTimeout(async () => {
        
        let list = await componentContactDropdownList(value.value);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`;
        contactList.innerHTML = list ? list : empty;
    }, 200);

    contactList.classList.remove('d-none');
}

const changeContactDropdown = async (value) => {
    value.value = formData.contact ? formData.contact : '';
    validationInput();
}

const changeDataInput = (value) => {
    formData.date = value.value;
}

const componentListInput = (index) => {
    return `<div class="list-input-Revenue">
        <div class="row justify-content-between py-2">
            <div class="col-5">
                <div class="position-relative">
                    <input type="text" class="form-control search-input-dropdown from-account-input" placeholder="Akun" aria-label="Akun" aria-describedby="create-sub-category-account" onclick="showAccountDropdown(this)" onkeyup="showAccountDropdown(this)"
                    onchange="changeAccountDropdown(this)" autocomplete="off" data-order="${index}" value="${formData.listInput[index].accountName}">
                    <div class="d-none bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary account-list" style="max-height: 130px; z-index:1">

                    </div>
                </div>
            </div>
            <div class="col-5 text-end">
                <input type="text" class="form-control text-end total-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].total}" onkeyup="setCurrencyFormat(this)" onchange="changeTotal(this)" data-order="${index}">
            </div>
            <div class="col-1">
                <button class="btn btn-sm btn-danger btn-remove-row" onclick="deleteRowInput(this)" data-order="${index}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>`
}

const selectToAccount = (value) => {
    formData.to_account = {
        id: parseInt(value.dataset.id),
        name: value.dataset.name
    }

    toAccountInput.value = formData.to_account.name;
    validationInput();
}

const changeToAccountDropdown = (value) => {
    value.value = formData.to_account?.name;
    validationInput();
}

const componentToAccountDropdownList = async (value) => {
    let toAccounts = await getAccounts(value, 1);

    let list = '';
    toAccounts.data.map(toAccount => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectToAccount(this)" data-name="${toAccount.name}" data-id="${toAccount.id}">
        ${toAccount.name}
    </button>`
    });

    return list;
}

const showToAccountDropdown = (value) => {
    contactList.innerHTML = `
            <button type="button" class="list-group-item list-group-item-action text-center">
            <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </button>`;  
    setTimeout(async () => {
          
        let list = await componentToAccountDropdownList(value.value);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`;
        toAccountList.innerHTML = list ? list : empty;
    }, 200);

    toAccountList.classList.remove('d-none');
}

const changeDateInput = (value) => {
    formData.date = value.value;
}

async function getNoRefAccountAuto (value) {
    formData.desc = capital(value.value);
    descriptionInput.value = formData.desc;

    validationInput();
}

function noRefChange(value){
    formData.no_ref = capital(value.value);

    validationInput();
}

function detailInputChange(value){
    formData.detail = value.value;
}

const componentAccountDropdownList = async (value, index) => {
    let accounts = await getAccounts(value, 0);

    let list = '';
    accounts.data.map(account => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectAccount(this)" data-name="${account.name}" data-id="${account.id}" data-order="${index}">
        <div style="font-size: 10px">${account.code}</div>
        ${account.name}
    </button>`
    });

    return list;
}

async function showAccountDropdown(value){
    const accountLists = Array.from(document.getElementsByClassName('account-list'));

    accountLists[value.dataset.order].innerHTML = `
    <button type="button" class="list-group-item list-group-item-action text-center">
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </button>`

    setTimeout(async () => {
        let list = await componentAccountDropdownList(value.value, value.dataset.order);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        accountLists[value.dataset.order].innerHTML = list ? list : empty;
    }, 100);
    

    accountLists[value.dataset.order].classList.remove('d-none');
}

const changeAccountDropdown = (value) => {
    value.value = formData.listInput[value.dataset.order].accountName;
}

const selectAccount = (value) => {
    const fromAccountInput = Array.from(document.getElementsByClassName('from-account-input'));

    formData.listInput[value.dataset.order].accountName = value.dataset.name;
    formData.listInput[value.dataset.order].accountId = parseInt(value.dataset.id);

    fromAccountInput[value.dataset.order].value = formData.listInput[value.dataset.order].accountName;

    adjustTotalValues();
    validationInput();
}

const adjustTotalValues = () => {
    const totaInputs = Array.from(document.getElementsByClassName('total-input'));
    totalInput = 0;
    totaInputs.map(total => {
        totalInput += parseInt(toPrice(total.value))
    });

    totalLabel.innerHTML = `Rp. ${formatRupiah(totalInput.toString())}`
}

const changeTotal = (value) => {
    adjustTotalValues();

    formData.listInput[value.dataset.order].total = parseInt(toPrice(value.value));

    validationInput();
}

const addListInput = () => {
    formData.listInput = [...formData.listInput, {
        accountId:null,
        accountName:'',
        total:0,
    }];

    const row = document.createElement('div');
    row.className = 'list-input-Revenue';
    row.innerHTML = componentListInput(totalInputList);
    totalInputList++;

    listInputContent.appendChild(row);
    validationInput();
}

const deleteRowInput = (value) => {
    let order = value.dataset.order;

    totalInputList--;
    //hapus data pada RevenueData
    formData.listInput.splice(parseInt(order), 1);

    // hapus element 
    document.getElementsByClassName('list-input-Revenue')[parseInt(order)].remove();

    //set ulang attribute order setiap row
    for (let index = 0; index < totalInputList; index++) {
        document.getElementsByClassName('search-input-dropdown')[index].setAttribute('data-order', index);
        document.getElementsByClassName('total-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('btn-remove-row')[index].setAttribute('data-order', index);
    }

    adjustTotalValues();
    validationInput();

}

async function submitRevenue(){
    formData.value = totalInput;
    try {
        btnSubmit.setAttribute('disabled','disabled');
        submitButtonLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`
        let result = await postRevenue(formData);

        let message = `${result.desc} Berhasil Ditambahkan`;
        
        myToast(message, 'success');

        setDefaultValue();
    } catch (error) {
        console.log(error);
    }
}

window.addEventListener('load', function(){
    setDefaultValue();
    
})