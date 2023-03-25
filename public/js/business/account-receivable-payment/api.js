const business = document.querySelector('#content').dataset.business;
const businessName = document.querySelector('#content').dataset.businessName;
const identity = document.querySelector('#content').dataset.identity;
const id = document.querySelector('#content').dataset.invoice;

const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (search = '') => {
    let url = `/api/${business}/no-ref-account-receivable-payment-recomendation?search=${search}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getContacts = async (search) => {
    let url = `/api/${business}/account-receivable?search=${search}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getInvoices = async (contact, search) => {
    let url = `/api/${business}/account-receivable-by-invoice/${contact}?search=${search}`;
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

const getAccountReceivablePayment = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear) => {
    let url = `/api/${business}/account-receivable-payment?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getSingleAccountReceivablePayment = async (id) => {
    let url = `/api/${business}/account-receivable-payment/${id}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const postAccountReceivablePayment = async (data) => {
    let url = `/api/${business}/account-receivable-payment`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updateAccountReceivablePayment = async (data, id) => {
    let url = `/api/${business}/account-receivable-payment/${id}`;
    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyAccountReceivablePayment = async (id) => {
    let url = `/api/${business}/account-receivable-payment/${id}`;
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const showData = async () =>{
    let url = `/api/${business}/account-receivable-payment/${id}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

function goToPrintAccountReceivablePayment(id)
{
    window.open(`/${business}/account-receivable-payment/${id}/print-detail`)
}
