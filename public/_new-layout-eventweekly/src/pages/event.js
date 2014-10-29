$(function() {
    

    $(".js-b-gallery-slider").addClass("swiper-container");
    $(".js-b-gallery-slider-slide")
        .addClass("swiper-slide")
        .wrapAll("<div class='swiper-wrapper'></div>");


    var swipeGallery = new Swiper('.js-b-gallery-slider', {
        calculateHeight: true,
        // cssWidthAndHeight: true,
        mousewheelControl: true,
        mousewheelControlForceToAxis: true,
        preventLinksPropagation: true,
        slidesPerView: 'auto'
    });


    $('.js-b-gallery-arrow-prev').on('click', function(e){
        swipeGallery.swipePrev();
        e.preventDefault();
    });

    $('.js-b-gallery-arrow-next').on('click', function(e){
        swipeGallery.swipeNext();
        e.preventDefault();
    });

});