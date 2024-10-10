// Texto Animado
var typed = new Typed('#typed-text', {
    strings: ["EXPOSICION DE PROVEEDURIA", "ENCUENTRO DE NEGOCIOS", "CONFERENCIAS", "TALLERES", "NETWORKING"],
    typeSpeed: 150,
    backSpeed: 90,
    loop: true
});

// Contador
var countDownDate = new Date("Nov 7, 2024 00:00:00").getTime();

var x = setInterval(function() {
    var now = new Date().getTime();
    var distance = countDownDate - now;

    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById("days").innerHTML = days;
    document.getElementById("hours").innerHTML = hours;
    document.getElementById("minutes").innerHTML = minutes;
    document.getElementById("seconds").innerHTML = seconds;

    if (distance < 0) {
        clearInterval(x);
        document.getElementById("countdown").innerHTML = "EXPIRED";
    }
}, 1000);
