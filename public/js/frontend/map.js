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
	'jCookie',	
	'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places',
	'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js',
	'http://connect.facebook.net/en_US/all.js#xfbml=1&appId=166657830211705'
	], 
	function($, frontTopPanel, fb, gmap, gmapEvents, utils, noti) {
		var locationElem = $('#current_location');
        noti.init();
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
		fb.init({
            		appId: '166657830211705',
		            status: true
		        }); 

		if ($('#conflict_location').length > 0) {
			noti.createNotification('Your location from Facebook does not match to location from IP. Please confirm your location in <a href="/profile">profile</a> settings.', 'warning');
		}
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);