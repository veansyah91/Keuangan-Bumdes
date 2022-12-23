const successAlert = document.querySelector('#success-alert');
const successAlertMessage = document.querySelector('#success-alert-message');

const dateInput = document.querySelector('#date-input');
const descriptionInput = document.querySelector('#description-input');
const noRefInput = document.querySelector('#no-ref-input');
const detailInput = document.querySelector('#detail-input');

const listInputContent = document.querySelector('#list-input-content');
const btnSubmitJournal = document.querySelector('#btn-submit-journal');

const totalCredit = document.querySelector('#total-credit');
const totalDebit = document.querySelector('#total-debit');
const differenceDebitCreditComponent = document.querySelector('#difference-debit-credit');

const submitButtonLabel = document.querySelector('#submit-button-label');
const btnSubmit = document.querySelector('#btn-submit');

let totalInputList = 2;
let differenceDebitCredit = 0;
let totalDebitInput = 0;
let totalCreditInput = 0;

let formData = {
    date: '',
    desc: '',
    no_ref: '',
    detail: '',
    value: 0,
    listInput:[{},{}]
}

const setDefaultValue = () => {
    totalInputList = 2;
    differenceDebitCredit = 0;
    totalDebitInput = 0;
    totalCreditInput = 0;

    formData = {
        date: dateNow(),
        desc: '',
        no_ref: '',
        detail: '',
        listInput:[
            {
                accountId:null,
                accountName:'',
                debit:0,
                credit:0
            },
            {
                accountId:null,
                accountName:'',
                debit:0,
                credit:0
            }
        ]
    }
}

const validationInput = () => {

    let isValidated = false;

    if (formData.desc && formData.no_ref) {
        isValidated = true
        
        formData.listInput.map(list => {
            if (!list.accountName || differenceDebitCredit > 0) {
                isValidated &= false;
            }
        })
    }

    isValidated 
    ? btnSubmitJournal.classList.remove('d-none')
    : btnSubmitJournal.classList.add('d-none')
    
}

const setValueInputComponent = () => {
    dateInput.value = formData.date;
    descriptionInput.value = formData.desc;
    noRefInput.value = formData.no_ref;
    detailInput.value = formData.detail;

    totalDebit.innerHTML = 'Rp.0';
    totalCredit.innerHTML = 'Rp.0';

    btnSubmitJournal.classList.add('d-none');

    submitButtonLabel.innerHTML = `Simpan`;
    btnSubmit.removeAttribute('disabled');

    let list = '';
    for (let index = 0; index < totalInputList; index++) {
        list += componentListInput(index)
    }
    listInputContent.innerHTML = list;
}

const changeDataInput = (value) => {
    formData.date = value.value;
}

const componentListInput = (index) => {
    return `<div class="list-input-journal">
        <div class="row justify-content-between py-2">
            <div class="col-4">
                <div class="position-relative">
                    <input type="text" class="form-control search-input-dropdown" placeholder="Akun" aria-label="Akun" aria-describedby="create-sub-category-account" onclick="showAccountDropdown(this)" onkeyup="showAccountDropdown(this)"
                    onchange="changeAccountDropdown(this)" autocomplete="off" data-order="${index}" value="${formData.listInput[index].accountName}">
                    <div class="d-none bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary account-list" style="max-height: 130px; z-index:1">

                    </div>
                </div>
            </div>
            <div class="col-3 text-end">
                <input type="text" class="form-control text-end debit-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].debit}" onkeyup="setCurrencyFormat(this)" onchange="changeDebit(this)" data-order="${index}">
            </div>
            <div class="col-3 text-end">
                <input type="text" class="form-control text-end credit-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].credit}" onkeyup="setCurrencyFormat(this)" onchange="changeCredit(this)" data-order="${index}">
            </div>
            <div class="col-1">
                <button class="btn btn-sm btn-danger btn-remove-row" onclick="deleteRowInput(this)" data-order="${index}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>`
}

async function getNoRefAccountAuto (value) {
    formData.desc = capital(value.value);
    descriptionInput.value = formData.desc;

    let referenceNo = `${abbreviation(value.value)}-`;
    let result = await newRef(referenceNo);

    formData.no_ref = result.data.data;
    noRefInput.value = formData.no_ref;

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
    let accounts = await getAccounts(value);

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
    const searchInputDropdown = Array.from(document.getElementsByClassName('search-input-dropdown'));
    const debitInput = Array.from(document.getElementsByClassName('debit-input'));
    const creditInput = Array.from(document.getElementsByClassName('credit-input'));

    formData.listInput[value.dataset.order].accountName = value.dataset.name;
    formData.listInput[value.dataset.order].accountId = parseInt(value.dataset.id);

    searchInputDropdown[value.dataset.order].value = formData.listInput[value.dataset.order].accountName;

    if (totalDebitInput > totalCreditInput) {
        formData.listInput[value.dataset.order].credit = differenceDebitCredit;
        creditInput[value.dataset.order].value = formatRupiah(differenceDebitCredit.toString());

    }
    if (totalDebitInput < totalCreditInput) {
        formData.listInput[value.dataset.order].debit = differenceDebitCredit;
        debitInput[value.dataset.order].value = formatRupiah(differenceDebitCredit.toString());

    }

    adjustCreditValues();
    adjustDebitValues();
    validationInput();
}

const adjustCreditValues = () => {
    const creditInputs = Array.from(document.getElementsByClassName('credit-input'));

    totalCreditInput = 0;
    creditInputs.map(creditInput => {
        totalCreditInput += parseInt(toPrice(creditInput.value))
    });

    differenceDebitCredit = Math.abs(totalDebitInput - totalCreditInput);
    differenceDebitCreditComponent.innerHTML = `Rp.${formatRupiah(differenceDebitCredit.toString())}`;
    totalCredit.innerHTML = `Rp.${formatRupiah(totalCreditInput.toString())}`;
}

const adjustDebitValues = () => {
    const debitInputs = Array.from(document.getElementsByClassName('debit-input'));

    totalDebitInput = 0;
    debitInputs.map(debitInput => {
        totalDebitInput += parseInt(toPrice(debitInput.value))
    });

    differenceDebitCredit = Math.abs(totalDebitInput - totalCreditInput);
    differenceDebitCreditComponent.innerHTML = `Rp.${formatRupiah(differenceDebitCredit.toString())}`;
    totalDebit.innerHTML = `Rp.${formatRupiah(totalDebitInput.toString())}`;
}

const changeDebit = (value) => {
    adjustDebitValues();

    formData.listInput[value.dataset.order].debit = parseInt(toPrice(value.value));

    validationInput();
}

const changeCredit = (value) => {
    adjustCreditValues();

    formData.listInput[value.dataset.order].credit = parseInt(toPrice(value.value));

    validationInput();

}

const addListInput = () => {
    formData.listInput = [...formData.listInput, {
        accountId:null,
        accountName:'',
        debit:0,
        credit:0
    }];

    const row = document.createElement('div');
    row.className = 'list-input-journal';
    row.innerHTML = componentListInput(totalInputList);
    totalInputList++;

    listInputContent.appendChild(row);
}

const deleteRowInput = (value) => {
    let order = value.dataset.order;

    totalInputList--;
    //hapus data pada journalData
    formData.listInput.splice(parseInt(order), 1);

    // hapus element 
    document.getElementsByClassName('list-input-journal')[parseInt(order)].remove();

    //set ulang attribute order setiap row
    for (let index = 0; index < totalInputList; index++) {
        document.getElementsByClassName('search-input-dropdown')[index].setAttribute('data-order', index);
        document.getElementsByClassName('debit-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('credit-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('btn-remove-row')[index].setAttribute('data-order', index);
    }

    adjustCreditValues();
    adjustDebitValues();

    validationInput();

}

async function submitJournal(){
    formData.value = totalDebitInput;
    try {
        btnSubmit.setAttribute('disabled','disabled');
        submitButtonLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`
        let result = await postJournal(formData);

        let message = `${result.desc} Berhasil Ditambahkan`;
        
        myToast(message, 'success');

        setDefaultValue();
        setValueInputComponent();
    } catch (error) {
        console.log(error);
    }
}

window.addEventListener('load', function(){
    setDefaultValue();
    setValueInputComponent();
})