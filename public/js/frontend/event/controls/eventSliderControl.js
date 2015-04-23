define('eventSliderControl',
    ['jquery', 'utils', 'idangerous', 'domReady'],
    function($, utils, idangerous) {

        function eventSliderControl($, utils, idangerous)
        {
            var self = this;

            self.init = function(slidingOptions, controlOptions, swiperOptions)
            {
                self.settings = controlOptions;

                $(controlOptions.sliderContainer).addClass(controlOptions.sliderContainerClass);
                $(controlOptions.sliderElem)
                    .addClass(controlOptions.sliderElemClass)
                    .wrapAll("<div class='swiper-wrapper'></div>");

                swipeGallery = new Swiper(self.settings.sliderContainer, swiperOptions);

                if (slidingOptions.sliderPagingType == 'arrow') {
                    self.bindArrowClicks(swipeGallery);
                }
                return swipeGallery;
            },

                self.bindArrowClicks = function(swipeGallery)
                {
                    $(self.settings.sliderArrowPrev).on('click', function(e){
                        swipeGallery.swipePrev();
                        e.preventDefault();
                    });

                    $(self.settings.sliderArrowNext).on('click', function(e){
                        swipeGallery.swipeNext();
                        e.preventDefault();
                    });
                }
        };

        return new eventSliderControl($, utils, idangerous);
    }
);