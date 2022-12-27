const business = document.querySelector('#content').dataset.business;

const period = document.querySelector('#period');
const periodBefore = document.querySelector('#period-before');

const revenue = document.querySelector('#revenue');
const totalRevenueNow = document.querySelector('#total-revenue-now');
const totalRevenueBefore = document.querySelector('#total-revenue-before');

const cost = document.querySelector('#cost');
const totalCostNow = document.querySelector('#total-cost-now');
const totalCostBefore = document.querySelector('#total-cost-before');

const grossLostProfitNow = document.querySelector('#gross-lost-profit-now');
const grossLostProfitBefore = document.querySelector('#gross-lost-profit-before');

const lostProfitBeforeTaxNow = document.querySelector('#lost-profit-before-tax-now');
const lostProfitBeforeTaxBefore = document.querySelector('#lost-profit-before-tax-before');

const lostProfitAfterTaxNow = document.querySelector('#lost-profit-after-tax-now');
const lostProfitAfterTaxBefore = document.querySelector('#lost-profit-after-tax-before');


const printButton = document.querySelector('#print-button');

let revenues = [];
let costs = [];

let totalRevenuesNow = 0;
let totalRevenuesBefore = 0;

let totalCostsNow = 0;
let totalCostsBefore = 0;

let cogsNow = 0;
let cogsBefore = 0;

let totalTaxNow = 0;
let totalTaxBefore = 0;

let year = 0;

const setDefaultValue = () => {
    let date = new Date();
    year = date.getFullYear();
    
    revenues = [];
    costs = [];

    totalRevenuesNow = 0;
    totalRevenuesBefore = 0;

    totalCostsNow = 0;
    totalCostsBefore = 0;

    cogsNow = 0;
    cogsBefore = 0;

    totalTaxNow = 0;
    totalTaxBefore = 0;
}


const showLostProfit = (component, totalComponentNow, totalComponentBefore, lostProfits, totalLostProfitsNow, totalLostProfitsBefore) => {
    if (lostProfits.length > 0) {
        let list = '';
        
        lostProfits.map(lostProfit => {
            list += `
            <tr>
                <td class="w-40">
                    ${lostProfit.name}
                </td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${formatRupiah(lostProfit.totalNow.toString())} 
                        </div>
                    </div>  
                </td>
                <td class="w-25">
                    <div class="d-flex justify-content-between">
                        <div>
                            Rp. 
                        </div>
                        <div class="text-end">
                            ${formatRupiah(lostProfit.totalBefore.toString())} 
                        </div>
                    </div> 
                </td>
            </tr>
            ` 
        });
        component.innerHTML = list;

        totalComponentNow.innerHTML = `${formatRupiah(totalLostProfitsNow.toString())}`;
        
        totalComponentBefore.innerHTML = `${formatRupiah(totalLostProfitsBefore.toString())}`;
    }
}

const showReport = async () => {
    try {
        let url = `/api/${business}/report/lost-profit-year?year=${year}&end_year=${year}`;

        let res = await getData(url);

        console.log(res);
        
        period.innerHTML = res.period;
        periodBefore.innerHTML = res.period - 1;

        revenues = [];
        costs = [];

        totalRevenuesNow = 0;
        totalRevenuesBefore = 0;

        totalCostsNow = 0;
        totalCostsBefore = 0;

        cogsNow = 0;
        cogsBefore = 0;

        totalTaxNow = 0;
        totalTaxBefore = 0;
        
        res.lost_profit.map(data => {
            if (parseInt(data.code) < 5000000) {
                if (data.total_now - data.total_before != 0) {
                    revenues = [...revenues, {
                        name: `${data.name}`,
                        totalNow: data.total_now * -1,
                        totalBefore: data.total_before * -1
                    }];
                    totalRevenuesNow += data.total_now * -1;
                    totalRevenuesBefore += data.total_before * -1;
                }
                
            }else{
                if (data.total_now - data.total_before != 0) {
                    costs = [...costs, {
                        name: `${data.name}`,
                        totalNow: data.total_now * -1,
                        totalBefore: data.total_before * -1
                    }];
                    totalCostsNow += data.total_now ;
                    totalCostsBefore += data.total_before ;
                }
                

                if (data.name == 'Harga Pokok Penjualan' || data.name == 'Beban Atas Pendapatan' || data.name == 'Potongan Pembelian Barang Dagang' || data.name == 'Retur Pembelian Barang Dagang') {
                    cogsNow += data.total_now;
                    cogsBefore += data.total_before;
                }

                if (data.name == 'Beban Pajak') {
                    totalTaxNow += data.total_now;
                    totalTaxBefore += data.total_before;
                }
            }
        });

        showLostProfit(revenue, totalRevenueNow, totalRevenueBefore, revenues, totalRevenuesNow, totalRevenuesBefore);

        showLostProfit(cost, totalCostNow, totalCostBefore, costs, totalCostsNow, totalCostsBefore);

        grossLostProfitNow.innerHTML = `${totalRevenuesNow - cogsNow < 0 ? `(${formatRupiah((totalRevenuesNow - cogsNow).toString())})` : formatRupiah((totalRevenuesNow - cogsNow).toString())}`;

        grossLostProfitBefore.innerHTML = `${totalRevenuesBefore - cogsBefore < 0 ? `(${formatRupiah((totalRevenuesBefore - cogsBefore).toString())})` : formatRupiah((totalRevenuesBefore - cogsBefore).toString())}`;

        lostProfitBeforeTaxNow.innerHTML = `${totalRevenuesNow - totalCostsNow + totalTaxNow < 0 ? `(${formatRupiah((totalRevenuesNow - totalCostsNow + totalTaxNow).toString())})` : formatRupiah((totalRevenuesNow - totalCostsNow + totalTaxNow).toString())}`;

        lostProfitBeforeTaxBefore.innerHTML = `${totalRevenuesBefore - totalCostsBefore + totalTaxBefore < 0 ? `(${formatRupiah((totalRevenuesBefore - totalCostsBefore + totalTaxBefore).toString())})` : formatRupiah((totalRevenuesBefore - totalCostsBefore + totalTaxBefore).toString())}`;


        lostProfitAfterTaxNow.innerHTML = `${totalRevenuesNow - totalCostsNow < 0 ? `(${formatRupiah((totalRevenuesNow - totalCostsNow).toString())})` : formatRupiah((totalRevenuesNow - totalCostsNow).toString())}`;
        lostProfitAfterTaxBefore.innerHTML = `${totalRevenuesBefore - totalCostsBefore < 0 ? `(${formatRupiah((totalRevenuesBefore - totalCostsBefore).toString())})` : formatRupiah((totalRevenuesBefore - totalCostsBefore).toString())}`;

    } catch (error) {
        console.log(error);
    }
}


const goToPrintCashflow = () => {
    let url = `/${business}/report/lost-profit-year-print?year=${year}&end_year=${year}`;
    
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

window.addEventListener('load',async function(){
    setDefaultValue();
    setSelectYearValue();
    await showReport();
})