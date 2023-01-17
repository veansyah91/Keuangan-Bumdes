let year = 0;
let url = '';

const business = document.querySelector('#content').dataset.business;
const period = document.querySelector('#period');
const trialBalanceList = document.querySelector('#trial-balance');
const trialBalanceFooter = document.querySelector('#trial-balance-footer');

function setDefault(){
    let date = new Date();
    year = date.getFullYear();
}

async function showReport(){
    try {
        url = `/api/${business}/report/trial-balance?year=${year}&end_year=${year}`;

        let res = await getData(url);

        console.log(res);
        
        period.innerHTML = res.period;

        let trialBalances = res.trial_balance;
        let list = '';
        
        let totalCreditStart = 0;
        let totalDebitStart = 0;
        let totalCreditEnd = 0;
        let totalDebitEnd = 0;
        let totalCreditMutation = 0;
        let totalDebitMutation = 0;

        trialBalances.map(trialBalance => {     
            debitStart = (trialBalance.debit_start - trialBalance.credit_start) > 0 ? trialBalance.debit_start - trialBalance.credit_start : 0;
            creditStart = (trialBalance.debit_start - trialBalance.credit_start) < 0 ? trialBalance.debit_start - trialBalance.credit_start : 0;

            debitMutation = (trialBalance.debit_mutation - trialBalance.credit_mutation) > 0 ? trialBalance.debit_mutation - trialBalance.credit_mutation : 0;
            creditMutation = (trialBalance.debit_mutation - trialBalance.credit_mutation) < 0 ? trialBalance.debit_mutation - trialBalance.credit_mutation : 0;

            debitEnd = (trialBalance.debit_end - trialBalance.credit_end) > 0 ? trialBalance.debit_end - trialBalance.credit_end : 0;
            creditEnd = (trialBalance.debit_end - trialBalance.credit_end) < 0 ? trialBalance.debit_end - trialBalance.credit_end : 0;

            totalCreditStart += creditStart;
            totalDebitStart += debitStart;

            totalCreditMutation += debitMutation;
            totalDebitMutation += debitMutation;

            totalCreditEnd += creditEnd;
            totalDebitEnd += debitEnd;
            list += 
                (trialBalance.credit_end - trialBalance.debit_end == 0 
                && trialBalance.credit_start - trialBalance.debit_start == 0
                && trialBalance.credit_mutation - trialBalance.debit_mutation == 0) ? '' :
                `
                <tr>
                    <td>${trialBalance.code}</td>
                    <td>${trialBalance.name}</td>
                    <td class="text-end">
                        ${formatRupiah(debitStart.toString())}
                        
                    </td>
                    <td class="text-end">
                        ${formatRupiah(creditStart.toString())}
                    </td>
                    <td class="text-end">${formatRupiah(debitMutation.toString())}</td>
                    <td class="text-end">${formatRupiah(creditMutation.toString())}</td>
                    <td class="text-end">${formatRupiah(debitEnd.toString())}</td>
                    <td class="text-end">${formatRupiah(creditEnd.toString())}</td>
                </tr>` ;
            
                
        });
        
        trialBalanceList.innerHTML = list;
        trialBalanceFooter.innerHTML = `
            <tr>
                <th class="text-center" colspan="2">Jumlah</th>
                <th class="text-end">${formatRupiah(totalDebitStart.toString())}</th>
                <th class="text-end">${formatRupiah(totalCreditStart.toString())}</th>
                <th class="text-end">${formatRupiah(totalDebitMutation.toString())}</th>
                <th class="text-end">${formatRupiah(totalCreditMutation.toString())}</th>
                <th class="text-end">${formatRupiah(totalDebitEnd.toString())}</th>
                <th class="text-end">${formatRupiah(totalCreditEnd.toString())}</th>
            </tr>
        `;

    } catch (error) {
        console.log(error);
    }
}

const goToPrintCashflow = () => {
    let url = `/${business}/report/trial-balance-print?year=${year}&end_year=${year}`;
    
    window.open(url);
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

const handleSelectYear = async (value) => {
    setDefault();
    year = parseInt(value.value);
    await showReport();
}

window.addEventListener('load', function(){
    setDefault();
    setSelectYearValue();
    showReport();
})