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

const getExpense = async (url) => {
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getNewNoRefContact = async () => {
    let url = `/api/no-ref-expense-recomendation`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const postExpense = async (data) => {
    let url = `/api/expense`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const putExpense = async (data, id) => {
    let url = `/api/expense/${id}`;

    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyExpense = async (id) => {
    let url = `/api/expense/${id}`;
    
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}