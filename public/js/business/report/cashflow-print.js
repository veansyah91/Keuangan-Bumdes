// define component 
const business = document.querySelector('#content').dataset.business;

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
    let list = '';
    totalOperationalCashes = 0;
    if (operationalCashes.length > 0) {
        operationalCashes.map(cash => {
            totalOperationalCashes += cash.debit - cash.credit;
            list += `
            <tr>
                <td class="w-50">${cash.account_name}</td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${cash.credit - cash.debit > 0 ? `(${formatRupiah((cash.credit - cash.debit).toString())})` : formatRupiah((cash.credit - cash.debit).toString())}
                        </div>
                    </div>                                        
                </td>
                <td w-25></td>
            </tr>`;
        })  
    }
    operationalActivity.innerHTML = list;
    totalOperationalActivity.innerHTML = totalOperationalCashes < 0 ? `(${formatRupiah(totalOperationalCashes.toString())})` : formatRupiah(totalOperationalCashes.toString());
}

const showInvestmentCash = () => {
    let list = '';
    totalInvesmentCashes = 0;
    if (investmentCashes.length > 0) {

        investmentCashes.map(cash => {
            totalOperationalCashes += cash.debit - cash.credit;
            list += `
            <tr>
                <td class="w-50">${cash.account_name}</td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${cash.credit > 0 ? `(${formatRupiah(cash.credit.toString())})` : formatRupiah(cash.debit.toString())}
                        </div>
                    </div>                                        
                </td>
                <td w-25></td>
            </tr>`;
        })
    }
    investmentActivity.innerHTML = list ;        
    totalInvestmentActivity.innerHTML = totalInvesmentCashes < 0 ? `(${formatRupiah(totalInvesmentCashes.toString())})` : formatRupiah(totalInvesmentCashes.toString())
    
}

const showFinanceCash = () => {
    let list = '';
    totalFinanceCashes = 0;
    if (financeCashes.length > 0) {

        financeCashes.map(cash => {
            totalOperationalCashes += cash.debit - cash.credit;
            list += `
            <tr>
                <td class="w-50">${cash.account_name}</td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${cash.credit > 0 ? `(${formatRupiah(cash.credit.toString())})` : formatRupiah(cash.debit.toString())}
                        </div>
                    </div>                                        
                </td>
                <td w-25></td>
            </tr>`;
        })
    }
    financeActivity.innerHTML = list;        
    totalFinanceActivity.innerHTML = totalFinanceCashes < 0 ? `(${formatRupiah(totalFinanceCashes.toString())})` : formatRupiah(totalFinanceCashes.toString())
}

const showReport = async () => {
    try {
        let res = await getData(url);

        period.innerHTML = res.period;
        res.cashFlows.map(cashflows => {
            if (cashflows.type == 'investment') {
                investmentCashes = [...investmentCashes, cashflows];
            } else if (cashflows.type == 'finance') {
                financeCashes = [...financeCashes, cashflows];
            } else {
                operationalCashes = [...operationalCashes, cashflows];
            }
            
        });

        showOperationalCash();
        showInvestmentCash();
        showFinanceCash();

        increaseCash.innerHTML = `${(totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes) < 0 ? '(' : ''}${formatRupiah((totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes).toString())}${(totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes) < 0 ? ')' : ''}`;

        endCash.innerHTML = `${res.totalBalance < 0 ? '(' : ''}${formatRupiah(res.totalBalance.toString())}${res.totalBalance < 0 ? ')' : ''}`;
        startCash.innerHTML = `${(res.totalBalance - (totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes)) < 0 ? '(' : ''}${formatRupiah((res.totalBalance - (totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes)).toString())}${(res.totalBalance - (totalOperationalCashes + totalInvesmentCashes + totalFinanceCashes)) < 0 ? ')' : ''}`;
        
    } catch (error) {
        console.log(error);
    }
}

window.addEventListener('load', function(){
    let currentUrl = new URL(window.location.href);

    url = `/api/${business}/report/cashflow${currentUrl.search}`;
    setDefaultValue();
    showReport();
})