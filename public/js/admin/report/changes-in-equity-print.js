const period = document.querySelector('#period');
const periodBefore = document.querySelector('#period-before');

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

const userPositionLabel = document.querySelector('#user-position-label');
const userPositionForm = document.querySelector('#user-position-form');
const userNameLabel = document.querySelector('#user-name-label');
const userNameForm = document.querySelector('#user-name-form');
const locationPrintLabel = document.querySelector('#location-print-label');
const locationPrintForm = document.querySelector('#location-print-form');
const addressDate = document.querySelector('#address-date');
const yearLabel = document.querySelector('#year-label');

let year = 0;

let equities= [];
let prive= [];
let lostProfits= [];

let totalNonCurrentAssetsNow = 0;

let totalEquitiesNow = 0;
let totalPriveNow = 0;
let totalLostProfitNow = 0;

let totalLiabilitiesEquitiesNow = 0;
let totalLiabilitiesEquitiesBefore = 0;

const setDefaultValue = () => {
    let date = new Date();
    year = date.getFullYear();

    equities= [];
    prive=[];
    lostProfits = [];

    totalPriveNow = 0;
    totalEquitiesNow = 0;
    totalLostProfitNow = 0;

    totalLiabilitiesEquitiesNow = 0;
    totalLiabilitiesEquitiesBefore = 0;
}

const goToPrintCashflow = () => {
    let url = `/report/print-balance-year?year=${year}&end_year=${year}`;
    
    window.open(url);
}

const handleSelectYear = async (value) => {
    setDefaultValue();
    year = parseInt(value.value);
    await showReport();
}

function totalEquityFunc(accounts){
    let total = 0;
    if (accounts.length > 0) {
        accounts.forEach(account => {
            if (account.ledgers.length > 0) {
                account.ledgers.forEach(ledger => {
                    total += ledger.credit - ledger.debit;
                })
            }
        });
    }

    return total;
}

const showReport = async () => {
    try {
        let url = `/api/report/changes-in-equity?year=${year}&end_year=${year}`;

        let res = await getData(url);

        period.innerHTML = res.period;
        yearLabel.innerHTML = res.period;
        
        let lastYears = Array.from(res.balance);

        if (!Array.isArray(res.balance)) {
            let {...data} = res.balance;
            let dataKeys = Object.keys(data);

            dataKeys.map(key => {
                lastYears = [...lastYears, data[parseInt(key)]];
            })
        }

        let totalLostProfitCurrent = 0;
        let totalEquityBefore = 0;
        let totalLostProfitBefore = 0;
        res.equityBefore.map(balance => {
            if (balance.accounts.length > 0) {
                balance.accounts.forEach(account => {
                    if (account.ledgers.length > 0) {
                        account.ledgers.forEach(ledger => {
                            totalEquityBefore += ledger.credit - ledger.debit;
                        })
                    }
                });
            }
        })

        res.lostProfitBefore.map(balance => {
            totalLostProfitBefore += totalEquityFunc(balance.accounts);
        })

        lastYears.map(balance => {
            let totalEquity = 0;
            if (parseInt(balance.code) <= 3500000) {
                if (parseInt(balance.code) < 3400000) {
                    totalEquity += totalEquityFunc(balance.accounts)
                    //modal ditambah
                    equities = [...equities, {
                        name : `${balance.name}`,
                        total : totalEquity,
                    }];
                    totalEquitiesNow += totalEquity || 0;
                } else {
                    totalEquity += totalEquityFunc(balance.accounts)
                    //modal diambil (prive)
                    prive = [...prive, {
                        name : `${balance.name}`,
                        total : totalEquity,
                    }];
                    totalPriveNow += totalEquity;
                }
            }else {
                totalEquity += totalEquityFunc(balance.accounts)
                //modal lost profit
                lostProfits = [...lostProfits, {
                    name : `${balance.name}`,
                    total : totalEquity,
                }];
                totalLostProfitNow += totalEquity;
            }
        })

        let dataTable = '';
        let totalEquityCurrent = totalEquityBefore;

        //penyertaan modal
        dataTable += `
        <tr>
            <td class="w-40" style="width: 40%">Penyertaan Modal Awal</td>
            <td class="w-25" style="width: 25%">
                <div class="d-flex justify-content-between">
                    <div>
                        Rp. 
                    </div>
                    <div class="text-end">
                        ${totalEquityBefore < 0 ? '(' : ''}${formatRupiah(totalEquityBefore.toString())}${totalEquityBefore < 0 ? ')' : ''}
                    </div>
                </div> 
            </td>
        </tr>
        <tr>
            <th class="w-40" style="width: 40%">Penambahan Investasi Periode Berjalan :</th>
            <th class="w-25" style="width: 25%">
                
            </th>
        </tr>
        `;

        equities.forEach(equity => {
            totalEquityCurrent += equity.total;
            dataTable += `
                <tr>
                    <td class="w-40" style="width: 40%">${equity.name}</td>
                    <td class="w-25" style="width: 25%">
                        <div class="d-flex justify-content-between">
                            <div>
                                Rp. 
                            </div>
                            <div class="text-end">
                                ${equity.total < 0 ? '(' : ''}${formatRupiah(equity.total.toString())}${equity.total < 0 ? ')' : ''}
                            </div>
                        </div> 
                    </td>
                </tr>
            `
        })

        document.querySelector('#total-current-asset-now').innerHTML = `
        <tr>
            <th class="w-40" style="width: 40%">Penyertaan Modal Akhir</th>
            <th class="w-25" style="width: 25%">
                <div class="d-flex justify-content-between">
                    <div>
                        Rp. 
                    </div>
                    <div class="text-end">
                    ${totalEquityCurrent < 0 ? '(' : ''}${formatRupiah(totalEquityCurrent.toString())}${totalEquityCurrent < 0 ? ')' : ''}
                    </div>
                </div>      
            </th>
        </tr> 
        `;

        document.querySelector('#equity').innerHTML = dataTable;

        totalLostProfitCurrent = totalLostProfitBefore;

        //laba rugi
        dataTable = `
            <tr>
                <td class="w-40" style="width: 40%">Laba Ditahan Awal</td>
                <td class="w-25" style="width: 25%">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${totalLostProfitBefore < 0 ? '(' : ''}${formatRupiah(totalLostProfitBefore.toString())}${totalLostProfitBefore < 0 ? ')' : ''}
                        </div>
                    </div> 
                </td>
            </tr>
        `;

        lostProfits.forEach(lostProfit => {
            if (lostProfit.name == "Laba Tahun Berjalan") {
                lostProfit.total = res.lostProfit - lostProfit.total;
            }
            totalLostProfitCurrent += lostProfit.total;
            
            dataTable += lostProfit.total != 0 ? `
                <tr>
                    <td class="w-40" style="width: 40%">${lostProfit.name}</td>
                    <td class="w-25" style="width: 25%">
                        <div class="d-flex justify-content-between">
                            <div>
                                Rp. 
                            </div>
                            <div class="text-end">
                                ${lostProfit.total < 0 ? '(' : ''}${formatRupiah(lostProfit.total.toString())}${lostProfit.total < 0 ? ')' : ''}
                            </div>
                        </div> 
                    </td>
                </tr>
            ` : ''
        });

        //prive
        dataTable += `
            <tr>
                <th class="w-40" style="width: 40%">Bagi Hasil Penyertaan :</th>
                <th class="w-25" style="width: 25%">
                    
                </th>
            </tr>
        `
        prive.forEach(p => {
            totalLostProfitCurrent += p.total;
            
            dataTable += `
                <tr>
                    <td class="w-40" style="width: 40%">${p.name}</td>
                    <td class="w-25" style="width: 25%">
                        <div class="d-flex justify-content-between">
                            <div>
                                Rp. 
                            </div>
                            <div class="text-end">
                                ${p.total < 0 ? '(' : ''}${formatRupiah(p.total.toString())}${p.total < 0 ? ')' : ''}
                            </div>
                        </div> 
                    </td>
                </tr>
            `
        });

        document.querySelector('#lost-profit').innerHTML = dataTable;

        document.querySelector('#total-lost-profit-now').innerHTML = `
        <tr>
            <th class="w-40" style="width: 40%">Total Laba Ditahan</th>
            <th class="w-25" style="width: 25%">
                <div class="d-flex justify-content-between">
                    <div>
                        Rp. 
                    </div>
                    <div class="text-end">
                        ${totalLostProfitCurrent < 0 ? '(' : ''}${formatRupiah(totalLostProfitCurrent.toString())}${totalLostProfitCurrent < 0 ? ')' : ''}
                    </div>
                </div>  
            </th>
        </tr>
        `

        //total equity
        let totalEquity = totalLostProfitCurrent + totalEquityCurrent;
        document.querySelector('#total-equity').innerHTML = `
        <tr class="text-primary">
            <th class="w-40" style="width: 40%">Modal Akhir</th>
            <th class="w-25" style="width: 25%">
                <div class="d-flex justify-content-between">
                    <div>
                        Rp. 
                    </div>
                    <div class="text-end">
                        ${totalEquity < 0 ? '(' : ''}${formatRupiah(totalEquity.toString())}${totalEquity < 0 ? ')' : ''}
                    </div>
                </div>  
            </th>
        </tr>
        `;

    } catch (error) {
        console.log(error);
    }
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
    setDefaultValue();
    localStorageCheck();
    await showReport()
})