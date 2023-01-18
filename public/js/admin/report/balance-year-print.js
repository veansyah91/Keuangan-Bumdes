const period = document.querySelector('#period');
const periodTitle = document.querySelector('#period-title');
const periodBefore = document.querySelector('#period-before');

let userPrintPage = null;
let position = null;
let locationPrint = null;

const userPositionLabel = document.querySelector('#user-position-label');
const userPositionForm = document.querySelector('#user-position-form');
const userNameLabel = document.querySelector('#user-name-label');
const userNameForm = document.querySelector('#user-name-form');
const locationPrintLabel = document.querySelector('#location-print-label');
const locationPrintForm = document.querySelector('#location-print-form');
const addressDate = document.querySelector('#address-date');

const yearLabel = document.querySelector('#year-label');

const selectYear = document.querySelector('#select-year');
const printButton = document.querySelector('#print-button');

const currentAsset = document.querySelector('#current-asset');
const totalCurrentAssetNow = document.querySelector('#total-current-asset-now');
const totalCurrentAssetBefore = document.querySelector('#total-current-asset-before');

const nonCurrentAsset = document.querySelector('#non-current-asset');
const totalNonCurrentAssetNow = document.querySelector('#total-non-current-asset-now');
const totalNonCurrentAssetBefore = document.querySelector('#total-non-current-asset-before');

const totalAssetNow = document.querySelector('#total-asset-now');
const totalAssetBefore = document.querySelector('#total-asset-before');

const shortTermLiability = document.querySelector('#short-term-liability');
const totalShortTermLiabilityNow = document.querySelector('#total-short-term-liability-now');
const totalShortTermLiabilityBefore = document.querySelector('#total-short-term-liability-before');

const longTermLiability = document.querySelector('#long-term-liability');
const totalLongTermLiabilityNow = document.querySelector('#total-long-term-liability-now');
const totalLongTermLiabilityBefore = document.querySelector('#total-long-term-liability-before');

const totalLiabilityNow = document.querySelector('#total-liability-now');
const totalLiabilityBefore = document.querySelector('#total-liability-before');

const equity = document.querySelector('#equity');
const totalEquityNow = document.querySelector('#total-equity-now');
const totalEquityBefore = document.querySelector('#total-equity-before');

const totalLiabilityEquityNow = document.querySelector('#total-liability-equity-now');
const totalLiabilityEquityBefore = document.querySelector('#total-liability-equity-before');

let year = 0;

let currentAssets = [];
let nonCurrentAssets = [];
let shortTermLiabilities = [];
let longTermLiabilities = [];
let equities= [];

let totalCurrentAssetsNow = 0;
let totalCurrentAssetsBefore = 0;
let totalNonCurrentAssetsNow = 0;
let totalNonCurrentAssetsBefore = 0;
let totalAssetsNow = 0;
let totalAssetsBefore = 0;

let totalLongTermLiabilitiesNow = 0;
let totalLongTermLiabilitiesBefore = 0;
let totalShortTermLiabilitiesNow = 0;
let totalShortTermLiabilitiesBefore = 0;
let totalEquitiesNow = 0;
let totalEquitiesBefore = 0;

let totalLiabilitiesEquitiesNow = 0;
let totalLiabilitiesEquitiesBefore = 0;

const setDefaultValue = () => {

    currentAssets = [];
    nonCurrentAssets = [];
    shortTermLiabilities = [];
    longTermLiabilities = [];
    equities= [];

    totalCurrentAssetsNow = 0;
    totalCurrentAssetsBefore = 0;
    totalNonCurrentAssetsNow = 0;
    totalNonCurrentAssetsBefore = 0;
    totalAssetsNow = 0;
    totalAssetsBefore = 0;

    totalLongTermLiabilitiesNow = 0;
    totalLongTermLiabilitiesBefore = 0;
    totalShortTermLiabilitiesNow = 0;
    totalShortTermLiabilitiesBefore = 0;
    totalEquitiesNow = 0;
    totalEquitiesBefore = 0;

    totalLiabilitiesEquitiesNow = 0;
    totalLiabilitiesEquitiesBefore = 0;
}

const showBalance = (component, totalComponentNow, totalComponentBefore, balances, totalBalanceNow, totalBalanceBefore) => {
    let list = '';
    if (balances.length > 0) {
        
        balances.map(balance => {
            list += balance.totalNow + balance.totalBefore != 0 ? `
            <tr>
                <td class="w-40">
                    ${balance.name}
                </td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${balance.totalNow < 0 ? `(${formatRupiah(balance.totalNow.toString())})` : formatRupiah(balance.totalNow.toString())} 
                        </div>
                    </div>  
                </td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${balance.totalBefore < 0 ? `(${formatRupiah(balance.totalBefore.toString())})` : formatRupiah(balance.totalBefore.toString())} 
                        </div>
                    </div> 
                </td>
            </tr>
            ` : ''
        });
    }
    component.innerHTML = list;

    totalComponentNow.innerHTML = `${formatRupiah(totalBalanceNow.toString())}`;
    totalComponentBefore.innerHTML = `${formatRupiah(totalBalanceBefore.toString())}`;

}

const goToPrintCashflow = () => {
    let url = `/report/print-balance?date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`;
    
    window.open(url);
}

function loading(){
    return `
        <tr>
            <td colspan="3" class="text-center">
                <div class="spinner-grow spinner-grow-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow spinner-grow-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow spinner-grow-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow spinner-grow-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `
}

function loadingComponent(){
    currentAsset.innerHTML = loading();
    totalCurrentAssetNow.innerHTML = loading();
    totalCurrentAssetBefore.innerHTML = loading();

    nonCurrentAsset.innerHTML = loading();
    totalNonCurrentAssetNow.innerHTML = loading();
    totalNonCurrentAssetBefore.innerHTML = loading();

    shortTermLiability.innerHTML = loading();
    totalShortTermLiabilityNow.innerHTML = loading();
    totalShortTermLiabilityBefore.innerHTML = loading();

    longTermLiability.innerHTML = loading();
    totalLongTermLiabilityNow.innerHTML = loading();
    totalLongTermLiabilityBefore.innerHTML = loading();

    equity.innerHTML = loading();
    totalEquityNow.innerHTML = loading();
    totalEquityBefore.innerHTML = loading();

    totalAssetNow.innerHTML = loading();
    totalAssetBefore.innerHTML = loading();

    totalLiabilityNow.innerHTML = loading();
    totalLiabilityBefore.innerHTML = loading();

    totalLiabilityEquityNow.innerHTML = loading();
    totalLiabilityEquityBefore.innerHTML = loading();
}

const showReport = async () => {
    loadingComponent()
    try {
        let url = `/api/report/balance-year?year=${year}&end_year=${year}`;

        let res = await getData(url);

        period.innerHTML = res.period;
        periodTitle.innerHTML = res.period;
        yearLabel.innerHTML = res.period;
        periodBefore.innerHTML = res.period - 1;

        totalCurrentAssets = 0;
        totalNonCurrentAssets = 0;
        totalAssets = 0;

        totalShortTemLiabilities = 0;
        totalEquities = 0;
        totalLiabilitiesEquities = 0;

        res.balance.map(balance => {
            if (parseInt(balance.code) < 2000000) {
                if (parseInt(balance.code) < 1600000) {
                    currentAssets = [...currentAssets, {
                        name : `${balance.name}`,
                        totalNow : parseInt(balance.total_now),
                        totalBefore : parseInt(balance.total_before),
                    }];
                    totalCurrentAssetsNow += parseInt(balance.total_now);
                    totalCurrentAssetsBefore += parseInt(balance.total_before);
                } else {
                    nonCurrentAssets = [...nonCurrentAssets, {
                        name : `${balance.name}`,
                        totalNow : parseInt(balance.total_now),
                        totalBefore : parseInt(balance.total_before),
                    }];
                    totalNonCurrentAssetsNow += parseInt(balance.total_now);
                    totalNonCurrentAssetsBefore += parseInt(balance.total_before);
                }
            } else if(parseInt(balance.code) < 3000000) {
                if (parseInt(balance.code) < 2600000) {
                    shortTermLiabilities = [...shortTermLiabilities, {
                        name : `${balance.name}`,
                        totalNow : parseInt(balance.total_now) * -1,
                        totalBefore : parseInt(balance.total_before) * -1,
                    }];
                    totalShortTermLiabilitiesNow += parseInt(balance.total_now) * -1;
                    totalShortTermLiabilitiesBefore += parseInt(balance.total_before) * -1;
                } else {
                    longTermLiabilities = [...longTermLiabilities, {
                        name : `${balance.name}`,
                        totalNow : parseInt(balance.total_now) * -1,
                        totalBefore : parseInt(balance.total_before) * -1,
                    }];
                    totalLongTermLiabilitiesNow += parseInt(balance.total_now) * -1;
                    totalLongTermLiabilitiesBefore += parseInt(balance.total_before) * -1;
                }
                
            } else {
                equities = [...equities, {
                    name : `${balance.name}`,
                    totalNow : parseInt(balance.total_now) * -1,
                    totalBefore : parseInt(balance.total_before) * -1,
                }];
                totalEquitiesNow += parseInt(balance.total_now) * -1;
                totalEquitiesBefore += parseInt(balance.total_before) * -1;
            }
        })

        showBalance(currentAsset, totalCurrentAssetNow, totalCurrentAssetBefore, currentAssets, totalCurrentAssetsNow, totalCurrentAssetsBefore);

        showBalance(nonCurrentAsset, totalNonCurrentAssetNow, totalNonCurrentAssetBefore, nonCurrentAssets, totalNonCurrentAssetsNow, totalNonCurrentAssetsBefore);

        showBalance(shortTermLiability, totalShortTermLiabilityNow, totalShortTermLiabilityBefore, shortTermLiabilities, totalShortTermLiabilitiesNow, totalShortTermLiabilitiesBefore);

        showBalance(longTermLiability, totalLongTermLiabilityNow, totalLongTermLiabilityBefore, longTermLiabilities, totalLongTermLiabilitiesNow, totalLongTermLiabilitiesBefore);

        showBalance(equity, totalEquityNow, totalEquityBefore, equities, totalEquitiesNow, totalEquitiesBefore);

        totalAssetNow.innerHTML = `${formatRupiah((totalCurrentAssetsNow + totalNonCurrentAssetsNow).toString())}`;
        totalAssetBefore.innerHTML = `${formatRupiah((totalCurrentAssetsBefore + totalNonCurrentAssetsBefore).toString())}`;

        totalLiabilityNow.innerHTML = `${formatRupiah((totalLongTermLiabilitiesNow + totalShortTermLiabilitiesNow).toString())}`;
        totalLiabilityBefore.innerHTML = `${formatRupiah((totalLongTermLiabilitiesBefore + totalShortTermLiabilitiesBefore).toString())}`;

        totalLiabilityEquityNow.innerHTML = `${formatRupiah((totalLongTermLiabilitiesNow + totalShortTermLiabilitiesNow + totalEquitiesNow).toString())}`;
        totalLiabilityEquityBefore.innerHTML = `${formatRupiah((totalLongTermLiabilitiesBefore + totalShortTermLiabilitiesBefore + totalEquitiesBefore).toString())}`;


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
    currentAssets = [];
    nonCurrentAssets = [];
    liabilities = [];
    equities= [];

    totalCurrentAssets = 0;
    totalNonCurrentAssets = 0;

    totalLiabilities = 0;
    totalEquities = 0;
    setQuery();
    await showReport();

}

function localStorageCheck(){
    userPrintPage = localStorage.getItem('userPrintPage');
    position = localStorage.getItem('position');
    locationPrint = localStorage.getItem('locationPrint');

    if (position) {
        userPositionLabel.innerHTML = `${position} <button class="btn btn-sm d-print-none" onclick="changePosition()"><i class="bi bi-pencil"></i></button>`;
        userPositionLabel.classList.remove('text-danger', 'fst-italic');
    } else {
        userPositionLabel.innerHTML = `(belum ada jabatan) <button class="btn btn-sm d-print-none" onclick="changePosition()"><i class="bi bi-pencil"></i></button>`;
        userPositionLabel.classList.add('text-danger', 'fst-italic');
    }

    if (userPrintPage) {
        userNameLabel.innerHTML = `${userPrintPage} <button class="btn btn-sm d-print-none" onclick="changeName()"><i class="bi bi-pencil"></i></button>`;
        userNameLabel.classList.remove('text-danger', 'fst-italic');
    } else {
        userNameLabel.innerHTML = `(belum ada nama) <button class="btn btn-sm d-print-none" onclick="changeName()"><i class="bi bi-pencil"></i></button>`;
        userNameLabel.classList.add('text-danger', 'fst-italic');
    }

    if (locationPrint) {
        locationPrintLabel.innerHTML = `<button class="btn btn-sm d-print-none" onclick="changeLocationPrint()"><i class="bi bi-pencil"></i></button>${locationPrint}`;
        locationPrintLabel.classList.remove('text-danger', 'fst-italic');
    } else {
        locationPrintLabel.innerHTML = `<button class="btn btn-sm d-print-none" onclick="changeLocationPrint()"><i class="bi bi-pencil"></i></button>(belum ada lokasi)`;
        locationPrintLabel.classList.add('text-danger', 'fst-italic');
    }
}

function submitLocation(event){
    event.preventDefault();
    localStorage.setItem('locationPrint', document.querySelector('#position-print-input').value);
    cancelEditLocation();
    localStorageCheck();
    printButtonVisibility();
}

function submitName(event){
    event.preventDefault();
    let userNameInput = document.querySelector('#user-name-input');
    localStorage.setItem('userPrintPage', userNameInput.value);
    cancelEditName();
    localStorageCheck();
    printButtonVisibility();
}

function submitPosition(event){
    event.preventDefault();
    let userPositionInput = document.querySelector('#user-position-input');
    
    localStorage.setItem('position', userPositionInput.value);
    cancelEditPosition();
    localStorageCheck();
    printButtonVisibility();
}

const printButtonVisibility = () => {
    const printButton = document.querySelector('#print-button');

    if (userPrintPage && position && locationPrint) {
        printButton.classList.remove('d-none');
    } else {
        printButton.classList.add('d-none');
    }
}

function changePosition(){
    userPositionLabel.classList.add('d-none');
    userPositionForm.classList.remove('d-none');
    let userPositionInput = document.querySelector('#user-position-input');
    userPositionInput.value = localStorage.getItem('position');
}

function changeName(){
    userNameLabel.classList.add('d-none');
    userNameForm.classList.remove('d-none');
    let userNameInput = document.querySelector('#user-name-input');
    userNameInput.value = localStorage.getItem('userPrintPage');
}

function changeLocationPrint(){
    addressDate.classList.add('d-none');
    document.querySelector('#position-print-form').classList.remove('d-none');
    document.querySelector('#position-print-input').value =  localStorage.getItem('locationPrint');
}

function cancelEditLocation(){
    addressDate.classList.remove('d-none');
    document.querySelector('#position-print-form').classList.add('d-none');
}

function cancelEditPosition(){
    userPositionLabel.classList.remove('d-none');
    userPositionForm.classList.add('d-none');
}

function cancelEditName(){
    userNameLabel.classList.remove('d-none');
    userNameForm.classList.add('d-none');
}

window.addEventListener('load', async function(){
    let currentUrl = new URL(window.location.href);

    let splitArray = currentUrl.search.split('&');
    let splitArrayToGetYear = splitArray[0].split('=');

    year = parseInt(splitArrayToGetYear[1]);

    localStorageCheck();
    setDefaultValue();
    printButtonVisibility();
    await showReport()
})