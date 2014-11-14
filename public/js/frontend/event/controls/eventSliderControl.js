define('eventSliderControl',
	['jquery', 'utils', 'idangerous', 'domReady'],
	function($, utils, idangerous) {
	
		function eventSliderControl($, utils, idangerous) 
		{
			var self = this;
			
			self.settings = {
				sliderContainer: '.js-b-gallery-slider',
				sliderContainerClass: 'swiper-container',
				sliderElem: '.js-b-gallery-slider-slide',
				sliderElemClass: 'swiper-slide',
				sliderArrowPrev: '.js-b-gallery-arrow-prev',
				sliderArrowNext: '.js-b-gallery-arrow-next',
			},
			self.swipeGallery = null,

			
			self.init = function()
			{
				$(self.settings.sliderContainer).addClass(self.settings.sliderContainerClass);
			    $(self.settings.sliderElem)
			        .addClass(self.settings.sliderElemClass)
			        .wrapAll("<div class='swiper-wrapper'></div>");

			    self.swipeGallery = new Swiper(self.settings.sliderContainer, {
			        calculateHeight: true,
			        // cssWidthAndHeight: true,
			        mousewheelControl: true,
			        mousewheelControlForceToAxis: true,
			        preventLinksPropagation: true,
			        slidesPerView: 'auto'
			    });
			    
				self.bindClicks();
			},
			
			
			self.bindClicks = function()
			{
			    $(self.settings.sliderArrowPrev).on('click', function(e){
			        self.swipeGallery.swipePrev();
			        e.preventDefault();
			    });

			    $(self.settings.sliderArrowNext).on('click', function(e){
			    	self.swipeGallery.swipeNext();
			        e.preventDefault();
			    }); 
			}
		};
		
		return new eventSliderControl($, utils, idangerous);
	}
);