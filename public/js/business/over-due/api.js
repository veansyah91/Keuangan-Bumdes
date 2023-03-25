const token = `Bearer ${localStorage.getItem('token')}`;
const business = document.querySelector('#content').dataset.business;

const getData = async (url) => {
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const updateData= async (url) => {
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return response.data.data;
}

const getAccounts = async (search, is_cash) => {
    let url = `/api/${business}/account?search=${search}&is_cash=${is_cash}`

    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyData= async (id) => {
    let url = `/api/${business}/over-due/${id}`;
    
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}
