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
let thisMonth = '';
let thisYear = '';

let operationalCashes = [];
let investmentCashes = [];
let financeCashes = [];

let totalOperationalCashes = 0;
let totalInvesmentCashes = 0;
let totalFinanceCashes = 0;

let url;

const setDefaultValue = () => {
    accountName = '';
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = '';
    thisYear = '';

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
    financeActivity.innerHTML = loading();
    totalFinanceActivity.innerHTML = loading();

    investmentActivity.innerHTML = loading();
    totalInvestmentActivity.innerHTML = loading();

    operationalActivity.innerHTML = loading();
    totalOperationalActivity.innerHTML = loading();

    increaseCash.innerHTML = loading();

    endCash.innerHTML = loading();
    startCash.innerHTML = loading();
}

const showReport = async () => {
    loadingComponent();
    try {
        let res = await getData(url);

        console.log(res);
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

window.addEventListener('load', function(){
    let currentUrl = new URL(window.location.href);

    url = `/api/report/cashflow${currentUrl.search}`;
    setDefaultValue();
    showReport();
})