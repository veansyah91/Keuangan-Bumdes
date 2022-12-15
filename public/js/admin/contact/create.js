const successAlert = document.querySelector('#success-alert');
const successAlertMessage = document.querySelector('#success-alert-message');

const toAccountList = document.querySelector('#to-account-list');
const toAccountInput = document.querySelector('#to-account-input');

const fromAccountList = document.querySelector('#from-account-list');
const fromAccountInput = document.querySelector('#from-account-input');

const dateInput = document.querySelector('#date-input');
const descriptionInput = document.querySelector('#description-input');
const noRefInput = document.querySelector('#no-ref-input');
const detailInput = document.querySelector('#detail-input');
const valueInput = document.querySelector('#value-input');

const btnSubmitCashMutation = document.querySelector('#btn-submit-cash-mutation');

const submitButtonLabel = document.querySelector('#submit-button-label');
const btnSubmit = document.querySelector('#btn-submit');


let formData = {
    from_account: {
        id:0,
        name: '',
    },
    to_account: {
        id:0,
        name: '',
    },
    date: '',
    desc: '',
    no_ref: '',
    detail: '',
    value: 0,
}

const setDefaultValue = async () => {
    let res = await getNewNoRef();

    formData = {
        from_account: {
            id:0,
            name: '',
        },
        to_account: {
            id:0,
            name: '',
        },
        date: dateNow(),
        desc: '',
        no_ref:  res,
        detail: '',
        value: 0,
    }

    setValueInputComponent();
}

const validationInput = () => {

    let isValidated = false;

    if (formData.desc && formData.no_ref && formData.value && formData.from_account.name && formData.to_account.name) {
        isValidated = true;
    }
    isValidated 
    ? btnSubmitCashMutation.classList.remove('d-none')
    : btnSubmitCashMutation.classList.add('d-none')
    
}

const setValueInputComponent = () => {
    dateInput.value = formData.date;
    descriptionInput.value = formData.desc;
    noRefInput.value = formData.no_ref;
    detailInput.value = formData.detail;
    toAccountInput.value = formData.from_account.name;
    valueInput.value = formData.value;

    btnSubmitCashMutation.classList.add('d-none');

    submitButtonLabel.innerHTML = `Simpan`;
    btnSubmit.removeAttribute('disabled');
}

const changeDataInput = (value) => {
    formData.date = value.value;
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
    toAccountList.innerHTML = `
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

const componentFromAccountDropdownList = async (value, component) => {
    let accounts = await getAccounts(value, 0);

    let list = '';
    accounts.data.map(account => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectFromAccount(this)" data-name="${account.name}" data-id="${account.id}">
        <div style="font-size: 10px">${account.code}</div>
        ${account.name}
    </button>`
    });

    return list;
}

async function showFromAccountDropdown(value){
    fromAccountList.innerHTML = `
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
        let list = await componentFromAccountDropdownList(value.value, fromAccountInput);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        fromAccountList.innerHTML = list ? list : empty;
    }, 100);
    
    fromAccountList.classList.remove('d-none');
}

const changeFromAccountDropdown = (value) => {
    value.value = formData.from_account.name
}

const selectFromAccount = (value) => {
    formData.from_account.name = value.dataset.name;
    formData.from_account.id = parseInt(value.dataset.id);

    fromAccountInput.value = formData.from_account.name;
    changeDescriptionAutomatically();
    validationInput();
}

const changeToAccountDropdown = (value) => {
    value.value = formData.to_account.name
}

const changeDescriptionAutomatically = () => {
    descriptionInput.value = formData.from_account.name && formData.to_account.name ? `Mutasi Dari: ${formData.from_account.name}, Ke: ${formData.to_account.name}` : '';

    formData.desc = formData.from_account.name && formData.to_account.name ? `Mutasi Dari: ${formData.from_account.name}, Ke: ${formData.to_account.name}` : ''
}

const selectToAccount = (value) => {
    formData.to_account.name = value.dataset.name;
    formData.to_account.id = parseInt(value.dataset.id);

    toAccountInput.value = formData.to_account.name;

    changeDescriptionAutomatically();
    validationInput();
}

async function submitCashMutation(){
    try {
        btnSubmit.setAttribute('disabled','disabled');
        submitButtonLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`
        let result = await postMutationCash(formData);

        let message = `${result.desc} Berhasil Ditambahkan`;
        
        myToast(message, 'success');

        setDefaultValue();
    } catch (error) {
        console.log(error);
    }
}

const changeValue = (value) => {
    formData.value =  parseInt(toPrice(value.value));
    validationInput();
}

window.addEventListener('load', function(){
    setDefaultValue();
    
})