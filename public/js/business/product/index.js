const searchInput = document.querySelector('#search-input');

const selectFilter = document.querySelector('#select-filter');
const dateRange = document.querySelector('#date-range');
const startFilter = document.querySelector('#start-filter');
const endFilter = document.querySelector('#end-filter');

const countData = document.querySelector('#count-data');
const prevBtn = document.querySelector('#prev-page');
const nextBtn = document.querySelector('#next-page');

const listData = document.querySelector('#list-data');

let search = '';
let page = 1;
let deleteId = null;
let filterBy = '';
let date_from, date_to, thisWeek, thisMonth, thisYear;

async function searchForm(event){
    event.preventDefault();

    search = searchInput.value;

    await showProduct();
}

function setDefault(){
    search = '';
    page = 1;
    deleteId = null;

    filterBy = 'this month';
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = 1;
    thisYear = '';
}

async function prevButton(){
    page--;
    await showProduct();
}

async function nextButton(){
    page++;
    await showProduct();
}

async function showProduct(){
    try {
        let url = `/api/${business}/product?search=${search}&page=${page}`;

        let response = await getProduct(url);

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
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleProduct(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#createModal" onclick="editProduct(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteProduct(${res.id})" >
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
                        <small>Kode: ${res.code}  </small>
                    </div>
                </div>
                <div style="width:40%" class="my-auto text-end">
                    Rp. ${formatRupiah(res.selling_price.toString())}
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
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleProduct(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                            <a class="dropdown-item" href="/${business}/product/${res.id}/edit">
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </a>
                            </li>
                            ${res.stocks_sum_qty > 0 ? '' : `<li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteProduct(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-danger">
                                        <div class="col-2"><i class="bi bi-trash"></i></div>
                                        <div class="col-3">Hapus</div>
                                    </div>
                                </button>
                            </li>`}
                            
                        </ul>
                    </div>
                </div>
                <div style="width:14%" class="px-2 my-auto">${res.code}</div>
                <div style="width:20%" class="px-2 my-auto">${res.name}</div>
                <div style="width:20%" class="px-2 my-auto">${res.category}</div>
                <div style="width:20%" class="px-2 text-end my-auto">${formatRupiah(res.selling_price.toString())}</div>
                <div style="width:10%" class="px-2 text-end my-auto">${ res.is_stock_checked ? (res.stocks_sum_qty ?? 0) : `-` } ${ res.is_stock_checked ? res.unit : ''}</div>
                <div style="width:10%" class="fw-bold px-2 text-center my-auto${res.is_active ? ' text-success' : ' text-danger'}">${res.is_active ? 'Aktif' : 'Tidak Aktif'}</div>
                
            </div>`
            });

            listData.innerHTML = list;
        }

        
    } catch (error) {
        console.log(error);
    }
    
}

function deleteProduct(id){
    deleteId = id;
}

async function submitDeleteProduct(){
    try {
        let response = await destroyProduct(deleteId);

        let message = `${response.name} Berhasil Dihapus`;
        
        myToast(message, 'success');

        setDefault();
        await showProduct();

    } catch (error) {
        myToast(error.response.data.errors.message, 'danger');
    }
}

async function showSingleProduct(id){
    try {
        let url = `/api/${business}/product/${id}`;
        let res = await getProduct(url);
        
        document.querySelector('#name-detail').innerHTML = res.name;
        if (res.is_active) {
            document.querySelector('#status-detail').innerHTML = 'Aktif';
            document.querySelector('#status-detail').classList.add('text-success','bg-success');
        } else {
            document.querySelector('#status-detail').innerHTML = 'Tidak Aktif';
            document.querySelector('#status-detail').classList.add('text-danger', 'bg-danger');
        }
        document.querySelector('#code-detail').innerHTML = res.code;
        document.querySelector('#unit-detail').innerHTML = res.unit;
        document.querySelector('#category-detail').innerHTML = res.category;
        document.querySelector('#selling-price-detail').innerHTML = formatRupiah(res.selling_price.toString());
        document.querySelector('#unit-price-detail').innerHTML = formatRupiah(res.unit_price.toString());

        res.unit_price 
        ? document.querySelector('#is-stock-checked-detail').setAttribute('checked', 'checked')
        : document.querySelector('#is-stock-checked-detail').removeAttribute('checked');

    } catch (error) {
        console.log(error);
    }
}

function filterButton(){
    selectFilter.value = filterBy;
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
    await showProduct();

}

window.addEventListener('load', async function(){
    setDefault();
    await showProduct();
})