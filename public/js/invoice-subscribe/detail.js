async function confirmationReqest(value)
{
    value.setAttribute('disabled','disabled');
    value.innerHTML = `<div class="spinner-border-sm spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`
    try {
        let res = await paymentConfirmation(value.dataset.id);

        let message = `Pembayaran INV${res.no_ref} Berhasil Konfirmasikan, Mohon Tunggu`;
        
        myToast(message, 'success');

        const paymentStatus = document.querySelector('#payment-status');

        sendWa(res);

        paymentStatus.innerHTML = `
            : <span class="text-secondary" >Menunggu Konfirmasi</span> 
        `;

    } catch (error) {
        console.log(error);
    }

    value.removeAttribute('disabled');
    value.innerHTML = `Konfirmasi Pembayaran Via WA`;
    
}

function sendWa(data){
    //set link send to wa
    let waLink = 'https://web.whatsapp.com/send';

    let message = `*KONFIRMASI PEMBAYARAN PERPANJANG LAYANAN*%0A-------------------------------------------------------%0A%0A*No Faktur:* INV${data.no_ref}%0A*Jenis Layanan:* ${data.package == 'yearly' ? 'TAHUNAN' : 'BULANAN'}%0A*Tanggal:* ${dateReadable(data.date)}%0A*Jumlah Bayar:* Rp. ${formatRupiah(data.value.toString())}%0A*Alamat WEB:* ${window.location.hostname}%0A-------------------------------------------------------%0A_Mohon Sertakan Bukti Transfer Agar Proses Konfirmasi Dilakukan Lebih Cepat_
    `

    //value
    let content = `${waLink}?phone=${phoneAdmin}&text=${message}`;
    
    /* Whatsapp Window Open */
    window.open(content, '_blank');
}


window.addEventListener('load', function(){

})