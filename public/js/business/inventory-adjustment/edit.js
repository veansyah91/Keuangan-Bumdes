const successAlert = document.querySelector('#success-alert');
const successAlertMessage = document.querySelector('#success-alert-message');

const dateInput = document.querySelector('#date-input');
const descriptionInput = document.querySelector('#description-input');
const noRefInput = document.querySelector('#no-ref-input');
const creditAccountInput = document.querySelector('#credit-account-input');

const listInputContent = document.querySelector('#list-input-content');
const btnSubmitInventoryAdjustment = document.querySelector('#btn-submit-inventory-adjustment');

const submitButtonLabel = document.querySelector('#submit-button-label');
const btnSubmit = document.querySelector('#btn-submit');

let totalInputList = 1;
let differenceDebitCredit = 0;

let formData = {
    date: '',
    description: 'Penyesuaian Stok',
    no_ref: '',
    credit: {
        id:null,
        name: '',
    },
    listInput:[
        {
            productId:null,
            productName:'',
            productCategory:'',
            qty:0,
            unit_price:0,
            total: 0
        }
    ]
}

const setDefaultValue = async () => {
    let res = await showData();

    totalInputList = res.listInput.length;

    formData = res;
    
    validationInput();
    setValueInputComponent();
}

const validationInput = () => {
    
    let isValidated = false;

    if (formData.description && formData.no_ref && formData.credit.name) {
        isValidated = true
        
        formData.listInput.map(list => {
            if (!list.productName || !list.total > 0) {
                isValidated = false;
            }
        })
    }

    isValidated 
    ? btnSubmitInventoryAdjustment.classList.remove('d-none')
    : btnSubmitInventoryAdjustment.classList.add('d-none')
    
}

const setValueInputComponent = () => {
    
    dateInput.value = formData.date;
    descriptionInput.value = formData.description;
    noRefInput.value = formData.no_ref;
    creditAccountInput.value = formData.credit.name;

    btnSubmitInventoryAdjustment.classList.add('d-none');

    submitButtonLabel.innerHTML = `Simpan`;
    btnSubmit.removeAttribute('disabled');

    let list = '';
    for (let index = 0; index < totalInputList; index++) {
        list += componentListInput(index)
    }
    listInputContent.innerHTML = list;
    validationInput();
}

const changeDataInput = (value) => {
    formData.date = value.value;
}

const componentListInput = (index) => {
    return `<div class="list-input-journal">
        <div class="row justify-content-between py-2">
            <div class="col-4">
                <div class="position-relative">
                    <input type="text" class="form-control search-input-dropdown search-product-input-dropdown" placeholder="Produk" aria-label="Produk" aria-describedby="create-sub-category-account" onclick="showProductDropdown(this)" onkeyup="showProductDropdown(this)"
                    onchange="changeProductDropdown(this)" autocomplete="off" data-order="${index}" value="${formData.listInput[index].productName}">
                    <div class="d-none bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary account-list" style="max-height: 130px; z-index:1">

                    </div>
                </div>
            </div>
            <div class="col-2 text-end">
                <input type="text" class="form-control text-end qty-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].qty}" onkeyup="setCurrencyFormat(this)" onchange="changeQty(this)" data-order="${index}">
            </div>
            <div class="col-2 text-end">
                <input type="text" class="form-control text-end unit-price-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formatRupiah(formData.listInput[index].unit_price.toString())}" onkeyup="setCurrencyFormat(this)" onchange="changeUnitPrice(this)" data-order="${index}">
            </div>
            <div class="col-2 text-end">
                <input type="text" class="form-control text-end total-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formatRupiah(formData.listInput[index].total.toString())}" onkeyup="setCurrencyFormat(this)" onchange="changeTotal(this)" data-order="${index}" disabled>
            </div>
            <div class="col-1">
                <button class="btn btn-sm btn-danger btn-remove-row" onclick="deleteRowInput(this)" data-order="${index}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>`
}

async function getNoRefAccountAuto(value) {
    formData.description = capital(value.value);
    descriptionInput.value = formData.description;

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

function changeAccount(value){
    value.value = formData.credit.name;
}

function selectAccount(value) {
    formData.credit.name = value.dataset.name;
    formData.credit.id = parseInt(value.dataset.id);

    creditAccountInput.value = formData.credit.name;
    validationInput();
}

function detailInputChange (value){
    formData.description = value.value;
    validationInput();
}

const componentAccountCreditDropdownList = async (value) => {
    let account = await getAccounts(value);

    let list = '';
    account.data.map(account => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectAccount(this)" data-name="${account.name}" data-id="${account.id}">
        <div style="font-size: 10px">${account.code}</div>
        ${account.name}
    </button>`
    });

    return list;
}

async function showAccountCreditDropdown(value){
    const accountLists = document.querySelector('#account-list');

    accountLists.innerHTML = `
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
        let list = await componentAccountCreditDropdownList(value.value, value.dataset.order);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        accountLists.innerHTML = list ? list : empty;
    }, 100);
    

    accountLists.classList.remove('d-none');
}

function creditAccountInputChange(value){
    value.value = formData.credit.name;
}

const componentProductDropdownList = async (value, index) => {
    let products = await getProducts(value);

    let list = '';
    products.data.map(product => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectProduct(this)" data-name="${product.name}" data-id="${product.id}" data-category="${product.category}" data-order="${index}" data-unit-price="${product.unit_price}">
        <div style="font-size: 10px">${product.code}</div>
        ${product.name}
    </button>`
    });

    return list;
}

async function showProductDropdown(value){
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
        let list = await componentProductDropdownList(value.value, value.dataset.order);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        accountLists[value.dataset.order].innerHTML = list ? list : empty;
    }, 100);
    

    accountLists[value.dataset.order].classList.remove('d-none');
}

const changeProductDropdown = (value) => {
    value.value = formData.listInput[value.dataset.order].productName;
}

const selectProduct = (value) => {
    const searchProductInputDropdown = Array.from(document.getElementsByClassName('search-product-input-dropdown'));
    const unitPriceInput = Array.from(document.getElementsByClassName('unit-price-input'));

    formData.listInput[value.dataset.order].productName = value.dataset.name;
    formData.listInput[value.dataset.order].productCategory = value.dataset.category;
    formData.listInput[value.dataset.order].productId = parseInt(value.dataset.id);
    formData.listInput[value.dataset.order].unit_price = parseInt(value.dataset.unitPrice);

    searchProductInputDropdown[value.dataset.order].value = formData.listInput[value.dataset.order].productName;
    unitPriceInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].unit_price.toString());

    validationInput();
}

const changeQty = (value) => {
    const totalInput = Array.from(document.getElementsByClassName('total-input'));

    formData.listInput[value.dataset.order].qty = parseInt(toPrice(value.value));
    formData.listInput[value.dataset.order].total = formData.listInput[value.dataset.order].qty * formData.listInput[value.dataset.order].unit_price;

    totalInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].total.toString());

    validationInput();
}

const changeUnitPrice = (value) => {
    const totalInput = Array.from(document.getElementsByClassName('total-input'));

    formData.listInput[value.dataset.order].unit_price = parseInt(toPrice(value.value));
    formData.listInput[value.dataset.order].total = formData.listInput[value.dataset.order].qty * formData.listInput[value.dataset.order].unit_price;

    totalInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].total.toString());

    validationInput();
}

const addListInput = () => {
    formData.listInput = [...formData.listInput, {
        productId:null,
        productName:'',
        qty:0,
        unit_price:0,
        total: 0
    }];

    const row = document.createElement('div');
    row.className = 'list-input-journal';
    row.innerHTML = componentListInput(totalInputList);
    totalInputList++;

    listInputContent.appendChild(row);
    validationInput();
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
        document.getElementsByClassName('search-product-input-dropdown')[index].setAttribute('data-order', index);
        document.getElementsByClassName('qty-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('unit-price-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('btn-remove-row')[index].setAttribute('data-order', index);
    }

    validationInput();

}

async function submitInventoryAdjustment(){
    try {
        btnSubmit.setAttribute('disabled','disabled');
        submitButtonLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`
        let result = await updateInventoryAdjustment(formData);

        let message = `${result.desc} Berhasil Diubah`;
        
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