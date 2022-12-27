const period = document.querySelector('#period');

const currentAsset = document.querySelector('#current-asset');
const totalCurrentAsset = document.querySelector('#total-current-asset');
const nonCurrentAsset = document.querySelector('#non-current-asset');
const totalNonCurrentAsset = document.querySelector('#total-non-current-asset');
const totalAsset = document.querySelector('#total-asset');

const liability = document.querySelector('#liability');
const totalLiability = document.querySelector('#total-liability');
const equity = document.querySelector('#equity');
const totalEquity = document.querySelector('#total-equity');
const totalLiabilityEquity = document.querySelector('#total-liability-equity');

let date_from = '';
let date_to = '';
let thisWeek = '';
let thisMonth = 1;
let thisYear = '';

let currentAssets = [];
let nonCurrentAssets = [];
let liabilities = [];
let equities= [];

let totalCurrentAssets = 0;
let totalNonCurrentAssets = 0;
let totalAssets = 0;

let totalLiabilities = 0;
let totalEquities = 0;
let totalLiabilitiesEquities = 0;

let url;

const setDefaultValue = () => {
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = 1;
    thisYear = '';

    filterBy = 'this month';

    currentAssets = [];
    nonCurrentAssets = [];
    liabilities = [];
    equities= [];

    totalCurrentAssets = 0;
    totalNonCurrentAssets = 0;

    totalLiabilities = 0;
    totalEquities = 0;
}

const showBalance = (component, totalComponent, balances, totalBalances) => {
    if (balances.length > 0) {
        let list = '';

        balances.map(balance => {
            list += balance.total != 0 ? `
            <tr>
                <td class="w-40">
                    ${balance.name}
                </td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${balance.total < 0 ? `(${formatRupiah(balance.total.toString())})` : formatRupiah(balance.total.toString())} 
                        </div>
                    </div>  
                </td>
                <td class="w-25">

                </td>
            </tr>
            ` : ''
        });
        component.innerHTML = list;
    }

    totalComponent.innerHTML = `${formatRupiah(totalBalances.toString())}`;

}

const goToPrintCashflow = () => {
    let url = `/report/print-balance?date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`;
    
    window.open(url);
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
    totalAsset.innerHTML = loading();
    totalLiabilityEquity.innerHTML = loading();

    currentAsset.innerHTML = loading();
    totalCurrentAsset.innerHTML = loading();

    nonCurrentAsset.innerHTML = loading();
    totalNonCurrentAsset.innerHTML = loading();

    liability.innerHTML = loading();
    totalLiability.innerHTML = loading();
    equity.innerHTML = loading();
    totalEquity.innerHTML = loading();
}

const showReport = async () => {
    loadingComponent()
    try {
        let res = await getData(url);

        period.innerHTML = res.period;

        totalCurrentAssets = 0;
        totalNonCurrentAssets = 0;
        totalAssets = 0;

        totalLiabilities = 0;
        totalEquities = 0;
        totalLiabilitiesEquities = 0;

        res.balance.map(balance => {
            if (parseInt(balance.code) < 2000000) {
                if (parseInt(balance.code) < 1600000) {
                    currentAssets = [...currentAssets, {
                        name : `${balance.code} - ${balance.name}`,
                        total : parseInt(balance.total)
                    }];
                    totalCurrentAssets += balance.total;
                } else {
                    nonCurrentAssets = [...nonCurrentAssets, {
                        name : `${balance.code} - ${balance.name}`,
                        total : parseInt(balance.total)
                    }];
                    totalNonCurrentAssets += balance.total;
                }
            } else if(parseInt(balance.code) < 3000000) {
                liabilities = [...liabilities, {
                    name : `${balance.code} - ${balance.name}`,
                    total : parseInt(balance.total) * -1
                }];
                totalLiabilities += balance.total * -1;
            } else {
                equities = [...equities, {
                    name : `${balance.code} - ${balance.name}`,
                    total : parseInt(balance.total) * -1
                }];
                totalEquities += balance.total * -1;
            }
        })

        showBalance(currentAsset, totalCurrentAsset, currentAssets, totalCurrentAssets);
        showBalance(nonCurrentAsset, totalNonCurrentAsset, nonCurrentAssets, totalNonCurrentAssets);

        totalAsset.innerHTML = `${formatRupiah((totalCurrentAssets + totalNonCurrentAssets).toString())}`;

        showBalance(liability, totalLiability, liabilities, totalLiabilities);
        showBalance(equity, totalEquity, equities, totalEquities);
        totalLiabilityEquity.innerHTML = `${formatRupiah((totalLiabilities + totalEquities).toString())}`;  


    } catch (error) {
        console.log(error);
    }
}

window.addEventListener('load', async function(){
    setDefaultValue();
    let currentUrl = new URL(window.location.href);

    url = `/api/report/balance${currentUrl.search}`;
    await showReport()
})