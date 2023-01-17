const tokenIndex = `Bearer ${localStorage.getItem('token')}`;
const business = document.querySelector('#content').dataset.business;
let outletId = 0;
let month = 0;
let year = 0;
let timeLimit = '';

let lostProfit = 0;

const defineComponentElement = () => {
    const filterValue = document.querySelector('#filter-value');
    const lostProfitLabel = document.querySelector('#lost-profit-label');
    const lostProfitValue = document.querySelector('#lost-profit-value');
    const income = document.querySelector('#income');
    const expense = document.querySelector('#expense');
    const lostProfitChartIcon = document.querySelector('#lost-profit-chart-icon');
    const asset = document.querySelector('#asset');
    const liability = document.querySelector('#liability');
    const equity = document.querySelector('#equity');

    return {
        filterValue, lostProfitLabel, lostProfitValue, income, expense, lostProfitChartIcon, asset, liability, equity
    }
}

const loadingStatus = () => {
    return `<tr class="journal-table-row">
        <td colspan="5" class="text-center">
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" style="width: .7rem; height: .7rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        </td>
    </tr>`;
}

const setLoading = () => {
    const {
        lostProfitValue, income, expense, asset, liability, equity
    } = defineComponentElement();

    lostProfitValue.innerHTML = loadingStatus();
    income.innerHTML = loadingStatus();
    expense.innerHTML = loadingStatus();
    asset.innerHTML = loadingStatus();
    liability.innerHTML = loadingStatus();
    equity.innerHTML = loadingStatus();

}

const showMonthStatus = (month, year) => {
    const {
        filterValue
    } = defineComponentElement();
    
    filterValue.value = `${monthReadable(month)} ${year}`;
}

const showData = async () => {
    await getLostProfit();
    await getAsset();
    await getLiability();
    await getEquity();
}

const setDefaultValue = async () => {

    let dateArr = waktu(true).split('-');

    month = parseInt(dateArr[1]);
    year = parseInt(dateArr[0]);

    if ( month + 1 > 12) {
        timeLimit = `${year + 1}-01-01`
        
    } else {
        timeLimit = `${year}-${month + 1}-01`
    }
    
    showMonthStatus(month, year);
    await showData();
}

const getLostProfit = async () => {
    const {
        lostProfitLabel, lostProfitValue, income, expense, lostProfitChartIcon
    } = defineComponentElement();

    let url = `/api/${business}/home/lost-profit?&month=${month}&year=${year}`;

    await axios.get(url, {
        headers:{
            Authorization : tokenIndex
        }
    })
                .then(res=> {
                    let data = res.data.data;
                    lostProfit = data.lost_profit;
                    lostProfitLabel.innerHTML = data.lost_profit > 1 ? "Laba" : "Rugi";

                    lostProfitValue.innerHTML = `Rp.${formatRupiah(data.lost_profit.toString())}`;
                    
                    lostProfitChartIcon.innerHTML = data.lost_profit > 1 ?
                                                    `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-graph-up-arrow text-success" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z"/>
                                                </svg>` 
                                                : `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-graph-down-arrow text-danger" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0Zm10 11.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 0-1 0v2.6l-3.613-4.417a.5.5 0 0 0-.74-.037L7.06 8.233 3.404 3.206a.5.5 0 0 0-.808.588l4 5.5a.5.5 0 0 0 .758.06l2.609-2.61L13.445 11H10.5a.5.5 0 0 0-.5.5Z"/>
                                              </svg>`;

                    income.innerHTML = `Rp.${formatRupiah(data.income.toString())}`;
                    expense.innerHTML = `Rp.${formatRupiah(data.expense.toString())}`;
                })
                .catch(err => {
                    console.log(err);
                })
}

const getAsset = async () => {
    const {
        asset
    } = defineComponentElement();

    let url = `/api/${business}/home/asset?time_limit=${timeLimit}&month_selected=${month}&year_selected=${year}`;

    await axios.get(url, {
        headers:{
            Authorization : tokenIndex
        }
    })
                .then(res=> {
                    let data = res.data.data;
                    asset.innerHTML = `Rp.${formatRupiah(data.toString())}`
                })
                .catch(err => {
                    console.log(err);
                })
}

const getLiability = async () => {
    const {
        liability
    } = defineComponentElement();

    let url = `/api/${business}/home/liability?time_limit=${timeLimit}&month_selected=${month}&year_selected=${year}`;

    await axios.get(url, {
        headers:{
            Authorization : tokenIndex
        }
    })
                .then(res=> {
                    let data = res.data.data;
                    liability.innerHTML = `Rp.${formatRupiah(data.toString())}`
                })
                .catch(err => {
                    console.log(err);
                })
}

const getEquity = async () => {
    const {
        equity
    } = defineComponentElement();

    let url = `/api/${business}/home/equity?time_limit=${timeLimit}&month_selected=${month}&year_selected=${year}`;

    await axios.get(url, {
        headers:{
            Authorization : tokenIndex
        }
    })
                .then(res=> {
                    let data = res.data.data;
                    equity.innerHTML = `Rp.${formatRupiah(data.toString())}`
                })
                .catch(err => {
                    console.log(err);
                })
}

const prevMonth = async () => {
    month--;
    if (month < 1) {
        month = 12;
        year--;
    }

    if ( month + 1 > 12) {
        timeLimit = `${year + 1}-01-01`
        
    } else {
        timeLimit = `${year}-${month + 1}-01`
    }

    showMonthStatus(month, year);
    setLoading();
    await showData();
}

const nextMonth = async () => {
    month++;
    if (month > 12) {
        month = 1;
        year++;
    }

    if ( month + 1 > 12) {
        timeLimit = `${year + 1}-01-01`
        
    } else {
        timeLimit = `${year}-${month + 1}-01`
    }

    showMonthStatus(month, year);
    setLoading();
    await showData();
}


window.addEventListener('load', async function(){
    setLoading();
    await setDefaultValue();
})