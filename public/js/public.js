const setCurrencyFormat = (value) => {
    if (!value.value) {
        value.value = 0;
    }

    let temp = toPrice(value.value);

    value.value = formatRupiah(temp.toString());
}

function toPrice(angka) {
    return angka.replace(/[^0-9]/g, '');
}

function formatRupiah(angka, prefix) {
    var number_string = angka.replace(/[^,\d]/g, "").toString(),
      split = number_string.split(","),
      sisa = split[0].length % 3,
      rupiah = split[0].substr(0, sisa),
      ribuan = split[0].substr(sisa).match(/\d{3}/gi);
  
    // tambahkan titik jika yang di input sudah menjadi angka ribuan
    if (ribuan) {
      separator = sisa ? "." : "";
      rupiah += separator + ribuan.join(".");
    }
  
    rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
    return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
  }

function dateNow(){
    var date = new Date();

    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();

    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    return year + "-" + month + "-" + day;
}

function addMonths(date, months) {
    date.setMonth(date.getMonth() + months);
  
    return date;
}

const waktu = (dateInputType=false) => {
    date = new Date();
    millisecond = date.getMilliseconds();
    detik = date.getSeconds();
    menit = date.getMinutes();
    jam = date.getHours();
    hari = date.getDay();
    tanggal = date.getDate();
    bulan = date.getMonth();
    tahun = date.getFullYear();
    return dateInputType ? `${tahun}-${bulan+1}-${tanggal} ${jam}:${menit}:${detik}` : `${tanggal}/${bulan+1}/${tahun} ${jam}:${menit}:${detik}`;
}

const monthReadable = (m) => {
    switch (parseInt(m)) {
        case 1:
            return 'Jan'
            break;
        case 2:
            return 'Febr'
            break;
        case 3:
            return 'Mar'
            break;
        case 4:
            return 'Apr'
            break;
        case 5:
            return 'Mei'
            break;
        case 6:
            return 'Juni'
            break;
        case 7:
            return 'Juli'
            break;
        case 8:
            return 'Agust'
            break;
        case 9:
            return 'Sept'
            break;
        case 10:
            return 'Okt'
            break;
        case 11:
            return 'Nov'
            break;
        default:
            return 'Des'
            break;
    }
}

const dateReadable = (date) => {
    let dateArr = date.split('-');

    let year = dateArr[0];

    let month = monthReadable(dateArr[1]);

    let day = dateArr[2];

    return `${month}, ${day} ${year}`
}

function capital(str)
{
    return str.replace(/\w\S*/g, function(kata){ 
        const kataBaru = kata.slice(0,1).toUpperCase() + kata.substr(1);
        return kataBaru
    });
}

function abbreviation(str) {
    let strArr = str.split(' ');
    let result = '';
    for (let i = 0; i < strArr.length; i++) {
        result += strArr[i].slice(0,1);
    }
    return result; 
}

function myToast(message, status) {
    // Get the snackbar DIV
    var x = document.getElementById("tost-message");
  
    // Add the "show" class to DIV
    if (status == 'success') {
        x.style.backgroundColor = '#198754'
    }

    if (status == 'danger') {
        x.style.backgroundColor = '#DD3832'
    }
    x.className = "show";
    x.innerHTML = message;
  
    // After 3 seconds, remove the show class from DIV
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
  }