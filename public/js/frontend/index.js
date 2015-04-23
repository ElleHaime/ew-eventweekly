require([
        'jquery',
        'fb',
        'noty',
        'eventSliderControl',
        'frontEventLike',
        'idangerous',
        'utils',
        'domReady',
        'underscore',
        'jCookie'
    ],
    function($, fb, noty, eventSliderControl, frontEventLike) {
        fb.init();
        frontEventLike.init();

        // initials slides per view
        var slidesPerViewFeatured = 3;
        var slidesPerViewTrending = 4;
        if($(window).width()<800) {
            slidesPerView = 1;
        }

        // paid featured events
        eventSliderControl.init({
            sliderPagingType: 'dot'
        }, {
            sliderContainer: '.js-main-popular-events-slider',
            sliderContainerClass: 'swiper-container',
            sliderElem: '.js-main-popular-events-slider-slide',
            sliderElemClass: 'swiper-slide',
            sliderPagination: '.js-main-popular-events-slider-dots',
        }, {
            calculateHeight: true,
            slidesPerView: "1",
            preventLinks: false,
            mousewheelControl: true,
            mousewheelControlForceToAxis: true,
            preventLinksPropagation: true,
            pagination: ".js-main-popular-events-slider-dots",
            paginationClickable: 1
        });


        // simple featured events
        var sliderFeatured= eventSliderControl.init({
            sliderPagingType: 'arrow'
        }, {
            sliderContainer: '.list-of-events__container.featured',
            sliderContainerClass: 'swiper-container',
            sliderElem: '.pure-u-1-3',
            sliderElemClass: 'swiper-slide',
            sliderArrowPrev: '#list-of-events-featured-prev',
            sliderArrowNext: '#list-of-events-featured-next',
        }, {
            calculateHeight: true,
            mousewheelControl: true,
            mousewheelControlForceToAxis: true,
            preventLinksPropagation: true,

            slidesPerView: slidesPerViewFeatured
        });

        // trending events
        var sliderTrending= eventSliderControl.init({
            sliderPagingType: 'arrow'
        }, {
            sliderContainer: '.list-of-events__container.trending',
            sliderContainerClass: 'swiper-container',
            sliderElem: '.list-of-events__item.pure-u-1-4',
            sliderElemClass: 'swiper-slide',
            sliderArrowPrev: '#list-of-events-trending-prev',
            sliderArrowNext: '#list-of-events-trending-next'
        }, {
            calculateHeight: true,
            mousewheelControl: true,
            mousewheelControlForceToAxis: true,
            preventLinksPropagation: true,
            slidesPerView: slidesPerViewTrending
        });

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }


        // change slides per view with browser width
        $(window).resize(function() {
            var browserWidthResize = $(window).width();
            if (browserWidthResize < 800) {
                sliderFeatured.params.slidesPerView=1;
                sliderTrending.params.slidesPerView=1;
            } else {
                sliderFeatured.params.slidesPerView=3;
                sliderTrending.params.slidesPerView=4;
            }
        });


    }
);