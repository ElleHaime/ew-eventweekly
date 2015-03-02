require([
	'jquery',
	'fb',
	'googleMap',
	'noty',
    'frontEventInviteFriend',
    'eventSliderControl',
    'frontEventLike',
    'idangerous',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	],
	function($, fb, googleMap, noty, frontEventInviteFriend, eventSliderControl, frontEventLike) {
    	var eCoords = $('#map_canvas');
    	var eAddress = $('#map_info');
		
		var map = new googleMap({
		    mapCenter: {
		        lat: eCoords.attr('latitude'),
		        lng: eCoords.attr('longitude')
		    },
		    mapZoom: $('#isMobile').val() === '1' ? 17 : 15
		});
		var newLatLng = new google.maps.LatLng(eCoords.attr('latitude'), eCoords.attr('longitude'));
		var infowindow = new google.maps.InfoWindow({
		      content: eAddress.attr('info') 
		});
		
		marker = new google.maps.Marker({
            position: newLatLng,
            map: map
        });
		google.maps.event.addListener(marker, 'click', function() {
		    infowindow.open(map, marker);
		}); 

		fb.init(); 
        frontEventInviteFriend.init();
        frontEventLike.init();
        
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
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);