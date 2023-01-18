// define component 
const business = document.querySelector('#content').dataset.business;

const period = document.querySelector('#period');
const periodTitle = document.querySelector('#period-title');
const periodBefore = document.querySelector('#period-before');
const operationalActivity = document.querySelector('#operational-activity');
const totalOperationalActivity = document.querySelector('#total-operational-activity');
const investmentActivity = document.querySelector('#investment-activity');
const totalInvestmentActivity = document.querySelector('#total-investment-activity');
const financeActivity = document.querySelector('#finance-activity');
const totalFinanceActivity = document.querySelector('#total-finance-activity');

const increaseCashNow = document.querySelector('#increase-cash-now');
const increaseCashBefore = document.querySelector('#increase-cash-before');
const startCashNow = document.querySelector('#start-cash-now');
const startCashBefore = document.querySelector('#start-cash-before');
const endCashNow = document.querySelector('#end-cash-now');
const endCashBefore = document.querySelector('#end-cash-before');
 
let year = 0;

let listRevenueInvestmentActivity = '';
let listCostInvestmentActivity = '';

let listRevenueFinanceActivity = '';
let listCostFinanceActivity = '';

let listRevenueOparationalActivity = '';
let listCostOparationalActivity = '';

let totalOperationalCashes = 0;
let totalInvesmentCashes = 0;
let totalFinanceCashes = 0;

let url;

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

const setDefaultValue = () => {

    listRevenueInvestmentActivity = '';
    listCostInvestmentActivity = '';

    listRevenueFinanceActivity = '';
    listCostFinanceActivity = '';

    listRevenueOparationalActivity = '';
    listCostOparationalActivity = '';

    totalOperationalCashes = 0;
    totalInvesmentCashes = 0;
    totalFinanceCashes = 0;
}

const listReportDetails = (data) => {
    let totalDebitNow = 0;
    let totalCreditNow = 0;
    let totalDebitBefore = 0;
    let totalCreditBefore = 0;

    let listRevenueActivity = ``;
    let listCostActivity = ``;
    
    data.cashflows.map(cashflow => {
        
        let date = new Date(cashflow.date);
            
            if (date.getFullYear() == year) {
                
                if (cashflow.debit > 0) {
                    totalDebitNow += cashflow.debit;
                } else {
                    totalCreditNow += cashflow.credit;
                }
            }
            else {
                if (cashflow.debit > 0) {
                    totalDebitBefore += cashflow.debit;
                } else {
                    totalCreditBefore += cashflow.credit;
                }
            }

    })

    listRevenueActivity +=  totalDebitNow - totalDebitBefore != 0 ? `
                    <tr>
                        <td class="w-50">${data.sub}</td>
                        <td class="w-25">
                            <div class="d-flex justify-content-between">
                                <div>
                                    Rp. 
                                </div>
                                <div class="text-end">
                                    ${formatRupiah(totalDebitNow.toString())}
                                </div>
                            </div>                                        
                        </td>
                        <td class="w-25">
                        <div class="d-flex justify-content-between">
                                <div>
                                    Rp. 
                                </div>
                                <div class="text-end">
                                    ${formatRupiah(totalDebitBefore.toString())}
                                </div>
                            </div></td>
                    </tr>
                ` : '';

    listCostActivity +=  totalCreditNow - totalCreditBefore != 0 ? `
                <tr>
                    <td class="w-50">${data.sub}</td>
                    <td class="w-25">
                        <div class="d-flex justify-content-between">
                            <div>
                                Rp. 
                            </div>
                            <div class="text-end">
                                ${ totalCreditNow > 0 ? `(${formatRupiah(totalCreditNow.toString())})` : 0 }
                            </div>
                        </div>                                        
                    </td>
                    <td class="w-25">
                    <div class="d-flex justify-content-between">
                            <div>
                                Rp. 
                            </div>
                            <div class="text-end">
                                ${ totalCreditBefore > 0 ? `(${formatRupiah(totalCreditBefore.toString())})` : 0 }
                            </div>
                        </div></td>
                </tr>
                `: '';

    totalNow = totalDebitNow - totalCreditNow
    totalBefore = totalDebitBefore - totalCreditBefore

    return {
        listRevenueActivity, listCostActivity, totalNow, totalBefore
    }
}

const totalPerList = (totalNow, totalBefore, statement) => {
    let totalFinanceNowHTML = totalNow < 0 ? `(${formatRupiah((totalNow).toString())})` : formatRupiah((totalNow).toString());
    let totalFinanceBeforeHTML = totalBefore < 0 ? `(${formatRupiah((totalBefore).toString())})` : formatRupiah((totalBefore).toString());

    return `
    <tr>
        <th class="w-50">Total Kas ${statement}</th>
        <th class="w-25">
            <div class="d-flex justify-content-between">
                <div>
                    Rp. 
                </div>
                <div class="text-end">
                    ${totalFinanceNowHTML}
                </div>
            </div>                                        
        </th>
        <th class="w-25">
            <div class="d-flex justify-content-between">
                <div>
                    Rp. 
                </div>
                <div class="text-end">
                    ${totalFinanceBeforeHTML}
                </div>
            </div>
        </th>
    </tr>
    `
}

const handleSelectYear = async (value) => {
    setDefaultValue();
    year = parseInt(value.value);
    await showReport();
}

const showReport = async () => {
    try {
        url = `/api/${business}/report/cashflow?year=${year}&end_year=${year}`;

        let res = await getData(url);

        let listRevenueOperationalActivity = '';
        let listCostOperationalActivity = '';

        let totalInvestmentNow = 0;
        let totalInvestmentBefore = 0;
        let totalFinanceNow = 0;
        let totalFinanceBefore = 0;
        let totalOperationalNow = 0;
        let totalOperationalBefore = 0;

        let totalBalanceNow = 0;
        let totalBalanceBefore = 0;

        let listRevenueFinanceActivity = '';
        let listCostFinanceActivity = '';

        let headerRevenue = `<tr>
                                <th colspan="3">Penerimaan Kas:</th>
                            </tr>`
        let headerCost = `<tr>
                            <th colspan="3">Pengeluaran Kas:</th>
                        </tr>`
                        
        period.innerHTML = res.period;
        periodTitle.innerHTML = res.period;
        yearLabel.innerHTML = res.period;
        periodBefore.innerHTML = res.period - 1;       

        res.lastYear.map(data => {
            if (parseInt(data.sub_code) > 1500000 && parseInt(data.sub_code) < 2000000) {
                
                const { listRevenueActivity, listCostActivity, totalNow, totalBefore } = listReportDetails(data);

                listRevenueInvestmentActivity += listRevenueActivity ;
                listCostInvestmentActivity += listCostActivity ;

                totalInvestmentNow += totalNow;
                totalInvestmentBefore += totalBefore;

            } else if(parseInt(data.sub_code) > 2599999 && parseInt(data.sub_code) < 4000000){
                const { listRevenueActivity, listCostActivity, totalNow, totalBefore } = listReportDetails(data);

                listRevenueFinanceActivity += listRevenueActivity ;
                listCostFinanceActivity += listCostActivity ;

                totalFinanceNow += totalNow;
                totalFinanceBefore += totalBefore;
            } else {
                const { listRevenueActivity, listCostActivity, totalNow, totalBefore } = listReportDetails(data);

                listRevenueOperationalActivity += listRevenueActivity ;
                listCostOperationalActivity += listCostActivity ;

                totalOperationalNow += totalNow;
                totalOperationalBefore += totalBefore;
            }
        })

        totalBalanceNow = totalOperationalNow + totalInvestmentNow + totalFinanceNow;
        totalBalanceBefore = totalOperationalBefore + totalInvestmentBefore + totalFinanceBefore;

        investmentActivity.innerHTML = headerRevenue + listRevenueInvestmentActivity + headerCost + listCostInvestmentActivity;

        financeActivity.innerHTML = headerRevenue + listRevenueFinanceActivity + headerCost + listCostFinanceActivity;

        operationalActivity.innerHTML = headerRevenue + listRevenueOperationalActivity + headerCost + listCostOperationalActivity;

        totalInvestmentActivity.innerHTML = totalPerList(totalInvestmentNow, totalInvestmentBefore, 'Investasi');

        totalFinanceActivity.innerHTML = totalPerList(totalFinanceNow, totalFinanceBefore, 'Pendanaan')

        totalOperationalActivity.innerHTML = totalPerList(totalOperationalNow, totalOperationalBefore, 'Operasional');
        
        increaseCashNow.innerHTML = `${(totalBalanceNow) < 0 ? '-' : ''}${formatRupiah((totalBalanceNow).toString())}`;
        increaseCashBefore.innerHTML = `${(totalBalanceBefore) < 0 ? '-' : ''}${formatRupiah((totalBalanceBefore).toString())}`;

        endCashNow.innerHTML = `${formatRupiah(res.totalBalance.toString())}`;
        endCashBefore.innerHTML = `${formatRupiah((res.totalBalance - totalBalanceNow).toString())}`;

        startCashNow.innerHTML = `${formatRupiah((res.totalBalance - (totalBalanceNow)).toString())}`;
        startCashBefore.innerHTML = `${formatRupiah((res.totalBalance - totalBalanceNow - totalBalanceBefore).toString())}`;
        
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

window.addEventListener('load', async function(){
    let currentUrl = new URL(window.location.href);

    let splitArray = currentUrl.search.split('&');
    let splitArrayToGetYear = splitArray[0].split('=');

    year = parseInt(splitArrayToGetYear[1]);
    
    localStorageCheck();
    setDefaultValue();
    printButtonVisibility();
    await showReport();
})