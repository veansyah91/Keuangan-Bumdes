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

const getAccounts = async (search, is_cash) => {
    let url = `/api/${business}/account?search=${search}&is_cash=${is_cash}`

    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getNewNoRef = async (url) => {
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    });
    return result.data.data;
}

const postData= async (data) => {
    let url = `/api/${business}/credit-application`;
    
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const putData= async (data, id) => {
    let url = `/api/${business}/credit-application/${id}`;

    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyData= async (id) => {
    let url = `/api/${business}/credit-application/${id}`;
    
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}
