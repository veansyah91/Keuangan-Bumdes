let year = 0;
let url = '';

const period = document.querySelector('#period');
const revenue = document.querySelector('#revenue');
const totalRevenue = document.querySelector('#total-revenue');

const trialBalanceList = document.querySelector('#trial-balance');
const trialBalanceFooter = document.querySelector('#trial-balance-footer');

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

function setDefault(){
    let date = new Date();
    year = date.getFullYear();
}

function loading(){
    return `
        <tr>
            <td colspan=8 class="text-center">
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

async function showReport(){
    trialBalanceList.innerHTML = loading();
    try {
        url = `/api/report/trial-balance?year=${year}&end_year=${year}`
        let res = await getData(url);

        period.innerHTML = res.period;
        yearLabel.innerHTML = res.period;

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
    // setDefaultValue();
    printButtonVisibility();
    await showReport();
})