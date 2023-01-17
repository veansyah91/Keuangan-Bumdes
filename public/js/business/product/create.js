// input component
const nameInput = document.querySelector('#name-input');
const codeInput = document.querySelector('#code-input');
const unitInput = document.querySelector('#unit-input');
const unitPriceInput = document.querySelector('#unit-price-input');
const sellingPriceInput = document.querySelector('#selling-price-input');
const supplierInput = document.querySelector('#supplier-input');
const isStockChecked = document.querySelector('#is-stock-checked');
const categoryInput = document.querySelector('#category-input');

//category input
let categoryInputCreate = document.querySelector('#category-input-create');
let categoryInputCreateValue = '';

let formData = {
    name : '',
    code : '',
    unit : 'pcs',
    category : '',
    supplier : '',
    unit_price : 0,
    selling_price : 0,
    is_active : true,
    is_stock_checked : true
};

const setDefaultValue = async () => {
    formData = {
        name : '',
        code : '',
        unit : 'pcs',
        category : '',
        supplier : '',
        unit_price : 0,
        selling_price : 0,
        is_active : true,
        is_stock_checked : true
    };
}

const setValueInputComponent = () => {
    nameInput.value = formData.name;
    codeInput.value = formData.code;
    unitInput.value = formData.unit;
    categoryInput.value = formData.category;
    supplierInput.value = formData.supplier;
    unitPriceInput.value = formData.unit_price;
    sellingPriceInput.value = formData.selling_price;
    
    isStockChecked.setAttribute('checked', 'checked');

    document.querySelector('#submit-button-label').classList.remove('disabled');
    document.querySelector('#btn-submit').removeAttribute('disabled');

    document.querySelector('#submit-button-label').innerHTML = `Simpan`;
    validateInput();
}

const validateInput = () => {
    let isValidated = false;

    if (formData.name && formData.code && formData.category && formData.supplier) {
        isValidated = true;
    }

    isValidated 
    ? document.querySelector('#btn-submit-product').classList.remove('d-none')
    : document.querySelector('#btn-submit-product').classList.add('d-none')
}

const changeProduct = async (value) => {
    formData.name = value.value;

    let referenceNo = `${abbreviation(value.value)}-`;
    let result = await newRef(referenceNo);
    
    formData.code = result.data.data;
    codeInput.value = formData.code;
    validateInput();
}

const changeUnit = async (value) => {
    formData.unit = value.value;
    validateInput();
}

const validateCategoryInput = async (value) => {
    const submitCategory = document.querySelector('#submit-category');

    if (value.value) {
        submitCategory.classList.remove('disabled');
        submitCategory.removeAttribute('disabled');
    } else {
        submitCategory.classList.add('disabled');
        submitCategory.setAttribute('disabled', 'disabled');
    }
}

const storeCategory = async (event) => {
    event.preventDefault();
    try {
        let res = await postCategory(categoryInputCreateValue);

        let message = `${res.nama} Berhasil Ditambahkan`;
        
        myToast(message, 'success');
        categoryInputCreate.value = '';
        showCategory();
    } catch (error) {
        myToast(error.response.data.errors.message, 'danger');
    }
}

function selectcategory(value){
    formData.category = value.dataset.name;
    categoryInput.value = formData.category;
    validateInput();
}

async function deleteCategory(id) {
    try {
        let res = await destroyCategory(id);

        let message = `${res.nama} Berhasil Dihapus`;

        myToast(message, 'success');
        categoryInputCreate.value = '';
        showCategory();

    } catch (error) {
        myToast(error.response.data.errors.message, 'danger');
    }
}

const showCategory = async () => {
    const categoryInput = document.querySelector('#category-input');
    
    try {
        categories = await getCategory(categoryInput.value);
        let list = '';
        categories?.map(category => {
            list += `
            <tr>
                <td>${category.nama}</td>
                <td class="text-end"><button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id})"><i class="bi bi-trash"></button></td>
            </tr>
            `;
        })
        document.querySelector('#category-account').innerHTML = list;
    } catch (error) {
        console.log(error);
    }

}

const componentCategoryDropdownList = async (value) => {
    let categories = await getCategories(value);

    let list = '';
    categories.data.data.map(category => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectcategory(this)" data-name="${category.nama}" data-id="${category.id}">
        ${category.nama}
    </button>`
    });

    return list;
}

async function changeCategory(value){
    value.value = formData.category;
}

async function showCategoryDropdown(value){
    const categoryLists = document.querySelector('#category-list');

    categoryLists.innerHTML = `
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
        let list = await componentCategoryDropdownList(value.value, value.dataset.order);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        categoryLists.innerHTML = list ? list : empty;
    }, 100);
    

    categoryLists.classList.remove('d-none');
}

const changeCategoryDropdown = (value) => {
    value.value = formData.supplier;
}

const changeCategoryCreateInput = (value) => {
    categoryInputCreateValue = value.value;
}

function selectcontact(value){
    formData.supplier = value.dataset.name;
    supplierInput.value = formData.supplier;
    validateInput();
}

function changeSupplier(value){
    value.value = formData.supplier;
    validateInput();
}

const componentSupplierDropdownList = async (value) => {
    let contacts = await getContacts(value);

    let list = '';
    contacts.data.data.map(contact => {
        list += `<button type="button" class="list-group-item list-group-item-action" onclick="selectcontact(this)" data-name="${contact.name}" data-id="${contact.id}">
        <div style="font-size: 10px">${contact.no_ref}</div>
        ${contact.name}
    </button>`
    });

    return list;
}

async function showSupplierDropdown(value){
    const supplierLists = document.querySelector('#supplier-list');

    supplierLists.innerHTML = `
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
        let list = await componentSupplierDropdownList(value.value);
        let empty = `<button type="button" class="list-group-item list-group-item-action text-center disabled" disabled>
            No Option
        </button>`
        supplierLists.innerHTML = list ? list : empty;
    }, 100);
    

    supplierLists.classList.remove('d-none');
}

const changeSupplierDropdown = (value) => {
    value.value = formData.supplier;
    validateInput();
}

function changeUnitPrice(value){
    formData.unit_price = parseInt(toPrice(value.value));
    validateInput();
}

function changeSellingPrice (value){
    formData.selling_price = parseInt(toPrice(value.value));
    validateInput();
}

function changeStockChecked(value){
    formData.is_stock_checked = !formData.is_stock_checked ;
    formData.is_stock_checked 
    ? value.setAttribute('checked', 'checked')
    : value.removeAttribute('checked');
    validateInput();
}

async function submitProduct(){
    document.querySelector('#submit-button-label').classList.add('disabled');
    document.querySelector('#btn-submit').setAttribute('disabled', 'disabled');

    document.querySelector('#submit-button-label').innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`;
    try {
        let res = await postProduct(formData);

        let message = `${res.name} Berhasil Ditambahkan`;
        
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