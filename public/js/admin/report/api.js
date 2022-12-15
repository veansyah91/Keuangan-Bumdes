const token = `Bearer ${localStorage.getItem('token')}`;

const getData = async (url) => {
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}