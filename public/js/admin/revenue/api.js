const token = `Bearer ${localStorage.getItem('token')}`;

const getContacts = async (search) => {
    let url = `/api/contact?search=${search}`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getAccounts = async (search, is_cash) => {
    let url = `/api/account?search=${search}&is_cash=${is_cash}`

    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getRevenue = async (url) => {
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getNewNoRefContact = async () => {
    let url = `/api/no-ref-revenue-recomendation`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const postRevenue = async (data) => {
    let url = `/api/revenue`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const putRevenue = async (data, id) => {
    let url = `/api/revenue/${id}`;

    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyRevenue = async (id) => {
    let url = `/api/revenue/${id}`;
    
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}