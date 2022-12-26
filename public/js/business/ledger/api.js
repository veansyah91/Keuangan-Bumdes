const business = document.querySelector('#content').dataset.business;
const token = `Bearer ${localStorage.getItem('token')}`;

const getLedger = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear, account_id) => {

    let url = `/api/${business}/ledger?search=${search}&page=${page}?&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}&account_id=${account_id}&end_week=${thisWeek}&end_month=${thisMonth}&end_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getAccounts = async (search) => {
    let url = `/api/${business}/account?search=${search}`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}