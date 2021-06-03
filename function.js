function checkpw() {
  var pw1 = document.getElementById("pw1").value;
  var pw2 = document.getElementById("pw2").value;
  if (pw1 != pw2) {
    document.getElementById("check-text-pw").innerHTML = "Le password non coincidono";
    document.getElementById("register").disabled = true;
  } else {
    var valore = document.getElementById("pw1").value;
    var verifica = /^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[!#$%&?\."]).*$/;
    if (valore.match(verifica)) {
      document.getElementById("check-text-pw").innerHTML = "";
      document.getElementById("register").disabled = false;
    } else {
      document.getElementById("check-text-pw").innerHTML = "The password must contain more than 8 characters, downcase,uppercase letters,number and a characters like: '! # $ % & ? .'";
      document.getElementById("register").disabled = true;
    }
  }
}
document.addEventListener('DOMContentLoaded', function() {

  // Get all "navbar-burger" elements
  var $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

  // Check if there are any nav burgers
  if ($navbarBurgers.length > 0) {

    // Add a click event on each of them
    $navbarBurgers.forEach(function($el) {
      $el.addEventListener('click', function() {

        // Get the target from the "data-target" attribute
        var target = $el.dataset.target;
        var $target = document.getElementById(target);

        // Toggle the class on both the "navbar-burger" and the "navbar-menu"
        $el.classList.toggle('is-active');
        $target.classList.toggle('is-active');

      });
    });
  }

});
