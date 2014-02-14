require([
    'jquery',
    'frontTopPanel',
    'fb',
    'googleMap',
    'newGmapEvents',
    'googleMc',
    'utils',
    'noti',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, googleMap, newGmapEvents, googleMc, utils, noti) {
        noti.init();
        var locationElem = $('#current_location');

        console.log('type');
        console.log(typeof googleMap);

        var map = new googleMap({
            mapCenter: {
                lat: locationElem.attr('latitude'),
                lng: locationElem.attr('longitude')
            },
            mapZoom: $('#isMobile').val() === '1' ? 20 : 15
        });

        var Mc = new googleMc({
            Map: map,
            markers: []
        });

        var newGmapEvents = new newGmapEvents(map, Mc);

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        if ($('#conflict_location').length > 0) {
//            noti.createNotification('Your location from Facebook does not match to location from IP. Please confirm your location in <a href="/profile">profile</a> settings.', 'warning');
            noti.createNotification('Your current location does not match with your facebook profile location. Set your <a href="/profile">default location</a> for Eventweekly', 'warning');
        }

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);

/*
require([
	'jquery',
	'frontTopPanel',
	'fb',
	'gmap',
	'gmapEvents',
	'utils',
	'noti',
	'domReady',		
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, gmap, gmapEvents, utils, noti) {
		noti.init();
		var locationElem = $('#current_location');
		gmap.init({
	                mapCenter: {
	                    lat: locationElem.attr('latitude'),
	                    lng: locationElem.attr('longitude')
	                }
	        	});  
	
		gmapEvents.init();

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'
				});
		fb.init();

		if ($('#conflict_location').length > 0) {
			noti.createNotification('Your location from Facebook does not match to location from IP. Please confirm your location in <a href="/profile">profile</a> settings.', 'warning');
		}
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);*/
