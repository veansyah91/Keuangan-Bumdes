const business = document.querySelector('#content').dataset.business;
const id = document.querySelector('#content').dataset.inventoryAdjustment;

const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (search = '') => {
    let url = `/api/${business}/no-ref-inventory-adjustment-recomendation?search=${search}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response;
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

const getProducts = async (search) => {
    let url = `/api/${business}/product?search=${search}&stock_check=true`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getInventoryAdjustment = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear) => {
    let url = `/api/${business}/inventory-adjustment?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getSingleInventoryAdjustment = async (id) => {
    let url = `/api/${business}/inventory-adjustment/${id}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const postInventoryAdjustment = async (data) => {
    let url = `/api/${business}/inventory-adjustment`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updateInventoryAdjustment = async (data) => {
    let url = `/api/${business}/inventory-adjustment/${id}`;
    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyInventoryAdjustment = async (id) => {
    let url = `/api/${business}/inventory-adjustment/${id}`;
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const showData = async () =>{
    let url = `/api/${business}/inventory-adjustment/${id}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}