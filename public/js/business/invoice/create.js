const successAlert = document.querySelector('#success-alert');
const successAlertMessage = document.querySelector('#success-alert-message');

const dateInput = document.querySelector('#date-input');
const descriptionInput = document.querySelector('#description-input');
const noRefInput = document.querySelector('#no-ref-input');
const contactInput = document.querySelector('#contact-input');

const listInputContent = document.querySelector('#list-input-content');
const btnSubmitInvoice = document.querySelector('#btn-submit-invoice');

const submitButtonLabel = document.querySelector('#submit-button-label');
const btnSubmit = document.querySelector('#btn-submit');

let totalInputList = 1;
let orderProductReq = 0;

let formData = {
    date: '',
    description: '',
    no_ref: '',
    contact: {
        id:null,
        name: '',
    },
    listInput:[
        {
            productId:null,
            productName:'',
            productCategory:'',
            qty:0,
            selling_price:0,
            total: 0
        }
    ],
    debit: {
        account_id:null,
        account_name: '',
        value: 0
    },
    total: 0,
    balance: 0
}

const setDefaultValue = async () => {
    totalInputList = 1;

    let newRefValue = await newRef();


    formData = {
        date: dateNow(),
        description: '',
        no_ref: newRefValue.data.data,
        detail: '',
        contact: {
            id:null,
            name: '',
        },
        listInput:[
            {
                productId:null,
                productName:'',
                qty:0,
                selling_price:0,
                total: 0
            },
        ],
        debit: {
            account_id:null,
            account_name: '',
            value: 0
        },
        total: 0,
        balance: 0
    }
    validationInput();
    setValueInputComponent();
}

const validationInput = () => {
    
    let isValidated = false;

    if (formData.description && formData.no_ref && formData.contact.name && formData.debit.account_name) {
        isValidated = true
        
        formData.listInput.map(list => {
            if (!list.productName || !list.total > 0) {
                isValidated = false;
            }
        })
    }

    isValidated 
    ? btnSubmitInvoice.classList.remove('d-none')
    : btnSubmitInvoice.classList.add('d-none')
    
}

const setValueInputComponent = () => {
    
    dateInput.value = formData.date;
    descriptionInput.value = formData.description;
    noRefInput.value = formData.no_ref;
    contactInput.value = formData.contact.name;
    document.querySelector('#grand-total').innerHTML = `Rp.${formData.total}`;
    document.querySelector('#payment-input').value = formatRupiah(formData.debit.value.toString());
    document.querySelector('#return-payment-input').innerHTML = `Rp.${formData.balance}`;
    document.querySelector('#account-debit-input').value = formData.debit.account_name;

    btnSubmitInvoice.classList.add('d-none');

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
            <div class="col-4 d-flex">
                <div class="d-block d-md-none">
                    <button class="btn btn-search-product" data-bs-toggle="modal" data-bs-target="#exampleModal" data-order="${index}" onclick="searchProduct(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                </div>
                <div class="d-block d-md-none">
                    <input disabled type="text" class="form-control search-input-dropdown search-product-input w-100" placeholder="Produk" aria-label="Produk" aria-describedby="create-sub-category-account" autocomplete="off" data-order="${index}" value="${formData.listInput[index].productName}">
                </div>
                <div class="position-relative d-none d-md-block w-100">
                    <input type="text" class="form-control search-input-dropdown search-product-input-dropdown" placeholder="Produk" aria-label="Produk" aria-describedby="create-sub-category-account" onclick="showProductDropdown(this)" onkeyup="showProductDropdown(this)"
                    onchange="changeProductDropdown(this)" autocomplete="off" data-order="${index}" value="${formData.listInput[index].productName}">
                    <div class="d-none bg-light position-absolute list-group w-100 search-select overflow-auto custom-scroll border border-2 border-secondary contact-list" style="max-height: 130px; z-index:100">

                    </div>
                </div>
            </div>
            <div class="col-1 col-md-2 text-end">
                <input type="text" class="form-control text-end qty-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].qty}" onkeyup="setCurrencyFormat(this)" onchange="changeQty(this)" data-order="${index}">
            </div>
            <div class="col-3 col-md-2 text-end">
                <input type="text" class="form-control text-end selling-price-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].selling_price}" onkeyup="setCurrencyFormat(this)" onchange="changeSellingPrice(this)" data-order="${index}">
            </div>
            <div class="col-3 col-md-2 text-end">
                <input type="text" class="form-control text-end total-input" inputmode="numeric" autocomplete="off" onclick="this.select()" value="${formData.listInput[index].total}" onkeyup="setCurrencyFormat(this)" onchange="changeTotal(this)" data-order="${index}" disabled>
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

function changeContact(value){
    value.value = formData.contact.name;
}

function selectContact(value) {
    formData.contact.name = value.dataset.name;
    formData.contact.id = parseInt(value.dataset.id);
    formData.description = `Faktur Penjualan Kepada ${formData.contact.name}`;

    contactInput.value = formData.contact.name;
    descriptionInput.value = formData.description;
    validationInput();
}

function detailInputChange (value){
    formData.description = value.value;
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

async function showContactDropdown(value){
    const contactLists = document.querySelector('#contact-list');

    contactLists.innerHTML = `
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
        let list = await componentContactDropdownList(value.value, value.dataset.order);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        contactLists.innerHTML = list ? list : empty;
    }, 100);
    

    contactLists.classList.remove('d-none');
}

function contactInputChange(value){
    value.value = formData.contact.name;
}

const componentProductDropdownList = async (value, index) => {
    let products = await getProducts(value);

    let list = '';
    products.data.map(product => {
        let check = true;
        let qty = product.is_stock_checked > 0 ? (product.stocks_sum_qty ? product.stocks_sum_qty : 0 ) : '';
        if (product.is_stock_checked > 0) {
            check = product.stocks_sum_qty > 0 ? true : false;
        }
        
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectProduct(this)" data-name="${product.name}" data-id="${product.id}" data-category="${product.category}" data-order="${index}" data-selling-price="${product.selling_price}" ${ check ? '' : 'disabled'}>
        <div class="row justify-content-between" style="font-size: 10px">
            <div class="col-6">${product.code}</div>
            <div class="col-6 text-end ${(product.stocks_sum_qty > 0 && product.is_stock_checked == 1) ? 'text-success' : 'text-danger'}">${qty} ${product.is_stock_checked ? product.unit : ''}</div>
        </div>
            ${product.name}
    </button>`
    });

    return list;
}

async function showProductDropdown(value){
    const contactLists = Array.from(document.getElementsByClassName('contact-list'));

    contactLists[value.dataset.order].innerHTML = `
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
        contactLists[value.dataset.order].innerHTML = list ? list : empty;
    }, 100);
    

    contactLists[value.dataset.order].classList.remove('d-none');
}

const changeProductDropdown = (value) => {
    value.value = formData.listInput[value.dataset.order].accountName;
}

const grandTotal = () => {
    let total = 0;
    formData.listInput.map(list => {
        total += list.total;
    })
    formData.total = total;
    formData.debit.value = total;
    formData.balance = 0;

    document.querySelector('#grand-total').innerHTML = `Rp. ${formatRupiah(formData.total.toString())}`;
    document.querySelector('#payment-input').value = `${formatRupiah(formData.total.toString())}`;
}

const selectProduct = (value) => {
    const searchProductInputDropdown = Array.from(document.getElementsByClassName('search-product-input-dropdown'));
    const searchProductInput = Array.from(document.getElementsByClassName('search-product-input'));
    const sellingPriceInput = Array.from(document.getElementsByClassName('selling-price-input'));
    const qtyInput = Array.from(document.getElementsByClassName('qty-input'));
    const totalInput = Array.from(document.getElementsByClassName('total-input'));

    formData.listInput[value.dataset.order].productName = value.dataset.name;
    formData.listInput[value.dataset.order].productCategory = value.dataset.category;
    formData.listInput[value.dataset.order].productId = parseInt(value.dataset.id);
    formData.listInput[value.dataset.order].selling_price = parseInt(value.dataset.sellingPrice);
    formData.listInput[value.dataset.order].qty = 1;
    formData.listInput[value.dataset.order].total = parseInt(value.dataset.sellingPrice);

    searchProductInputDropdown[value.dataset.order].value = formData.listInput[value.dataset.order].productName;
    searchProductInput[value.dataset.order].value = formData.listInput[value.dataset.order].productName;
    sellingPriceInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].selling_price.toString());
    qtyInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].qty.toString());
    totalInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].total.toString());

    grandTotal();
    validationInput();
}

const changeQty = (value) => {
    const totalInput = Array.from(document.getElementsByClassName('total-input'));

    formData.listInput[value.dataset.order].qty = parseInt(toPrice(value.value));
    formData.listInput[value.dataset.order].total = formData.listInput[value.dataset.order].qty * formData.listInput[value.dataset.order].selling_price;

    totalInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].total.toString());

    grandTotal();
    validationInput();
}

const changeSellingPrice = (value) => {
    const totalInput = Array.from(document.getElementsByClassName('total-input'));

    formData.listInput[value.dataset.order].selling_price = parseInt(toPrice(value.value));
    formData.listInput[value.dataset.order].total = formData.listInput[value.dataset.order].qty * formData.listInput[value.dataset.order].selling_price;

    totalInput[value.dataset.order].value = formatRupiah(formData.listInput[value.dataset.order].total.toString());

    grandTotal();
    validationInput();
}

const addListInput = () => {
    formData.listInput = [...formData.listInput, {
        productId:null,
        productName:'',
        qty:0,
        selling_price:0,
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
        document.getElementsByClassName('btn-search-product')[index].setAttribute('data-order', index);
        document.getElementsByClassName('search-product-input-dropdown')[index].setAttribute('data-order', index);
        document.getElementsByClassName('search-product-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('qty-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('selling-price-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('total-input')[index].setAttribute('data-order', index);
        document.getElementsByClassName('btn-remove-row')[index].setAttribute('data-order', index);
    }

    validationInput();

}

async function submitInvoice(){
    try {
        btnSubmit.setAttribute('disabled','disabled');
        submitButtonLabel.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`
        let result = await postInvoice(formData);
        
        let message = `${result.description} Berhasil Ditambahkan`;
        
        myToast(message, 'success');

        setDefaultValue();
        setValueInputComponent();
        goToPintInvoice(result['id']);
    } catch (error) {
        console.log(error);
    }
}

function selectAccount(value) {
    formData.debit.account_name = value.dataset.name;
    formData.debit.account_id = parseInt(value.dataset.id);

    document.querySelector('#account-debit-input').value = formData.debit.account_name;
    validationInput();
}

function changeAccountDebit(value){
    value.value = formData.debit.account_name
}

const componentAccountDebitDropdownList = async (value) => {
    let account = await getAccounts(value);

    let list = '';
    account.data.map(account => {
        if (formData.debit.value < 1) {
            if (account.sub_category == 'Piutang Usaha') {
                list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectAccount(this)" data-name="${account.name}" data-id="${account.id}">
                    <div style="font-size: 10px">${account.code}</div>
                    ${account.name}
                </button>`
            }
        } else {
            list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectAccount(this)" data-name="${account.name}" data-id="${account.id}">
            <div style="font-size: 10px">${account.code}</div>
            ${account.name}
        </button>`
        }
    });

    return list;
}

async function showAccountDebitDropdown(value){
    const accountLists = document.querySelector('#account-debit-list');

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
        let list = await componentAccountDebitDropdownList(value.value, value.dataset.order);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        accountLists.innerHTML = list ? list : empty;
    }, 100);
    

    accountLists.classList.remove('d-none');
}

function debitAccountInputChange(value){
    value.value = formData.debit.account_name;
}

function changePayment(value)
{
    formData.debit.value = toPrice(value.value);
    formData.balance = formData.debit.value - formData.total;

    document.querySelector('#return-payment-input').innerHTML = `Rp. ${formatRupiah(formData.balance.toString())}`

    if (value.value < 1) {
        formData.debit = {
            account_id:null,
            account_name: '',
            value: 0
        }

        document.querySelector('#account-debit-input').value = '';
        
    }
}

function searchProduct(value)
{
    orderProductReq = value.dataset.order;
    document.querySelector('#search-product-input').value = '';
    document.getElementById('product-lists').innerHTML = `<button href="#" class="list-group-item list-group-item-action disabled" aria-current="true">
    No Option
</button>`;
}

async function componentProductModalList(value, index){
    let products = await getProducts(value);

    let list = '';
    products.data.map(product => {
        list += `
            <button class="list-group-item list-group-item-action" aria-current="true" onclick="selectProduct(this)" data-name="${product.name}" data-id="${product.id}" data-category="${product.category}" data-order="${index}" data-selling-price="${product.selling_price}" data-bs-dismiss="modal">
                ${product.name}
            </button>`
    });

    return list;
}

function changeSearchProductModal(value){
    const productLists = document.getElementById('product-lists');
    setTimeout(async () => {
        let list = await componentProductModalList(value.value, orderProductReq);
        
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        productLists.innerHTML = list ? list : empty;
    }, 100);
}

window.addEventListener('load', function(){
    setDefaultValue();
    setValueInputComponent();
})