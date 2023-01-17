const business = document.querySelector('#content').dataset.business;
const id = document.querySelector('#content').dataset.accountReceivable;

const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (search = '') => {
    let url = `/api/${business}/no-ref-account-recevable-recomendation?search=${search}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response;
}

const getContacts = async (search) => {
    let url = `/api/contact?search=${search}&type=Customer`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getAccounts = async (search) => {
    let url = `/api/${business}/account?search=${search}&payment=1`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getAccountReceivable = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear) => {
    let url = `/api/${business}/account-receivable?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getSingleAccountReceivable = async (id) => {
    let url = `/api/${business}/account-receivable/${id}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const postAccountReceivable = async (data) => {
    let url = `/api/${business}/account-receivable`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updateAccountReceivable = async (data) => {
    let url = `/api/${business}/account-receivable/${id}`;
    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyAccountReceivable = async (id) => {
    let url = `/api/${business}/account-receivable/${id}`;
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const showData = async () =>{
    let url = `/api/${business}/account-receivable/${id}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

function goToPintAccountReceivable(id)
{
    window.open(`/${business}/account-receivable/${id}/print-detail`)
}
