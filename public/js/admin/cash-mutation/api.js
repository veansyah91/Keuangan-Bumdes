const token = `Bearer ${localStorage.getItem('token')}`;

const getCashMutation = async (url) => {
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
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

const getNewNoRef = async () => {
    let url = `/api/no-ref-cash-mutation-recomendation`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const postMutationCash = async (data) => {
    let url = `/api/cash-mutation`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const putCashMutation = async (data, id) => {
    let url = `/api/cash-mutation/${id}`;

    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyCashMutation = async (id) => {
    let url = `/api/cash-mutation/${id}`;
    
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}