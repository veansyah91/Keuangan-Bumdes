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
const contactNameDetail = document.querySelector('#contact-name-detail');
const contactAddressDetail = document.querySelector('#contact-address-detail');
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
    await showAccountReceivable();
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
    await showAccountReceivable();
}

async function nextButton(){
    page++;
    await showAccountReceivable();
}

async function showAccountReceivable(){
    try {
        let response = await getAccountReceivable(search,page, date_from, date_to, thisWeek, thisMonth, thisYear);
        
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

        let total = 0;
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
            total += res.value;
            let sisa = parseInt(res.account_receivables_sum_debit) - parseInt(res.account_receivables_sum_credit);
            list += `
            <div class="d-flex d-md-none justify-content-between border-top border-bottom py-2">
                <div style="width:10%" class="my-auto">
                    <div class="btn-group dropstart">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${index}">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleAccountReceivable(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/${res.business_id}/account-receivable/${res.id}/edit" >
                                    <div class="row align-items-center justify-conter-start text-success">
                                        <div class="col-2"><i class="bi bi-pencil-square"></i></div>
                                        <div class="col-3">Ubah</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="deleteAccountReceivable(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-danger">
                                        <div class="col-2"><i class="bi bi-trash"></i></div>
                                        <div class="col-3">Hapus</div>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div style="width:90%">
                    <div class="font-bold">
                        ${res.name}
                    </div>
                    <div class="">
                        <small>Kode: ${res.no_ref} </small>
                    </div>
                </div>
                
            </div>

            <div class="d-md-flex d-none justify-content-between border-top border-bottom py-2 px-1 content-data">
                <div style="width:5%" class="px-2 my-auto">
                    <div class="dropdown">
                        <button class="btn btn-sm" type="button" id="dropdownMenuButton${index}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${index}">
                            <li>
                                <button class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#showDetailModal" onclick="showSingleAccountReceivable(${res.id})" >
                                    <div class="row align-items-center justify-conter-start text-info">
                                        <div class="col-2"><i class="bi bi-search"></i></div>
                                        <div class="col-3">Detail</div>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div style="width:20%" class="px-2 my-auto">${res.name}</div>
                <div style="width:20%" class="px-2 my-auto text-end">${formatRupiah(res.account_receivables_sum_debit)}</div>
                <div style="width:20%" class="px-2 my-auto text-end">${formatRupiah(res.account_receivables_sum_credit)}</div>
                <div style="width:20%" class="px-2 my-auto text-end">${formatRupiah(sisa.toString())}</div>
                
            </div>`
            });

            listData.innerHTML = list;
            // document.querySelector('#sum-data').innerHTML = `Rp. ${formatRupiah(total.toString())}`
        }

        
    } catch (error) {
        console.log(error);
    }
    
}

function deleteAccountReceivable(id){
    deleteId = id;
}

async function submitDeleteAccountReceivable(){
    try {
        let response = await destroyAccountReceivable(deleteId);
        
        let message = `Data Berhasil Dihapus`;
        
        myToast(message, 'success');

        setDefault();
        await showAccountReceivable();

    } catch (error) {
        console.log(error);
    }
}

async function showSingleAccountReceivable(id){
    try {
        let res = await getSingleAccountReceivable(id);

        document.querySelector('#name-detail').innerHTML = res.name;
        document.querySelector('#address-detail').innerHTML = res.address;
        document.querySelector('#phone-detail').innerHTML = res.phone;

        let list = '';
        res.invoices.map(invoice => {
            list += `
            <div class="row justify-content-between mt-5">
                <div class="col-4">
                    <span class="fw-bold">Faktur Penjualan: </span>${invoice.no_ref}
                </div>
                <div class="col-4">
                <span class="fw-bold">Tanggal Penjualan: </span>${dateReadable(invoice.date)}
                </div>
                <div class="col-3 d-flex jusfity-content-between">
                    <div class="col fw-bold">
                        Nilai Penjualan: 
                    </div>
                    <div class="col text-end">
                        Rp. ${formatRupiah(invoice.value.toString())}  
                    </div>
                </div>
            </div>
            <div class="mx-3">
                <div class="row mt-2 bg-info text-white">
                    <div class="col-2 text-start p-2 border border-white">
                        No Ref
                    </div>
                    <div class="col-2 text-start p-2 border border-white">
                        Tanggal
                    </div>
                    <div class="col-2 text-start p-2 border border-white">
                        Deskripsi
                    </div>
                    <div class="col-2 text-end p-2 border border-white">
                        Debit (IDR)
                    </div>
                    <div class="col-2 text-end p-2 border border-white">
                        Kredit (IDR)
                    </div>
                    <div class="col-2 text-end p-2 border border-white">
                        Sisa (IDR)
                    </div>
                </div>

                <div class="content-detail row">
                    
                </div>

                <div class="row mt-2 bg-info text-white">
                    <div class="col-6 text-start p-2 border border-white">
                        Total
                    </div>
                    <div class="col-2 text-end p-2 border border-white total-debit-detail">
                        Rp. 0
                    </div>
                    <div class="col-2 text-end p-2 border border-white total-credit-detail">
                        Rp. 0
                    </div>
                    <div class="col-2 text-end p-2 border border-white total-balance-detail">
                        Rp. 0
                    </div>
                </div>
            </div>
            `
        })

        document.querySelector('#invoices').innerHTML = list;

        const detailLists = Array.from(document.getElementsByClassName('content-detail'));
        const totalDebits = Array.from(document.getElementsByClassName('total-debit-detail'));
        const totalCredits = Array.from(document.getElementsByClassName('total-credit-detail'));
        const totalBalances = Array.from(document.getElementsByClassName('total-balance-detail'));

        detailLists.map((detail, index) => {
            let invoices = res.invoices[index];
            let list = '';

            let total = 0;
            let debits = 0;
            let credits = 0;
            if (invoices.account_receivables.length > 0) {
                invoices.account_receivables.map(accountReceivable => {
                    total += accountReceivable.debit - accountReceivable.credit;
                    debits += accountReceivable.debit;
                    credits += accountReceivable.credit;
                    list += `
                    <div class="col-2 text-start p-2 border border-white">
                        ${accountReceivable.no_ref}
                    </div>
                    <div class="col-2 text-start p-2 border border-white">
                        ${dateReadable(accountReceivable.date)}
                    </div>
                    <div class="col-2 text-start p-2 border border-white">
                        ${accountReceivable.description ? accountReceivable.description : '-'}
                    </div>
                    <div class="col-2 text-end p-2 border border-white">
                        ${formatRupiah(accountReceivable.debit.toString())}
                    </div>
                    <div class="col-2 text-end p-2 border border-white">
                        ${formatRupiah(accountReceivable.credit.toString())}
                    </div>
                    <div class="col-2 text-end p-2 border border-white">
                        ${formatRupiah(total.toString())}
                    </div>
                    `
                })
            } 
            
            detail.innerHTML = list;
            totalDebits[index].innerHTML = formatRupiah(debits.toString());
            totalCredits[index].innerHTML = formatRupiah(credits.toString());
            totalBalances[index].innerHTML = formatRupiah((debits - credits).toString());
        })

        // document.querySelector('#btn-submit-print-single').setAttribute('href',`/${res.business_id}/account-receivable/${res.id}/print-detail`);

    } catch (error) {
        console.log(error);
    }
}

function goToPrintAccountReceivables(){
    window.open(`/${business}/account-receivable/print?search=${search}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`)
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
    await showAccountReceivable();

}


window.addEventListener('load', async function(){
    setDefault();
    setDefaultComponentValue();
    await showAccountReceivable();
})