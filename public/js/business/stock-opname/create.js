const successAlert = document.querySelector('#success-alert');
const successAlertMessage = document.querySelector('#success-alert-message');

const dateInput = document.querySelector('#date-input');
const descriptionInput = document.querySelector('#description-input');
const noRefInput = document.querySelector('#no-ref-input');

const listInputContent = document.querySelector('#list-input-content');
const btnSubmitStockOpname = document.querySelector('#btn-submit-stock-opname');

const submitButtonLabel = document.querySelector('#submit-button-label');
const btnSubmit = document.querySelector('#btn-submit');

let totalInputList = 1;
let differenceDebitCredit = 0;

let formData = {
    date: '',
    description: 'Stok Opname',
    no_ref: '',
    listInput:[
        {
            productId:null,
            productName:'',
            qty_book:0,
            qty_physic:0,
            qty_balance: 0,
            account:{
                id: null,
                name: ''
            }
        },
    ]
}

const setDefaultValue = async () => {
    totalInputList = 1;

    let newRefValue = await newRef();

    formData = {
        date: dateNow(),
        description: 'Stok Opname',
        no_ref: newRefValue.data.data,
        detail: '',
        listInput:[
            {
                productId:null,
                productName:'',
                qty_book:0,
                qty_physic:0,
                qty_balance: 0,
                account:{
                    id: null,
                    name: ''
                }
            },
        ]
    }
    validationInput();
    setValueInputComponent();
}

const validationInput = () => {
    
    let isValidated = false;

    if (formData.listInput.length > 0 &&formData.description && formData.no_ref) {
        isValidated = true
        
        formData.listInput.map(list => {
            if (!list.productName || !list.qty_balance > 0 || !list.account.id) {
                isValidated = false;
            }
        })
    }

    isValidated 
    ? btnSubmitStockOpname.classList.remove('d-none')
    : btnSubmitStockOpname.classList.add('d-none')
}

const setValueInputComponent = () => {
    
    dateInput.value = formData.date;
    descriptionInput.value = formData.description;
    noRefInput.value = formData.no_ref;

    btnSubmitStockOpname.classList.add('d-none');

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
            <div class="col-3">
                <div class="position-relative">
                    <input type="text" class="form-control search-input-dropdown search-product-input-dropdown" placeholder="Produk" aria-label="Produk" onclick="showProductDropdown(this)" onkeyup="showProductDropdown(this)"
                    onchange="changeProductDropdown(this)" autocomplete="off" data-order="${index}" value="${formData.listInput[index].productName}">
                    <div class="d-none bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary product-list" style="max-height: 130px; z-index:1">

                    </div>
                </div>
            </div>
            <div class="col-1 text-end">
                <input type="text" class="form-control text-end qty-book-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].qty_book}" onkeyup="setCurrencyFormat(this)" onchange="changeQtyBook(this)" data-order="${index}" disabled>
            </div>
            <div class="col-1 text-end">
                <input type="text" class="form-control text-end qty-physic-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].qty_physic}" onkeyup="setCurrencyFormat(this)" onchange="changeQtyPhysic(this)" data-order="${index}">
            </div>
            <div class="col-1 text-end">
                <input type="text" class="form-control text-end qty-balance-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].qty_balance}" onkeyup="setCurrencyFormat(this)" onchange="changeQtyBalance(this)" data-order="${index}" disabled>
            </div>
            <div class="col-5">
                <div class="position-relative">
                    <input type="text" class="form-control search-input-dropdown search-account-input-dropdown"
                    placeholder="Akun" aria-label="Akun" aria-describedby="create-sub-category-account" onclick="showAccountDropdown(this)" onkeyup="showAccountDropdown(this)"
                    onchange="changeAccountDropdown(this)" autocomplete="off" data-order="${index}" value="${formData.listInput[index].account.name}">
                    <div class="d-none bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary account-list" style="max-height: 130px; z-index:1">

                    </div>
                </div>
            </div>
            <div class="col-1 text-center">
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
    const searchAccountInputDropdown = Array.from(document.getElementsByClassName('search-account-input-dropdown'));

    formData.listInput[value.dataset.order].account.name = value.dataset.name;
    formData.listInput[value.dataset.order].account.id = parseInt(value.dataset.id);

    searchAccountInputDropdown[value.dataset.order].value = formData.listInput[value.dataset.order].account.name;

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

//
const componentProductDropdownList = async (value, index) => {
    let products = await getProducts(value);

    let list = '';
    products.data.map(product => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectProduct(this)" data-name="${product.name}" data-id="${product.id}" data-category="${product.category}" data-order="${index}" data-qty="${product.stocks_sum_qty}">
        <div style="font-size: 10px">${product.code}</div>
        ${product.name}
    </button>`
    });

    return list;
}

async function showProductDropdown(value){
    const productLists = Array.from(document.getElementsByClassName('product-list'));

    productLists[value.dataset.order].innerHTML = `
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
        productLists[value.dataset.order].innerHTML = list ? list : empty;
    }, 100);
    

    productLists[value.dataset.order].classList.remove('d-none');
}

const changeProductDropdown = (value) => {
    value.value = formData.listInput[value.dataset.order].productName;
}

const selectProduct = (value) => {
    const searchProductInputDropdown = Array.from(document.getElementsByClassName('search-product-input-dropdown'));
    const qtyBookInput = Array.from(document.getElementsByClassName('qty-book-input'));
    const qtyBalanceInput = Array.from(document.getElementsByClassName('qty-balance-input'));

    formData.listInput[value.dataset.order].productName = value.dataset.name;
    formData.listInput[value.dataset.order].productCategory = value.dataset.category;
    formData.listInput[value.dataset.order].productId = parseInt(value.dataset.id);
    formData.listInput[value.dataset.order].qty_book = parseInt(value.dataset.qty);
    formData.listInput[value.dataset.order].qty_balance = parseInt(value.dataset.qty) * -1;

    searchProductInputDropdown[value.dataset.order].value = formData.listInput[value.dataset.order].productName;
    qtyBookInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].qty_book.toString());
    qtyBalanceInput[value.dataset.order].value = formData.listInput[value.dataset.order].qty_balance;

    validationInput();
}
//

const componentAccountDropdownList = async (value, index) => {
    let accounts = await getAccounts(value);

    let list = '';
    accounts.data.map(account => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectAccount(this)" data-name="${account.name}" data-id="${account.id}" data-category="${account.sub_category}" data-order="${index}">
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
    value.value = formData.listInput[value.dataset.order].productName;
}

const changeQtyPhysic = (value) => {
    const qtyBalanceInput = Array.from(document.getElementsByClassName('qty-balance-input'));

    formData.listInput[value.dataset.order].qty_physic = parseInt(toPrice(value.value));
    formData.listInput[value.dataset.order].qty_balance =  formData.listInput[value.dataset.order].qty_physic - formData.listInput[value.dataset.order].qty_book;

    qtyBalanceInput[value.dataset.order].value = formData.listInput[value.dataset.order].qty_balance;
    validationInput();
}

const addListInput = () => {
    formData.listInput = [...formData.listInput, {
        productId:null,
        productName:'',
        qty_book:0,
        qty_physic:0,
        qty_balance: 0,
        account:{
            id: null,
            name: ''
        }
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
        document.getElementsByClassName('qty-book-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('qty-physic-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('qty-balance-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('search-account-input-dropdown')[index].setAttribute('data-order', index);
        document.getElementsByClassName('btn-remove-row')[index].setAttribute('data-order', index);
    }

    validationInput();

}

async function submitStockOpname(){
    try {
        btnSubmit.setAttribute('disabled','disabled');
        submitButtonLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`
        let result = await postStockOpname(formData);

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