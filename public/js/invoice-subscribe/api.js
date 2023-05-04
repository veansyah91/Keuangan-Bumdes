const token = `Bearer ${localStorage.getItem('token')}`;

const phoneAdmin = '6287839542505';

const paymentConfirmation = async (id) => {
    let url = `/api/invoice-subscribe/${id}/payment-confirmation`;
    let result = await axios.get(url, {
        headers:{
            Authorization : token
        }
    }) 

    return result.data.data;
}