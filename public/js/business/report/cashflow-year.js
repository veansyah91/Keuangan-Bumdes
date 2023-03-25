// define component 
const business = document.querySelector('#content').dataset.business;

const period = document.querySelector('#period');
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

const printButton = document.querySelector('#print-button');

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

const setDefaultValue = () => {
    let date = new Date();
    year = date.getFullYear();

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

    let listRevenueActivity = ``;
    let listCostActivity = ``;

    listRevenueActivity += `
                    <tr>
                        <td class="w-50">${data.sub}</td>
                        <td class="w-25">
                            <div class="d-flex justify-content-between">
                                <div>
                                    Rp. 
                                </div>
                                <div class="text-end">
                                    ${formatRupiah(data.debit_now.toString())}
                                </div>
                            </div>                                        
                        </td>
                        <td class="w-25">
                        <div class="d-flex justify-content-between">
                                <div>
                                    Rp. 
                                </div>
                                <div class="text-end">
                                ${formatRupiah(data.debit_before.toString())}
                                </div>
                            </div></td>
                    </tr>
                ` ;

    listCostActivity += `
                <tr>
                    <td class="w-50">${data.sub}</td>
                    <td class="w-25">
                        <div class="d-flex justify-content-between">
                            <div>
                                Rp. 
                            </div>
                            <div class="text-end">
                                ${ data.credit_now > 0 ? `(${formatRupiah(data.credit_now.toString())})` : 0 }
                            </div>
                        </div>                                        
                    </td>
                    <td class="w-25">
                    <div class="d-flex justify-content-between">
                            <div>
                                Rp. 
                            </div>
                            <div class="text-end">
                                ${ data.credit_before > 0 ? `(${formatRupiah(data.credit_before.toString())})` : 0 }
                            </div>
                        </div></td>
                </tr>
                `;

    totalNow = data.debit_now - data.credit_now
    totalBefore = data.debit_before - data.credit_before

    return {
        listRevenueActivity, listCostActivity, totalNow, totalBefore
    }
}

const setSelectYearValue = () => {
    let list = '';

    let i = 2010;

    while (i < 2050) {
        list += `<option value="${i}" ${i == year ? 'selected' : ''}>${i}</option>`
        i++;
    }

    document.querySelector('#select-year').innerHTML = list;
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
        periodBefore.innerHTML = res.period - 1;

        res.baseAccountCategory.map(data => {
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
        
        increaseCashNow.innerHTML = `${(totalBalanceNow) < 0 ? '(' : ''}${formatRupiah((totalBalanceNow).toString())}${(totalBalanceNow) < 0 ? ')' : ''}`;
        increaseCashBefore.innerHTML = `${(totalBalanceBefore) < 0 ? '(' : ''}${formatRupiah((totalBalanceBefore).toString())}${(totalBalanceBefore) < 0 ? ')' : ''}`;

        endCashNow.innerHTML = `${res.totalBalance < 0 ? '(' : ''}${formatRupiah(res.totalBalance.toString())}${res.totalBalance < 0 ? ')' : ''}`;

        endCashBefore.innerHTML = `${(res.totalBalance - totalBalanceNow) < 0 ? '(' : ''}${formatRupiah((res.totalBalance - totalBalanceNow).toString())}${(res.totalBalance - totalBalanceNow) < 0 ? ')' : ''}`;

        startCashNow.innerHTML = `${(res.totalBalance - totalBalanceNow) < 0 ? '(' : ''}${formatRupiah((res.totalBalance - (totalBalanceNow)).toString())}${(res.totalBalance - totalBalanceNow) < 0 ? ')' : ''}`;
        
        startCashBefore.innerHTML = `${(res.totalBalance - totalBalanceNow - totalBalanceBefore) < 0 ? '(' : ''}${formatRupiah((res.totalBalance - totalBalanceNow - totalBalanceBefore).toString())}`;
        
    } catch (error) {
        console.log(error);
    }
}

const goToPrintCashflow = () => {
    let url = `/${business}/report/print-cashflow-year?year=${year}&end_year=${year}`;
    
    window.open(url);
}

window.addEventListener('load', async function(){
    setDefaultValue();
    setSelectYearValue();
    await showReport();
})