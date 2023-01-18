const business = document.querySelector('#content').dataset.business;

const period = document.querySelector('#period');
const revenue = document.querySelector('#revenue');
const totalRevenue = document.querySelector('#total-revenue');

const cost = document.querySelector('#cost');
const totalCost = document.querySelector('#total-cost');

const grossLostProfit = document.querySelector('#gross-lost-profit');
const lostProfitBeforeTax = document.querySelector('#lost-profit-before-tax');
const lostProfitAfterTax = document.querySelector('#lost-profit-after-tax');

//filter component
const selectFilter = document.querySelector('#select-filter');
const dateRange = document.querySelector('#date-range');
const startFilter = document.querySelector('#start-filter');
const endFilter = document.querySelector('#end-filter');

const printButton = document.querySelector('#print-button');

let date_from = '';
let date_to = '';
let thisWeek = '';
let thisMonth = 1;
let thisYear = '';

let filterBy = '';

let revenues = [];
let costs = [];

let totalRevenues = 0;
let totalCosts = 0;
let cogs = 0;
let totalTax = 0;

const setDefaultValue = () => {
    accountName = '';
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = 1;
    thisYear = '';

    filterBy = 'this month';

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
        let url = `/api/${business}/report/lost-profit?date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`;

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


function filterButton(){
    selectFilter.value = filterBy;
}

function changeFilter(value){
    filterBy = value.value;

    filterBy == 'custom' ? dateRange.classList.remove('d-none') : dateRange.classList.add('d-none');

    if (filterBy == 'custom') {
        dateRange.classList.remove('d-none');
        startFilter.value = dateNow();
        endFilter.value = dateNow();
    } else {
        dateRange.classList.add('d-none');
    }
}

function setQuery(){
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = '';
    thisYear = '';

    switch (filterBy) {
        case 'today':
            date_from = dateNow();
            date_to = dateNow();
            break;
        case 'this week':
            thisWeek = true;
            break;
        case 'this month':
            thisMonth = true;
            break;
        case 'this year':
            thisYear = true;

            break;
        default:
            date_from = startFilter.value;
            date_to = endFilter.value;
            break;
    }
}

async function submitFilter(){
    revenues = [];
    costs = [];

    totalRevenues = 0;
    totalCosts = 0;
    cogs = 0;
    totalTax = 0;
    setQuery();
    await showReport();

}

const goToPrintCashflow = () => {
    let url = `/${business}/report/print-lost-profit?date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`;
    
    window.open(url);
}

window.addEventListener('load',async function(){
    setDefaultValue();
    await showReport();
})