

$('#feel-the-wave').wavify({
    height: 60,
    bones: 7,
    amplitude: 90,
    color: 'rgba(0, 0, 0, 0.1)',
    speed: .11
});
$('#feel-the-wave-two').wavify({
    height: 150,
    bones: 8,
    amplitude: 70,
    color: 'rgba(0, 0, 0, 0.1)',
    speed: .14
});
$('#feel-the-wave-three').wavify({
    height: 10,
    bones: 5,
    amplitude: 80,
    color: 'rgba(0, 0, 0, 0.1)',
    speed: 0.16
});

$(document).ready(function (){
    $('.co_cool_features').slick({
        infinite: true,
        slidesToShow: 5,
        variableWidth: false,
        autoplay: true,
        autoplaySpeed: 2000,
        arrows: false,
        speed: 900,
        responsive: [{
            breakpoint: 992,
            settings:
            {
                slidesToShow: 4
            }
        },
        {
            breakpoint: 768,
            settings:
            {
                slidesToShow: 3
            }
        },
        {
            breakpoint: 520,
            settings:
            {
                slidesToShow: 2
            }
        },
        {
            breakpoint: 420,
            settings:
            {
                slidesToShow: 1
            }
        }]
    });

    $('.co_reviews').slick({
        infinite: true,
        slidesToShow: 1,
        variableWidth: false,
        autoplay: true,
        autoplaySpeed: 5000,
        speed: 900
    });
});



$("#monthly").click(function(){
   $("#one-time-section").hide();
   $("#monthly-section").show();
   $("#monthly").addClass("price-btn-bg");
   $("#one-time").removeClass("price-btn-bg");

});


$("#one-time").click(function(){
  $("#monthly-section").hide();
  $("#one-time-section").show();
  $("#one-time").addClass("price-btn-bg");
  $("#monthly").removeClass("price-btn-bg");
  

});