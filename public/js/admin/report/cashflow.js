// define component 
const period = document.querySelector('#period');
const operationalActivity = document.querySelector('#operational-activity');
const totalOperationalActivity = document.querySelector('#total-operational-activity');
const investmentActivity = document.querySelector('#investment-activity');
const totalInvestmentActivity = document.querySelector('#total-investment-activity');
const financeActivity = document.querySelector('#finance-activity');
const totalFinanceActivity = document.querySelector('#total-finance-activity');

const increaseCash = document.querySelector('#increase-cash');
const startCash = document.querySelector('#start-cash');
const endCash = document.querySelector('#end-cash');
 
//filter component
const selectFilter = document.querySelector('#select-filter');
const dateRange = document.querySelector('#date-range');
const startFilter = document.querySelector('#start-filter');
const endFilter = document.querySelector('#end-filter');

const printButton = document.querySelector('#print-button');

let date_from = '';
let date_to = '';
let thisWeek = '';
let thisMonth = 1;
let thisYear = '';

let filterBy = '';

let operationalCashes = [];
let investmentCashes = [];
let financeCashes = [];

let totalOperationalCashes = 0;
let totalInvesmentCashes = 0;
let totalFinanceCashes = 0;

const setDefaultValue = () => {
    accountName = '';
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = 1;
    thisYear = '';

    filterBy = 'this month';

    operationalCashes = [];
    invesmentCashes = [];
    financeCashes = [];

    totalOperationalCashes = 0;
    totalInvesmentCashes = 0;
    totalFinanceCashes = 0;
}

const showOperationalCash = () => {
    if (operationalCashes.length > 0) {
        let list = '';
        totalOperationalCashes = 0;
        operationalCashes.map(cash => {
            totalOperationalCashes += cash.totalCash;
            list += cash.totalCash != 0 ? `
            <tr>
                <td class="w-50">${cash.account}</td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${cash.totalCash < 0 ? `(${formatRupiah(cash.totalCash.toString())})` : formatRupiah(cash.totalCash.toString())}
                        </div>
                    </div>                                        
                </td>
                <td w-25></td>
            </tr>` : ''
        })

        operationalActivity.innerHTML = list;
        totalOperationalActivity.innerHTML = totalOperationalCashes < 0 ? `(${formatRupiah(totalOperationalCashes.toString())})` : formatRupiah(totalOperationalCashes.toString())
    }
}

const showInvestmentCash = () => {
    if (investmentCashes.length > 0) {
        let list = '';
        totalInvesmentCashes = 0;

        investmentCashes.map(cash => {
            totalInvesmentCashes += cash.totalCash;
            list += cash.totalCash != 0 ? `
            <tr>
                <td class="w-50">${cash.account}</td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${cash.totalCash < 0 ? `(${formatRupiah(cash.totalCash.toString())})` : formatRupiah(cash.totalCash.toString())}
                        </div>
                    </div>                                        
                </td>
                <td w-25></td>
            </tr>` : ''
        })
        investmentActivity.innerHTML = list ;        
    }
    totalInvestmentActivity.innerHTML = totalInvesmentCashes < 0 ? `(${formatRupiah(totalInvesmentCashes.toString())})` : formatRupiah(totalInvesmentCashes.toString())
    
}

const showFinanceCash = () => {
    if (financeCashes.length > 0) {
        let list = '';
        totalFinanceCashes = 0;

        financeCashes.map(cash => {
            totalFinanceCashes += cash.totalCash;
            list += cash.totalCash != 0 ? `
            <tr>
                <td class="w-50">${cash.account}</td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${cash.totalCash < 0 ? `(${formatRupiah(cash.totalCash.toString())})` : formatRupiah(cash.totalCash.toString())}
                        </div>
                    </div>                                        
                </td>
                <td w-25></td>
            </tr>` : ''
        })
        financeActivity.innerHTML = list;        
    }
    totalFinanceActivity.innerHTML = totalFinanceCashes < 0 ? `(${formatRupiah(totalFinanceCashes.toString())})` : formatRupiah(totalFinanceCashes.toString())
}

const showReport = async () => {
    try {
        let url = `/api/report/cashflow?date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`;

        let res = await getData(url);
        period.innerHTML = res.period;
        res.cashFlows.map(cashflows => {
            if (parseInt(cashflows.code) > 1500000 && parseInt(cashflows.code) < 2000000) {
                let totalDebitPerAccount = 0;
                let totalCreditPerAccount = 0;
                cashflows.cashflows.map(cashflow => {
                    totalDebitPerAccount += cashflow.debit;
                    totalCreditPerAccount += cashflow.credit;
                });
                
                investmentCashes = [...investmentCashes, {
                    account : `${cashflows.name}`,
                    totalCash : totalDebitPerAccount - totalCreditPerAccount
                }]
                
            } else if(parseInt(cashflows.code) > 2599999 && parseInt(cashflows.code) < 4000000){
                let totalDebitPerAccount = 0;
                let totalCreditPerAccount = 0;
                cashflows.cashflows.map(cashflow => {
                    totalDebitPerAccount += cashflow.debit;
                    totalCreditPerAccount += cashflow.credit;
                });

                financeCashes = [...financeCashes, {
                    account : `${cashflows.name}`,
                    totalCash : totalDebitPerAccount - totalCreditPerAccount
                }]
            } else {
                let totalDebitPerAccount = 0;
                let totalCreditPerAccount = 0;
                cashflows.cashflows.map(cashflow => {
                    totalDebitPerAccount += cashflow.debit;
                    totalCreditPerAccount += cashflow.credit;
                });

                operationalCashes = [...operationalCashes, {
                    account : `${cashflows.name}`,
                    totalCash : totalDebitPerAccount - totalCreditPerAccount
                }]
            }
            
        });

        showOperationalCash();
        showInvestmentCash();
        showFinanceCash();

        increaseCash.innerHTML = `${(totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes) < 0 ? '-' : ''}${formatRupiah((totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes).toString())}`;

        endCash.innerHTML = `${formatRupiah(res.totalBalance.toString())}`;
        startCash.innerHTML = `${formatRupiah((res.totalBalance - (totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes)).toString())}`;
        
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
    operationalCashes = [];
    investmentCashes = [];
    financeCashes = [];

    totalOperationalCashes = 0;
    totalInvesmentCashes = 0;
    totalFinanceCashes = 0;
    setQuery();
    await showReport();

}

const goToPrintCashflow = () => {
    let url = `/report/print-cashflow?date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`;
    
    window.open(url);
}

window.addEventListener('load', function(){
    setDefaultValue();
    showReport();
})