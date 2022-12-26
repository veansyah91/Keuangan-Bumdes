function logoutSubmit(){
    event.preventDefault(); 
    localStorage.removeItem('token');
    localStorage.removeItem('position');
    localStorage.removeItem('userPrintPage');
    localStorage.removeItem('locationPrint');
    document.getElementById('logout-form').submit();
}

window.onclick = function(event) {
    if (!event.target.matches('.search-input-dropdown')) {
      var dropdowns = document.getElementsByClassName("search-select");
      var i;
      for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
          openDropdown.classList.add('d-none');
      }
    }
  }