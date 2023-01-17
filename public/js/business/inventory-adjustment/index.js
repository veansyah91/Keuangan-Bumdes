const searchInput = document.querySelector('#search-input');

const selectFilter = document.querySelector('#select-filter');
const dateRange = document.querySelector('#date-range');
const startFilter = document.querySelector('#start-filter');
const endFilter = document.querySelector('#end-filter');

const countData = document.querySelector('#count-data');
const prevBtn = document.querySelector('#prev-page');
const nextBtn = document.querySelector('#next-page');

const listData = document.querySelector('#list-data');

const dateDetail = document.querySelector('#date-detail');
const noRefDetail = document.querySelector('#no-ref-detail');
const descriptionDetail = document.querySelector('#description-detail');
const creditAccountDetail = document.querySelector('#credit-account-detail');
const authorDetail = document.querySelector('#author-detail');
const authorDetailLabel = document.querySelector('#author-detail-label');
const contentDetail = document.querySelector('#content-detail');
const totalDetail = document.querySelector('#total-detail');
const qtyDetail = document.querySelector('#qty-detail');

let search = '';
let page = 1;
let deleteId = null;
let filterBy = '';
let date_from, date_to, thisWeek, thisMonth, thisYear;

async function searchForm(event){
    event.preventDefault();

    search = searchInput.value;

    await showInventoryAdjustment();
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

function setDefaultComponentValue(){
    dateRange.classList.add('d-none');
}

async function prevButton(){
    page--;
    await showInventoryAdjustment();
}

async function nextButton(){
    page++;
    await showInventoryAdjustment();
}

async function showInventoryAdjustment(){
    try {
        let response = await getInventoryAdjustment(search,page, date_from, date_to, thisWeek, thisMonth, thisYear);

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
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleInventoryAdjustment(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/${res.business_id}/inventory-adjustment/${res.id}/edit" >
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteInventoryAdjustment(${res.id})" >
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
                        ${res.description}
                    </div>
                    <div class="">
                        <small>Kode: ${res.no_ref}  </small>
                    </div>
                </div>
                <div style="width:40%" class="my-auto text-end">
                    
                </div>
                
            </div>

            <div class="d-md-flex d-none justify-content-between border-top border-bottom py-2 px-1 content-data">
                <div style="width:10%" class="px-2 my-auto">
                    <div class="dropdown">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${index}">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleInventoryAdjustment(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                            ${res.source ? '' : `<li>
                            <a class="dropdown-item" href="/${res.business_id}/inventory-adjustment/${res.id}/edit">
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteInventoryAdjustment(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-danger">
                                        <div class="col-2"><i class="bi bi-trash"></i></div>
                                        <div class="col-3">Hapus</div>
                                    </div>
                                </button>
                            </li>`}
                        </ul>
                    </div>
                </div>
                <div style="width:20%" class="px-2 my-auto">${dateReadable(res.date)}</div>
                <div style="width:20%" class="px-2 my-auto">${res.no_ref}</div>
                <div style="width:40%" class="px-2 my-auto">${res.description}</div>
                
            </div>`
            });

            listData.innerHTML = list;
        }

        
    } catch (error) {
        console.log(error);
    }
    
}

function deleteInventoryAdjustment(id){
    deleteId = id;
}

async function submitDeleteInventoryAdjustment(){
    try {
        let response = await destroyInventoryAdjustment(deleteId);
        
        let message = `Data Berhasil Dihapus`;
        
        myToast(message, 'success');

        setDefault();
        await showInventoryAdjustment();

    } catch (error) {
        console.log(error);
    }
}

async function showSingleInventoryAdjustment(id){
    try {
        let res = await getSingleInventoryAdjustment(id);

        dateDetail.innerHTML = `: ${dateReadable(res.date)}`;
        noRefDetail.innerHTML = `: ${res.no_ref}`;
        descriptionDetail.innerHTML = `: ${res.description}`;
        creditAccountDetail.innerHTML = `: ${res.credit.name}`
        authorDetailLabel.innerHTML = `${res.is_updated ? 'Diubah Oleh' : 'Diinput Oleh'}`;

        authorDetail.innerHTML = `: ${res.author} (${res.created_at_for_human})`;

        document.querySelector('#btn-submit-print-single').dataset.id = id;

        let listDetail = '';
        let qtyTotal = 0;
        let grandTotal = 0;
        res.listInput.map(list => {
            qtyTotal += list.qty;
            grandTotal += list.total;
            listDetail += `
            <div class="row mt-2 text-gray">
                <div class="col-2 text-start p-2 border border-white">
                    ${list.productCode}
                </div>
                <div class="col-3 text-start p-2 border border-white">
                    ${list.productName}
                </div>
                <div class="col-2 text-end p-2 border border-white">
                    ${formatRupiah(list.qty.toString())}
                </div>
                <div class="col-2 text-end p-2 border border-white">
                    Rp.${formatRupiah(list.unit_price.toString())}
                </div>
                <div class="col-3 text-end p-2 border border-white">
                    Rp.${formatRupiah(list.total.toString())}
                </div>
            </div>
            `
        })
        
        contentDetail.innerHTML = listDetail;
        qtyDetail.innerHTML = qtyTotal;
        totalDetail.innerHTML = `Rp. ${formatRupiah(grandTotal.toString())}`;

        document.querySelector('#btn-submit-print-single').setAttribute('href',`/${res.business_id}/inventory-adjustment/print-detail/${res.id}`);

    } catch (error) {
        console.log(error);
    }
}

function goToPrintInventoryAdjustmentPerId(value){
    window.open(`/inventory-adjustment/print-detail/${value.dataset.id}`)
}

function goToPrintInventoryAdjustments(){
    window.open(`/${business}/inventory-adjustment/print?search=${search}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`)
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
    await showInventoryAdjustment();

}


window.addEventListener('load', async function(){
    setDefault();
    setDefaultComponentValue();
    await showInventoryAdjustment();
})