require([
	'jquery',
    'eventSliderControl',
    'idangerous',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	],
	function($, eventSliderControl) {
        eventSliderControl.init({
			sliderPagingType: 'arrow'
		}, {
			sliderContainer: '.js-b-gallery-slider',
			sliderContainerClass: 'swiper-container',
			sliderElem: '.js-b-gallery-slider-slide',
			sliderElemClass: 'swiper-slide',
			sliderArrowPrev: '.js-b-gallery-arrow-prev',
			sliderArrowNext: '.js-b-gallery-arrow-next',
		}, {
	        calculateHeight: true,
	        mousewheelControl: true,
	        mousewheelControlForceToAxis: true,
	        preventLinksPropagation: true,
	        slidesPerView: 'auto'
	    });
	}
);