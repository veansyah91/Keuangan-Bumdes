const business = document.querySelector('#content').dataset.business;
const id = document.querySelector('#content').dataset.accountReceivable;

const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (search = '') => {
    let url = `/api/${business}/no-ref-account-payable-recomendation?search=${search}`;
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

const getAccountPayable = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear) => {
    let url = `/api/${business}/account-payable?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getSingleAccountPayable = async (id) => {
    let url = `/api/${business}/account-payable/${id}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const postAccountPayable = async (data) => {
    let url = `/api/${business}/account-payable`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updateAccountPayable = async (data) => {
    let url = `/api/${business}/account-payable/${id}`;
    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyAccountPayable = async (id) => {
    let url = `/api/${business}/account-payable/${id}`;
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const showData = async () =>{
    let url = `/api/${business}/account-payable/${id}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

function goToPintAccountPayable(id)
{
    window.open(`/${business}/account-payable/${id}/print-detail`)
}
