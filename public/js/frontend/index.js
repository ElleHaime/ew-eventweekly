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
	        paginationClickable: true
	    });
		
		
		// simple featured events		
		eventSliderControl.init({
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
	        slidesPerView: 3
	    });
		
		// trending events		
		eventSliderControl.init({
			sliderPagingType: 'arrow'
		}, {
			sliderContainer: '.list-of-events__container.trending',
			sliderContainerClass: 'swiper-container',
			sliderElem: '.list-of-events__item.pure-u-1-4',
			sliderElemClass: 'swiper-slide',
			sliderArrowPrev: '#list-of-events-trending-prev',
			sliderArrowNext: '#list-of-events-trending-next',
		}, {
			calculateHeight: true,
	        mousewheelControl: true,
	        mousewheelControlForceToAxis: true,
	        preventLinksPropagation: true,
	        slidesPerView: 3
	    });   
		
			
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);




