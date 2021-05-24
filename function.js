function checkpw() {
  var pw1 = document.getElementById("pw1").value;
  var pw2 = document.getElementById("pw2").value;
  if(pw1!=pw2){
    document.getElementById("check-text-pw").innerHTML = "Le password non coincidono";
    document.getElementById("register").disabled = true;
  }
  else{
    var valore=document.getElementById("pw1").value;
    var verifica=/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[!#$%&?\."]).*$/;
    if(valore.match(verifica)){
      document.getElementById("check-text-pw").innerHTML = "";
      document.getElementById("register").disabled = false;
    }
    else
    {
      document.getElementById("check-text-pw").innerHTML = "The password must contain more than 8 characters, downcase,uppercase letters,number and a characters like: '! # $ % & ? .'";
      document.getElementById("register").disabled = true;
    }
  }
}
