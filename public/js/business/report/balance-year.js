const business = document.querySelector('#content').dataset.business;

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

let year = 0;

let currentAssets = [];
let nonCurrentAssets = [];
let shortTermLiabilities = [];
let longTermLiabilities = [];
let equities= [];

let totalCurrentAssetsNow = 0;
let totalCurrentAssetsBefore = 0;
let totalNonCurrentAssetsNow = 0;
let totalNonCurrentAssetsBefore = 0;
let totalAssetsNow = 0;
let totalAssetsBefore = 0;

let totalLongTermLiabilitiesNow = 0;
let totalLongTermLiabilitiesBefore = 0;
let totalShortTermLiabilitiesNow = 0;
let totalShortTermLiabilitiesBefore = 0;
let totalEquitiesNow = 0;
let totalEquitiesBefore = 0;

let totalLiabilitiesEquitiesNow = 0;
let totalLiabilitiesEquitiesBefore = 0;

const setDefaultValue = () => {
    let date = new Date();
    year = date.getFullYear();

    currentAssets = [];
    nonCurrentAssets = [];
    shortTermLiabilities = [];
    longTermLiabilities = [];
    equities= [];

    totalCurrentAssetsNow = 0;
    totalCurrentAssetsBefore = 0;
    totalNonCurrentAssetsNow = 0;
    totalNonCurrentAssetsBefore = 0;
    totalAssetsNow = 0;
    totalAssetsBefore = 0;

    totalLongTermLiabilitiesNow = 0;
    totalLongTermLiabilitiesBefore = 0;
    totalShortTermLiabilitiesNow = 0;
    totalShortTermLiabilitiesBefore = 0;
    totalEquitiesNow = 0;
    totalEquitiesBefore = 0;

    totalLiabilitiesEquitiesNow = 0;
    totalLiabilitiesEquitiesBefore = 0;
}

const showBalance = (component, totalComponentNow, totalComponentBefore, balances, totalBalanceNow, totalBalanceBefore) => {
    if (balances.length != 0) {
        let list = '';
        
        balances.map(balance => {
            list += balance.totalNow + balance.totalBefore != 0 ? `
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
                            ${balance.totalNow < 0 ? `(${formatRupiah(balance.totalNow.toString())})` : formatRupiah(balance.totalNow.toString())} 
                        </div>
                    </div>  
                </td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${balance.totalBefore < 0 ? `(${formatRupiah(balance.totalBefore.toString())})` : formatRupiah(balance.totalBefore.toString())} 
                        </div>
                    </div> 
                </td>
            </tr>
            ` : ''
        });
        component.innerHTML = list;
    }

    totalComponentNow.innerHTML = `${formatRupiah(totalBalanceNow.toString())}`;
    totalComponentBefore.innerHTML = `${formatRupiah(totalBalanceBefore.toString())}`;

}

const goToPrintCashflow = () => {
    let url = `/${business}/report/print-balance-year?year=${year}&end_year=${year}`;
    
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
    setDefaultValue();
    year = parseInt(value.value);
    await showReport();
}

const showReport = async () => {
    try {
        let url = `/api/${business}/report/balance-year?year=${year}&end_year=${year}`;

        let res = await getData(url);
        
        period.innerHTML = res.period;
        periodBefore.innerHTML = res.period - 1;

        totalCurrentAssets = 0;
        totalNonCurrentAssets = 0;
        totalAssets = 0;

        totalShortTemLiabilities = 0;
        totalEquities = 0;
        totalLiabilitiesEquities = 0;
        
        let lastYears = Array.from(res.balance);

        if (!Array.isArray(res.balance)) {
            let {...data} = res.balance;
            let dataKeys = Object.keys(data);

            dataKeys.map(key => {
                lastYears = [...lastYears, data[parseInt(key)]];
            })
        }

        lastYears.map(balance => {
            if (parseInt(balance.code) < 2000000) {
                if (parseInt(balance.code) < 1600000) {
                    currentAssets = [...currentAssets, {
                        name : `${balance.name}`,
                        totalNow : parseInt(balance.total_now),
                        totalBefore : parseInt(balance.total_before),
                    }];
                    totalCurrentAssetsNow += parseInt(balance.total_now);
                    totalCurrentAssetsBefore += parseInt(balance.total_before);
                } else {
                    nonCurrentAssets = [...nonCurrentAssets, {
                        name : `${balance.name}`,
                        totalNow : parseInt(balance.total_now),
                        totalBefore : parseInt(balance.total_before),
                    }];
                    totalNonCurrentAssetsNow += parseInt(balance.total_now);
                    totalNonCurrentAssetsBefore += parseInt(balance.total_before);
                }
            } else if(parseInt(balance.code) < 3000000) {
                if (parseInt(balance.code) < 2600000) {
                    shortTermLiabilities = [...shortTermLiabilities, {
                        name : `${balance.name}`,
                        totalNow : parseInt(balance.total_now) * -1,
                        totalBefore : parseInt(balance.total_before) * -1,
                    }];
                    totalShortTermLiabilitiesNow += parseInt(balance.total_now) * -1;
                    totalShortTermLiabilitiesBefore += parseInt(balance.total_before) * -1;
                } else {
                    longTermLiabilities = [...longTermLiabilities, {
                        name : `${balance.name}`,
                        totalNow : parseInt(balance.total_now) * -1,
                        totalBefore : parseInt(balance.total_before) * -1,
                    }];
                    totalLongTermLiabilitiesNow += parseInt(balance.total_now) * -1;
                    totalLongTermLiabilitiesBefore += parseInt(balance.total_before) * -1;
                }
                
            } else {
                equities = [...equities, {
                    name : `${balance.name}`,
                    totalNow : parseInt(balance.total_now) * -1,
                    totalBefore : parseInt(balance.total_before) * -1,
                }];
                totalEquitiesNow += parseInt(balance.total_now) * -1;
                totalEquitiesBefore += parseInt(balance.total_before) * -1;
            }
        })

        showBalance(currentAsset, totalCurrentAssetNow, totalCurrentAssetBefore, currentAssets, totalCurrentAssetsNow, totalCurrentAssetsBefore);

        showBalance(nonCurrentAsset, totalNonCurrentAssetNow, totalNonCurrentAssetBefore, nonCurrentAssets, totalNonCurrentAssetsNow, totalNonCurrentAssetsBefore);

        showBalance(shortTermLiability, totalShortTermLiabilityNow, totalShortTermLiabilityBefore, shortTermLiabilities, totalShortTermLiabilitiesNow, totalShortTermLiabilitiesBefore);

        showBalance(longTermLiability, totalLongTermLiabilityNow, totalLongTermLiabilityBefore, longTermLiabilities, totalLongTermLiabilitiesNow, totalLongTermLiabilitiesBefore);

        showBalance(equity, totalEquityNow, totalEquityBefore, equities, totalEquitiesNow, totalEquitiesBefore);

        totalAssetNow.innerHTML = `${formatRupiah((totalCurrentAssetsNow + totalNonCurrentAssetsNow).toString())}`;
        totalAssetBefore.innerHTML = `${formatRupiah((totalCurrentAssetsBefore + totalNonCurrentAssetsBefore).toString())}`;

        totalLiabilityNow.innerHTML = `${formatRupiah((totalLongTermLiabilitiesNow + totalShortTermLiabilitiesNow).toString())}`;
        totalLiabilityBefore.innerHTML = `${formatRupiah((totalLongTermLiabilitiesBefore + totalShortTermLiabilitiesBefore).toString())}`;

        totalLiabilityEquityNow.innerHTML = `${formatRupiah((totalLongTermLiabilitiesNow + totalShortTermLiabilitiesNow + totalEquitiesNow).toString())}`;
        totalLiabilityEquityBefore.innerHTML = `${formatRupiah((totalLongTermLiabilitiesBefore + totalShortTermLiabilitiesBefore + totalEquitiesBefore).toString())}`;


    } catch (error) {
        console.log(error);
    }
}


window.addEventListener('load', async function(){
    setDefaultValue();
    setSelectYearValue();
    await showReport()
})