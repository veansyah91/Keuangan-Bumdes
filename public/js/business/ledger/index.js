const searchInputDropdown = document.querySelector('#search-input-dropdown');
const accountList = document.getElementById('account-list');

const selectFilter = document.querySelector('#select-filter');
const dateRange = document.querySelector('#date-range');
const startFilter = document.querySelector('#start-filter');
const endFilter = document.querySelector('#end-filter');

const printButton = document.querySelector('#print-button');

const countData = document.querySelector('#count-data');
const prevBtn = document.querySelector('#prev-page');
const nextBtn = document.querySelector('#next-page');

const listData = document.querySelector('#list-data');
const totalDebit = document.querySelector('#total-debit');
const totalCredit = document.querySelector('#total-credit');
const mutation = document.querySelector('#mutation');
const openingBalance = document.querySelector('#opening-balance');
const endingBalance = document.querySelector('#ending-balance');

let accountName = '';

let page = 1;
let date_from = '';
let date_to = '';
let thisWeek = '';
let thisMonth = 1;
let thisYear = '';
let account_id = null;

let filterBy = '';

const setDefaultValue = () => {
    accountName = '';
    page = 1;
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = 1;
    thisYear = '';
    account_id = null;

    filterBy = 'this month';

}

const setDefaultValueComponent = () => {
    searchInputDropdown.value = accountName;

    dateRange.classList.add('d-none');
    printButton.classList.add('disabled');
    printButton.setAttribute('disabled', 'disabled');

}

const changeAccountDropdown = (value) => {
    value.value = accountName;
}

async function prevButton(){
    page--;
    await showLedger();
}

async function nextButton(){
    page++;
    await showLedger();
}

const showLedger = async () => {
    try {
        let response = await getLedger('',page, date_from, date_to, thisWeek, thisMonth, thisYear, account_id);

        countData.innerHTML = `${response.ledgers.data.length ? (page * response.ledgers.per_page)-(response.ledgers.per_page-1) : 0}-${response.ledgers.data.length + (page * response.ledgers.per_page)-response.ledgers.per_page} dari ${response.ledgers.total}`;

        if (page == 1) {
            prevBtn.classList.add('disabled');
            prevBtn.setAttribute('disabled','disabled');
        }else{
            prevBtn.classList.remove('disabled');
            prevBtn.removeAttribute('disabled');
        }

        if (page == response.ledgers.last_page) {
            nextBtn.classList.add('disabled');
            nextBtn.setAttribute('disabled','disabled');
        }else{
            nextBtn.classList.remove('disabled');
            nextBtn.removeAttribute('disabled');
        }
        let list = '';

        if (!account_id) {
            listData.innerHTML = `
            <div class="d-md-flex d-none justify-content-between border-bottom py-2 px-1 fst-italic">
                <div style="width:100%;font-size:14px" class="px-2 my-auto text-center">Silakan Pilih Akun</div>
            </div>`
            return;
        }

        if (response.ledgers.data.length < 1) {
            listData.innerHTML = `
            <div class="d-md-flex d-none justify-content-between border-bottom py-2 px-1 fst-italic">
                <div style="width:100%" class="px-2 my-auto text-center">Tidak Ada Data</div>
            </div>`
            
        } else {

            response.ledgers.data.map(data => {
                list += `<div class="d-md-flex d-none justify-content-between border-bottom py-2 px-1 content-data">
                        
                    <div style="width:15%" class="px-2 my-auto">${dateReadable(data.date)}</div>
                    <div style="width:15%" class="px-2 my-auto">${data.no_ref}</div>
                    <div style="width:20%" class="px-2 my-auto">${data.description}</div>
                    <div style="width:20%" class="px-2 text-end my-auto">${formatRupiah(data.debit.toString())}</div>
                    <div style="width:20%" class="px-2 text-end my-auto">${formatRupiah(data.credit.toString())}</div>
                    
                </div>`
            });
        }

        listData.innerHTML = list;
        totalCredit.innerHTML = `Rp.${formatRupiah(response.total_credit.toString())}`;
        totalDebit.innerHTML = `Rp.${formatRupiah(response.total_debit.toString())}`;
        mutation.innerHTML = `Rp.${response.total_debit - response.total_credit < 0 ? '-' : ''}${formatRupiah((response.total_debit - response.total_credit).toString())}`;

        let startingBalanceValue = response.amountLedger - (response.total_debit - response.total_credit);
        openingBalance.innerHTML = `Rp.${startingBalanceValue < 0 ? '-' : ''}${formatRupiah(startingBalanceValue.toString())}`;
        endingBalance.innerHTML = `Rp.${response.amountLedger < 0 ? '-' : ''}${formatRupiah(response.amountLedger.toString())}`;


    } catch (error) {
        console.log(error);
    }
}

const selectAccount = async (value) => {
    accountName = value.dataset.name;
    searchInputDropdown.value = accountName;
    account_id = value.dataset.id;

    printButton.classList.remove('disabled');
    printButton.removeAttribute('disabled');

    document.querySelector('#print-button').setAttribute('href', `/${business}/ledger/print?account_id=${account_id}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`);

    await showLedger();
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
    accountList.innerHTML = `
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
        accountList.innerHTML = list ? list : empty;
    }, 100);

    accountList.classList.remove('d-none');

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
    await showLedger();

}

function goToPrintLedgers(){
    let url = `${business}/ledger/print?account_id=${account_id}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`;
    
    window.open(url);
}

window.addEventListener('load', async function(){
    setDefaultValue();
    setDefaultValueComponent();
    await showLedger();
});