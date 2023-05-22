const business = document.querySelector('#content').dataset.business;
const id = document.querySelector('#content').dataset.stockOpname;

const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (date = '') => {
    let url = `/api/${business}/no-ref-stock-opname-recomendation?date=${date}`;
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
    let url = `/api/${business}/product-stock-opname?search=${search}&stock_check=true`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getStockOpname = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear) => {
    let url = `/api/${business}/stock-opname?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getSingleStockOpname = async (id) => {
    let url = `/api/${business}/stock-opname/${id}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const postStockOpname = async (data) => {
    let url = `/api/${business}/stock-opname`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updateStockOpname = async (data) => {
    let url = `/api/${business}/stock-opname/${id}`;
    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyStockOpname = async (id) => {
    let url = `/api/${business}/stock-opname/${id}`;
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const showData = async () =>{
    let url = `/api/${business}/stock-opname/${id}`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}