const business = document.querySelector('#content').dataset.business;

const period = document.querySelector('#period');
const revenue = document.querySelector('#revenue');
const totalRevenue = document.querySelector('#total-revenue');

const cost = document.querySelector('#cost');
const totalCost = document.querySelector('#total-cost');

const grossLostProfit = document.querySelector('#gross-lost-profit');
const lostProfitBeforeTax = document.querySelector('#lost-profit-before-tax');
const lostProfitAfterTax = document.querySelector('#lost-profit-after-tax');

let revenues = [];
let costs = [];

let totalRevenues = 0;
let totalCosts = 0;
let cogs = 0;
let totalTax = 0;

const setDefaultValue = () => {

    revenues = [];
    costs = [];

    totalRevenues = 0;
    totalCosts = 0;
    cogs = 0;
    totalTax = 0;
}


const showLostProfit = (component, totalComponent, lostProfits, totalLostProfits) => {
    let list = '';
    if (lostProfits.length > 0) {

        lostProfits.map(lostProfit => {
            list += lostProfit.total > 0 ? `
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
                            ${formatRupiah(lostProfit.total.toString())} 
                        </div>
                    </div>  
                </td>
                <td class="w-25">

                </td>
            </tr>
            ` : ''
        });
    }
    component.innerHTML = list;

    totalComponent.innerHTML = `${formatRupiah(totalLostProfits.toString())}`;
}

const showReport = async () => {
    try {
        let res = await getData(url);

        period.innerHTML = res.period;
       
        res.lost_profit.map(data => {
            if (parseInt(data.code) < 5000000) {
                revenues = [...revenues, {
                    name: `${data.name}`,
                    total: data.total * -1
                }];
                totalRevenues += data.total * -1;
               
            }else{
                costs = [...costs, {
                    name: `${data.name}`,
                    total: data.total
                }];
                totalCosts += data.total;

                if (data.sub_category == 'Harga Pokok Penjualan' || data.sub_category == 'Beban Atas Pendapatan' || data.sub_category == 'Potongan Pembelian Barang Dagang' || data.sub_category == 'Retur Pembelian Barang Dagang') {
                    cogs += data.total;
                }

                if (data.sub_category == 'Beban Pajak') {
                    totalTax += data.total;
                }
            }
        });

        showLostProfit(revenue, totalRevenue, revenues, totalRevenues);
        showLostProfit(cost, totalCost, costs, totalCosts);

        grossLostProfit.innerHTML = `${totalRevenues - cogs < 0 ? '-' : ''}${formatRupiah((totalRevenues - cogs).toString())}`;
        lostProfitBeforeTax.innerHTML = `${totalRevenues - totalCosts + totalTax < 0 ? '-' : ''}${formatRupiah((totalRevenues - totalCosts + totalTax).toString())}`;

        lostProfitAfterTax.innerHTML = `${totalRevenues - totalCosts < 0 ? '-' : ''}${formatRupiah((totalRevenues - totalCosts).toString())}`;
    } catch (error) {
        console.log(error);
    }
}

window.addEventListener('load',async function(){
    let currentUrl = new URL(window.location.href);

    url = `/api/${business}/report/lost-profit${currentUrl.search}`;
    setDefaultValue();
    await showReport();
})