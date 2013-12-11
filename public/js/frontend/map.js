require([
	'jquery',
	'frontTopPanel',
	'fb',
	'gmap',
	'gmapEvents',
	'utils',
	'domReady',		
	'underscore',
	'jCookie',	
	'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places',
	'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js',
	'http://connect.facebook.net/en_US/all.js#xfbml=1&appId=423750634398167'
	], 
	function($, frontTopPanel, fb, gmap, gmapEvents, utils) {
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
		fb.init({
		            appId: '303226713112475', //'423750634398167',
		            status: true
		        }); 
	}
);