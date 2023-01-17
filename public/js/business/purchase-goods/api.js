const business = document.querySelector('#content').dataset.business;
const id = document.querySelector('#content').dataset.purchaseGoods;

const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (search = '') => {
    let url = `/api/${business}/no-ref-purchase-goods-recomendation?search=${search}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response;
}

const getContacts = async (search) => {
    let url = `/api/contact?search=${search}&type=Supplier`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getAccounts = async (search) => {
    let url = `/api/${business}/account?search=${search}&payable=1`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getProducts = async (search) => {
    let url = `/api/${business}/product?search=${search}&stock_check=1`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getPurchaseGoods = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear) => {
    let url = `/api/${business}/purchase-goods?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getSinglePurchaseGoods = async (id) => {
    let url = `/api/${business}/purchase-goods/${id}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const postPurchaseGoods = async (data) => {
    let url = `/api/${business}/purchase-goods`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updatePurchaseGoods = async (data) => {
    let url = `/api/${business}/purchase-goods/${id}`;
    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyPurchaseGoods = async (id) => {
    let url = `/api/${business}/purchase-goods/${id}`;
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const showData = async () =>{
    let url = `/api/${business}/purchase-goods/${id}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

function goToPintPurchaseGoods(id)
{
    window.open(`/${business}/purchase-goods/${id}/print-detail`)
}
