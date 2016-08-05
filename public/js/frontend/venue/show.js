require([
	'jquery',
	'fb',
	'googleMap',
	'noty',
    'idangerous',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	],
	function($, fb, googleMap, noty) {
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
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);