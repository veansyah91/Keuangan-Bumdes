const business = document.querySelector('#content').dataset.business;
const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (search = '') => {
    let url = `/api/${business}/no-ref-product-recomendation?search=${search}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response;
}

const getContacts = async (search) => {
    let url = `/api/contact?search=${search}&type=supplier`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response;
}

const getCategories = async (search) => {
    let url = `/api/${business}/category?search=${search}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response;
}

const getCategory = async (search) => {
    let url = `/api/${business}/category?search=${search}`;

    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const postCategory = async (data) => {
    let url = `/api/${business}/category`;
    let formData = {
        nama : data
    }

    let result = await axios.post(url, formData, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyCategory = async (id) => {
    let url = `/api/${business}/category/${id}`;

    let result = await axios.delete(url,{
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const postProduct = async(data) => {
    let url = `/api/${business}/product`;

    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updateProduct = async(id, data) => {
    let url = `/api/${business}/product/${id}`;

    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyProduct = async(id) => {
    let url = `/api/${business}/product/${id}`;

    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getProduct = async (url) => {
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}