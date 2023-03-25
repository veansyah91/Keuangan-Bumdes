const token = `Bearer ${localStorage.getItem('token')}`;
const business = document.querySelector('#content').dataset.business;
const user = document.querySelector('#content').dataset.user;


const dateRange = document.querySelector('#date-range');
const startFilter = document.querySelector('#start-filter');
const endFilter = document.querySelector('#end-filter');

function setDefault(){
    filterBy = 'this month';
    date_from = '';
    date_to = '';
    thisWeek = '';
    thisMonth = 1;
    thisYear = '';
}

function filterButton(){
    document.querySelector('#select-filter').value = filterBy;
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
    setQuery();
    await showData();
}

async function showData(){
    let url = `/api/${business}/saving-account/${user}/book-api?date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;

    await axios.get(url, {
        headers:{
            Authorization : token
        }
    })
    .then(res => {
        let result = res.data.data;
        document.querySelector('#no_ref').innerHTML = result.contact.no_ref;
        document.querySelector('#name').innerHTML = result.contact.contact.name;
        document.querySelector('#period').innerHTML = result.period;

        let balance = result.totalBalance - result.balance;

        let list = `
        <tr>
            <th colspan="5">Saldo Awal</th>
            <td class="text-end">${formatRupiah(balance.toString())}</td>
        </tr>
        `;

        if (result.contact.account_payables.length > 0) {
            result.contact.account_payables.map(data => {
                let desc = data.description.split(" ");
                let mutation = data.credit - data.debit;
                balance += mutation;
                list += `
                <tr>
                    <td>${dateReadable(data.date)}</td>
                    <td>${data.no_ref}</td>
                    <td>${desc[0]} ${desc[1]}</td>
                    <td class="text-end">${formatRupiah(mutation.toString())}</td>
                    <td class="text-end">${mutation < 0 ? '(D)' : ''}</td>
                    <td class="text-end">${formatRupiah(balance.toString())}</td>
                <tr>
                `
            });
        } else {

        }
        document.querySelector('#list-data').innerHTML = list;
    })
    .catch(err => {
        console.log(err);
    })
}

window.addEventListener('load', async function(){
    setDefault();
    await showData();
})