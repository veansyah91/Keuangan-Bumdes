const token = `Bearer ${localStorage.getItem('token')}`;

const newRef = async (search = '') => {
    let url = `/api/no-ref-journal-recomendation?search=${search}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response;
}

const getAccounts = async (search) => {
    let url = `/api/account?search=${search}`
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const getJournal = async (search = '', page, date_from, date_to, thisWeek, thisMonth, thisYear) => {
    let url = `/api/journal?search=${search}&page=${page}&date_from=${date_from}&date_to=${date_to}&this_week=${thisWeek}&this_month=${thisMonth}&this_year=${thisYear}`;
    
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const getSingleJournal = async (id) => {
    let url = `/api/journal/${id}`;
    let response = await axios.get(url, {
        headers:{
            Authorization : token
        }
    })

    return response.data.data;
}

const postJournal = async (data) => {
    let url = `/api/journal`;
    let result = await axios.post(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const updateJournal = async (data, id) => {
    let url = `/api/journal/${id}`;
    let result = await axios.put(url, data, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}

const destroyJournal = async (id) => {
    let url = `/api/journal/${id}`;
    let result = await axios.delete(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}