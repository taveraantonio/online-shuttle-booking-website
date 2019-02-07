
function validate(){
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    
    if(email===""|| password===""){
        alert('Please enter both e-mail and password');
        event.returnValue = false;
        return false; 
    }
    
    //sanitize input string
    email = escapeHTML(email); 
    password = escapeHTML(password); 
       
	//email filter taken from chromium project  
    var emailFilter = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var lowercaseletter = /[a-z]/;
    var uppercaseletter = /[A-Z]/;
    var digit = /[0-9]/;
    
    //check requirements 
    if (!emailFilter.test(email)) {
       alert('Please enter a valid e-mail address');
       event.returnValue = false;
       return false; 
    }
    
    if (password.length < 2 || !lowercaseletter.test(password)) {
       alert('Please enter a valid password');
       event.returnValue = false;
       return false;
    }
    if(!uppercaseletter.test(password)){
       if(!digit.test(password)){
          alert('Please enter a valid password');
          event.returnValue = false;
          return false;
       }
    }
    event.returnValue=true;
    return true;
}


function escapeHTML(unsafe_str) {
    return unsafe_str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/\"/g, '&quot;')
      .replace(/\'/g, '&#39;'); 
}


function showfield1(name){
    if(name=='other1'){
        document.getElementById('div1').style.display="block";
    } else{
        document.getElementById('div1').style.display="none";
    }
}


function showfield2(name){
    if(name=='other2'){
        document.getElementById('div2').style.display="block";
    } else{
        document.getElementById('div2').style.display="none";
    }
}


function hidefield() {
    document.getElementById('div1').style.display='none';
    document.getElementById('div2').style.display='none';  
}


function validateBooking(){
    
    var e = document.getElementById("departure");
    var value = e.options[e.selectedIndex].value;
    var departureopt = escapeHTML(value).toUpperCase();
     
    e = document.getElementById("arrival");
    value = e.options[e.selectedIndex].value;
    var arrivalopt = escapeHTML(value).toUpperCase();
       
    var other1 = document.getElementById("other1text").value;
    other1 = escapeHTML(other1).toUpperCase();
    
    var other2 = document.getElementById("other2text").value; 
    other2 = escapeHTML(other2).toUpperCase();
    
    
    
    var n; 
    if(other1!==""){
        if(other2!==""){
             n = other1.localeCompare(other2);
        }else{
             n = other1.localeCompare(arrivalopt);
        }
    }else{
        if(other2!==""){
             n = departureopt.localeCompare(other2);
        }else{
             n = departureopt.localeCompare(arrivalopt);
        }
    }
    
    if(n>=0){
        alert('Please enter an Arrival > Departure');
        event.returnValue = false;
        return false;  
    }else{
        event.returnValue = true;
        return true; 
    }
    
    
}


function checkCookie() {
      var x = navigator.cookieEnabled;
      if (x==false) {
        window.alert("Please enable cookies if you want to visit this web site. You have been redirecting..");
        window.location.href = "https://www.whatismybrowser.com/guides/how-to-enable-cookies/";
      }                                                                  
}