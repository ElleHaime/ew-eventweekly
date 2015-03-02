$(function() {
    

    $(".js-main-popular-events-slider").addClass("swiper-container");
    $(".js-main-popular-events-slider-slide")
        .addClass("swiper-slide")
        .wrapAll("<div class='swiper-wrapper'></div>");


    var swipeGallery = new Swiper('.js-main-popular-events-slider', {
        calculateHeight: true,
        slidesPerView: "1",
        preventLinks: false,
        mousewheelControl: true,
        mousewheelControlForceToAxis: true,
        preventLinksPropagation: true,
        pagination: ".js-main-popular-events-slider-dots",
        paginationClickable: true
    });

});