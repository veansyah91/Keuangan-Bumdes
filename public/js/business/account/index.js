const business = document.querySelector('#content').dataset.business;

const searchInput = document.querySelector('#search-input');
const listData = document.querySelector('#list-data');

const subClassificationInput = document.querySelector('#sub-classification-input');
const subClassificationCode = document.querySelector('#sub-classification-code');
const subClassificationAccount = document.querySelector('#sub-classification-account');
const subClassificationList = document.querySelector('#subclassification-list');

const validationNameInput = document.querySelector('#validation-name-input');
const validationNameCode = document.querySelector('#validation-name-code');

const subCategory = document.querySelector('#sub-category');
const code = document.querySelector('#code');
const nameInput = document.querySelector('#name');
const isCash = document.querySelector('#is-cash');
const isActive = document.querySelector('#is-active');

const token = `Bearer ${localStorage.getItem('token')}`;
let isUpdate = false;
let data = [];
let subCategories = [];
let search = '';
let searchSubClassification = '';

let formData = {
    id: 0,
    name :'',
    code :'',
    is_cash :false,
    is_active :false,
    sub_category :'',
}

function setDefault(){
    search = '';
    searchSubClassification = '';
    
    //form input
    subCategory.value = '';
    code.value = '';
    nameInput.value = '';
    isCash.checked = false;
    isActive.checked = true;

    //validation
    subCategory.classList.remove('is-invalid');
    code.classList.remove('is-invalid');
    nameInput.classList.remove('is-invalid');

    isUpdate = false;

    formData = {
        name :'',
        code :'',
        is_cash :false,
        is_active :false,
        sub_category :'',
    }
}

async function getData(search = ''){

    let url = `/api/${business}/account?search=${search}`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const searchForm = (event) => {
    event.preventDefault();

    search = searchInput.value;
    showData()
}

const showData = async () => {

    try {
        data = await getData(search);
    } catch (error) {
        return console.log(error);
    }

    document.querySelector('#count-data').innerHTML = data.total

    let list = '';
    data.data.map((d, i) => {
        list += `
        <div class="d-md-flex d-none justify-content-between border-top border-bottom py-2 px-1 content-data">
            <div style="width:1%" class="px-2">
                <div class="dropdown">
                    <button class="btn btn-sm" type="button" id="dropdownMenuButton${i}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${i}">
                        <li>
                            <a class="dropdown-item" href="#" onclick="editAccount(${d.id})" data-bs-toggle="modal" data-bs-target="#createModal">
                                <div class="row align-items-center justify-conter-start text-success">
                                    <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                    <div class="col-3">Ubah</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>  
            <div style="width:10%" class="px-2">${d.code}</div>
            <div style="width:30%" class="px-2">${d.name}</div>
            <div style="width:20%" class="px-2">${d.sub_category}</div>
            <div style="width:20%" class="px-2 ${d.is_active ? 'text-success' : 'text-danger'}">${d.is_active ? 'Aktif' : 'Tidak Aktif'}</div>
        </div>
        
        <div class="d-flex d-md-none justify-content-between border-top border-bottom py-2">
            <div style="width:10%" class="my-auto">
                <div class="btn-group dropstart">
                    <button class="btn btn-sm" type="button" id="dropdownMenuButton${i}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${i}">
                        <li>
                            <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#createModal" onclick="editAccount(${d.id})" >
                                <div class="row align-items-center justify-conter-start text-success">
                                    <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                    <div class="col-3">Ubah</div>
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div style="width:50%">
                <div class="font-bold">
                    ${d.name} 
                </div>
                <div class="">
                    <small>Kode: ${d.code}  </small>
                    
                </div>
            </div>
            <div style="width:40%" class="my-auto ${d.is_active ? 'text-success' : 'text-danger'}">
                ${d.is_active ? 'Aktif' : 'Tidak Aktif'}
            </div>
            
        </div>`;
    });

    listData.innerHTML = list;
}

const getSubCategory = async () => {
    let url = `/api/sub-category-account?search=${searchSubClassification}`;

    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const storeSubClassificationAccount = async (event) => {
    event.preventDefault();
    let url = `/api/sub-category-account`;

    subClassificationInput.classList.remove('is-invalid');
    subClassificationCode.classList.remove('is-invalid');

    let formData = { name: subClassificationInput.value, code: subClassificationCode.value};

    await axios.post(url, formData, {
        headers:{
            Authorization : token
        }
    })
    .then(res => {
        showSubCategory();
        subClassificationInput.value = '';
        subClassificationCode.value = '';
    })
    .catch(err => {
        console.log(err);
        err.response.data.errors.name && subClassificationInput.classList.add('is-invalid');
        err.response.data.errors.code && subClassificationCode.classList.add('is-invalid');

        err.response.data.errors.name ? validationNameInput.classList.remove('d-none') : validationNameInput.classList.add('d-none');
        err.response.data.errors.code ? validationNameCode.classList.remove('d-none') : validationNameCode.classList.add('d-none');
        
        validationNameInput.innerHTML = err.response.data.errors.name
        validationNameCode.innerHTML = err.response.data.errors.code
        
    })
}

async function deleteSubCategory(id)
{
    let url = `/api/sub-category-account/${id}`;


    await axios.delete(url, {
        headers:{
            Authorization : token
        }
    })
    .then(res => {
        let message = `Sub Klasifikasi Berhasil Dihapus`;
        myToast(message, 'success');
        showSubCategory();
    })
    .catch(err => {
        myToast(err.response.data.errors.message, 'danger');
        showData();
    })
}

const showSubCategory = async () => {
    subClassificationInput.value = '';
    subClassificationCode.value = '';

    try {
        subCategories = await getSubCategory();
    } catch (error) {
        return console.log(error);
    }

    let list = '';
    subCategories?.map(subCategory => {
        list += `
        <tr>
            <td>${subCategory.code}</td>
            <td>${subCategory.name}</td>
            <td class="text-end"><button class="btn btn-danger btn-sm" onclick="deleteSubCategory(${subCategory.id})"><i class="bi bi-trash"></button></td>
        </tr>
        `;
    })
    subClassificationAccount.innerHTML = list;
}

const selectSubClassification = async (value) => {
    
    subCategory.value = value.dataset.name;

    let subCategories = await getData(subCategory.value);
    
    let setCode = parseInt(subCategories.data[subCategories.data.length - 1].code);
    code.value = setCode + 1;
}

const showSearchSubClassification = async () => {
    let subClassificationAccountDataLists = await getSubCategory();

    let list = '';

    subClassificationAccountDataLists.map((data, index) => {
        list += `
        <button type="button" class="list-group-item list-group-item-action" onclick="selectSubClassification(this)" data-name="${subClassificationAccountDataLists[index].name}">
            ${subClassificationAccountDataLists[index].name}
        </button>
        `;
    })
    subClassificationList.innerHTML = list;
}

const changeSubCategoryDropdown = (value) => {
    searchSubClassification = value.value;

    showSearchSubClassification();
}

const showSubCategoryDropdown = async () => {

    subClassificationList.classList.remove('d-none');

    searchSubClassification = subCategory.value;

    await showSearchSubClassification();
}

const submitAccount = async (event) => {
    event.preventDefault();
    
    formData = {...formData, 
        name :nameInput.value,
        code :code.value,
        is_cash :isCash.checked,
        is_active :isActive.checked,
        sub_category :subCategory.value,
    }

    if (isUpdate) {
        updateAccount();
    } else {
        storeAccount();
    }
   
}

const updateAccount = async () => {
    let url = `/api/${business}/account/${formData.id}`;

    await axios.put(url, formData, {
        headers:{
            Authorization : token
        }
    })
    .then(res => {
        let message = `Akun Berhasil Diubah`;
        
        myToast(message, 'success');
        showData();
    })
    .catch(err => {
        let response = err.response.data;

        response.errors.name ? nameInput.classList.add('is-invalid') : nameInput.classList.remove('is-invalid') ; 
        response.errors.code ? code.classList.add('is-invalid') : code.classList.remove('is-invalid') ; 
        response.errors.sub_category ? subCategory.classList.add('is-invalid') :  subCategory.classList.remove('is-invalid') ; 
    })
}

const storeAccount = async () => {
    let url = `/api/${business}/account`;
        await axios.post(url, formData, {
            headers:{
                Authorization : token
            }
        })
        .then(res => {
            console.log(res);
            let message = `Akun Berhasil Ditambahkan`;
        
            myToast(message, 'success');
            setDefault();
            showData();
        })
        .catch(err => {
            console.log(err);
            let response = err.response.data;
    
            response.errors.name ? nameInput.classList.add('is-invalid') : nameInput.classList.remove('is-invalid') ; 
            response.errors.code ? code.classList.add('is-invalid') : code.classList.remove('is-invalid') ; 
            response.errors.sub_category ? subCategory.classList.add('is-invalid') :  subCategory.classList.remove('is-invalid') ; 
        })
    
}

async function editAccount(id){
    isUpdate = true;
    document.querySelector('#createModalLabel').innerHTML = 'Ubah Akun';
    document.querySelector('#btn-submit').innerHTML = 'Ubah';

    formData = {...formData, 
        id : id
    }

    await axios.get(`/api/${business}/account/${id}`, {
                    headers:{
                        Authorization : token
                    }
                })
                .then(res => {
                    let result = res.data.data;

                    subCategory.value = result.sub_category;
                    code.value = result.code;
                    nameInput.value = result.name;
                    isCash.checked = result.is_cash;
                    isActive.checked = result.is_active;

                })
                .catch(err => {
                    console.log(err);
                })
}

function addData(){
    setDefault();
    document.querySelector('#createModalLabel').innerHTML = 'Buat Akun';
    document.querySelector('#btn-submit').innerHTML = 'Tambah';
}

window.addEventListener('load', function(){
    showData()
})