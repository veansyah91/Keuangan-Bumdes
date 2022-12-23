let year = 0;
let url = '';

const period = document.querySelector('#period');
const trialBalanceList = document.querySelector('#trial-balance');
const trialBalanceFooter = document.querySelector('#trial-balance-footer');

function setDefault(){
    let date = new Date();
    year = date.getFullYear();
}

async function showReport(){
    try {
        url = `/api/report/trial-balance?year=${year}&end_year=${year}`;

        let res = await getData(url);
        
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
            totalCreditStart += trialBalance.credit_start;
            totalDebitStart += trialBalance.debit_start;

            totalCreditEnd += trialBalance.credit_end;
            totalDebitEnd += trialBalance.debit_end;

            totalCreditMutation += trialBalance.credit_mutation;
            totalDebitMutation += trialBalance.debit_mutation;

            debitStart = (trialBalance.debit_start - trialBalance.credit_start) > 0 ? trialBalance.debit_start - trialBalance.credit_start : 0;
            creditStart = (trialBalance.debit_start - trialBalance.credit_start) < 0 ? trialBalance.debit_start - trialBalance.credit_start : 0;
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
                    <td class="text-end">${formatRupiah((trialBalance.debit_end - debitStart).toString())}</td>
                    <td class="text-end">${formatRupiah((trialBalance.credit_end - creditStart).toString())}</td>
                    <td class="text-end">${formatRupiah(trialBalance.debit_end.toString())}</td>
                    <td class="text-end">${formatRupiah(trialBalance.credit_end.toString())}</td>
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
    let url = `/report/trial-balance-print?year=${year}&end_year=${year}`;
    
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