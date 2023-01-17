const business = document.querySelector('#content').dataset.business;
const id = document.querySelector('#content').dataset.invoice;

const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (search = '') => {
    let url = `/api/${business}/no-ref-account-payable-payment-recomendation?search=${search}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getContacts = async (search) => {
    let url = `/api/${business}/account-payable?search=${search}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getInvoices = async (contact, search) => {
    let url = `/api/${business}/account-payable-by-invoice/${contact}?search=${search}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getAccounts = async (search) => {
    let url = `/api/${business}/account?search=${search}&is_cash=1`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getAccountPayablePayment = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear) => {
    let url = `/api/${business}/account-payable-payment?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getSingleAccountPayablePayment = async (id) => {
    let url = `/api/${business}/account-payable-payment/${id}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const postAccountPayablePayment = async (data) => {
    let url = `/api/${business}/account-payable-payment`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updateAccountPayablePayment = async (data, id) => {
    let url = `/api/${business}/account-payable-payment/${id}`;
    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyAccountPayablePayment = async (id) => {
    let url = `/api/${business}/account-payable-payment/${id}`;
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const showData = async () =>{
    let url = `/api/${business}/account-payable-payment/${id}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

function goToPrintAccountPayablePayment(id)
{
    window.open(`/${business}/account-payable-payment/${id}/print-detail`)
}
