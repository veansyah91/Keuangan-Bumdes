const period = document.querySelector('#period');
const periodTitle = document.querySelector('#period-title');
const periodBefore = document.querySelector('#period-before');

const revenue = document.querySelector('#revenue');
const totalRevenueNow = document.querySelector('#total-revenue-now');
const totalRevenueBefore = document.querySelector('#total-revenue-before');

const cost = document.querySelector('#cost');
const totalCostNow = document.querySelector('#total-cost-now');
const totalCostBefore = document.querySelector('#total-cost-before');

const grossLostProfitNow = document.querySelector('#gross-lost-profit-now');
const grossLostProfitBefore = document.querySelector('#gross-lost-profit-before');

const lostProfitBeforeTaxNow = document.querySelector('#lost-profit-before-tax-now');
const lostProfitBeforeTaxBefore = document.querySelector('#lost-profit-before-tax-before');

const lostProfitAfterTaxNow = document.querySelector('#lost-profit-after-tax-now');
const lostProfitAfterTaxBefore = document.querySelector('#lost-profit-after-tax-before');

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

const printButton = document.querySelector('#print-button');

let revenues = [];
let costs = [];

let totalRevenuesNow = 0;
let totalRevenuesBefore = 0;

let totalCostsNow = 0;
let totalCostsBefore = 0;

let cogsNow = 0;
let cogsBefore = 0;

let totalTaxNow = 0;
let totalTaxBefore = 0;

let year = 0;

const setDefaultValue = () => {
    revenues = [];
    costs = [];

    totalRevenuesNow = 0;
    totalRevenuesBefore = 0;

    totalCostsNow = 0;
    totalCostsBefore = 0;

    cogsNow = 0;
    cogsBefore = 0;

    totalTaxNow = 0;
    totalTaxBefore = 0;
}


const showLostProfit = (component, totalComponentNow, totalComponentBefore, lostProfits, totalLostProfitsNow, totalLostProfitsBefore) => {
    let list = '';
    if (lostProfits.length > 0) {
        
        lostProfits.map(lostProfit => {
            list += `
            <tr>
                <td class="w-40">
                    ${lostProfit.name}
                </td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${formatRupiah(lostProfit.totalNow.toString())} 
                        </div>
                    </div>  
                </td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${formatRupiah(lostProfit.totalBefore.toString())} 
                        </div>
                    </div> 
                </td>
            </tr>
            ` 
        });
    }
    component.innerHTML = list;

    totalComponentNow.innerHTML = `${formatRupiah(totalLostProfitsNow.toString())}`;
    
    totalComponentBefore.innerHTML = `${formatRupiah(totalLostProfitsBefore.toString())}`;
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
    revenue.innerHTML = loading();
    totalRevenueNow.innerHTML = loading();
    totalRevenueBefore.innerHTML = loading();

    cost.innerHTML = loading();
    totalCostNow.innerHTML = loading();
    totalCostBefore.innerHTML = loading();

    grossLostProfitNow.innerHTML = loading();

    grossLostProfitBefore.innerHTML = loading();

    lostProfitBeforeTaxNow.innerHTML = loading();

    lostProfitBeforeTaxBefore.innerHTML = loading();


    lostProfitAfterTaxNow.innerHTML = loading();
    lostProfitAfterTaxBefore.innerHTML = loading();
}

const showReport = async () => {
    loadingComponent()
    try {
        let url = `/api/report/lost-profit-year?year=${year}&end_year=${year}`;

        let res = await getData(url);
        
        period.innerHTML = res.period;
        periodTitle.innerHTML = res.period;
        yearLabel.innerHTML = res.period;
        periodBefore.innerHTML = res.period - 1;

        revenues = [];
        costs = [];

        totalRevenuesNow = 0;
        totalRevenuesBefore = 0;

        totalCostsNow = 0;
        totalCostsBefore = 0;

        cogsNow = 0;
        cogsBefore = 0;

        totalTaxNow = 0;
        totalTaxBefore = 0;
        
        res.lost_profit.map(data => {
            if (parseInt(data.code) < 5000000) {
                if (data.total_now - data.total_before != 0) {
                    revenues = [...revenues, {
                        name: `${data.name}`,
                        totalNow: data.total_now * -1,
                        totalBefore: data.total_before * -1
                    }];
                    totalRevenuesNow += data.total_now * -1;
                    totalRevenuesBefore += data.total_before * -1;
                }
                
            }else{
                if (data.total_now - data.total_before != 0) {
                    costs = [...costs, {
                        name: `${data.name}`,
                        totalNow: data.total_now * -1,
                        totalBefore: data.total_before * -1
                    }];
                    totalCostsNow += data.total_now ;
                    totalCostsBefore += data.total_before ;
                }
                

                if (data.name == 'Harga Pokok Penjualan' || data.name == 'Beban Atas Pendapatan' || data.name == 'Potongan Pembelian Barang Dagang' || data.name == 'Retur Pembelian Barang Dagang') {
                    cogsNow += data.total_now;
                    cogsBefore += data.total_before;
                }

                if (data.name == 'Beban Pajak') {
                    totalTaxNow += data.total_now;
                    totalTaxBefore += data.total_before;
                }
            }
        });

        showLostProfit(revenue, totalRevenueNow, totalRevenueBefore, revenues, totalRevenuesNow, totalRevenuesBefore);

        showLostProfit(cost, totalCostNow, totalCostBefore, costs, totalCostsNow, totalCostsBefore);

        grossLostProfitNow.innerHTML = `${totalRevenuesNow - cogsNow < 0 ? `(${formatRupiah((totalRevenuesNow - cogsNow).toString())})` : formatRupiah((totalRevenuesNow - cogsNow).toString())}`;

        grossLostProfitBefore.innerHTML = `${totalRevenuesBefore - cogsBefore < 0 ? `(${formatRupiah((totalRevenuesBefore - cogsBefore).toString())})` : formatRupiah((totalRevenuesBefore - cogsBefore).toString())}`;

        lostProfitBeforeTaxNow.innerHTML = `${totalRevenuesNow - totalCostsNow + totalTaxNow < 0 ? `(${formatRupiah((totalRevenuesNow - totalCostsNow + totalTaxNow).toString())})` : formatRupiah((totalRevenuesNow - totalCostsNow + totalTaxNow).toString())}`;

        lostProfitBeforeTaxBefore.innerHTML = `${totalRevenuesBefore - totalCostsBefore + totalTaxBefore < 0 ? `(${formatRupiah((totalRevenuesBefore - totalCostsBefore + totalTaxBefore).toString())})` : formatRupiah((totalRevenuesBefore - totalCostsBefore + totalTaxBefore).toString())}`;


        lostProfitAfterTaxNow.innerHTML = `${totalRevenuesNow - totalCostsNow < 0 ? `(${formatRupiah((totalRevenuesNow - totalCostsNow).toString())})` : formatRupiah((totalRevenuesNow - totalCostsNow).toString())}`;
        lostProfitAfterTaxBefore.innerHTML = `${totalRevenuesBefore - totalCostsBefore < 0 ? `(${formatRupiah((totalRevenuesBefore - totalCostsBefore).toString())})` : formatRupiah((totalRevenuesBefore - totalCostsBefore).toString())}`;

    } catch (error) {
        console.log(error);
    }
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

function submitPosition(event){
    event.preventDefault();
    let userPositionInput = document.querySelector('#user-position-input');
    
    localStorage.setItem('position', userPositionInput.value);
    cancelEditPosition();
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

function submitLocation(event){
    event.preventDefault();
    localStorage.setItem('locationPrint', document.querySelector('#position-print-input').value);
    cancelEditLocation();
    localStorageCheck();
    printButtonVisibility();
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


window.addEventListener('load',async function(){
    let currentUrl = new URL(window.location.href);

    let splitArray = currentUrl.search.split('&');
    let splitArrayToGetYear = splitArray[0].split('=');

    year = parseInt(splitArrayToGetYear[1]);
    
    localStorageCheck();
    setDefaultValue();
    printButtonVisibility();
    await showReport();
})