define('googleMc',
//	['jquery', 'underscore', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
//	['jquery', 'underscore', 'domReady'],
	['jquery', 'underscore', 'googleMarkerClusterer', 'domReady'],
	function($, _, googleMarkerClusterer) {
        function Mc(options) {

            var MC = null;

            var settings = {
                Map: null,
                markers: [],
                mcGridSize: 50,
                mcMaxZoom: 15,
                styles: [
                     {
	                     height: 53,
	                     url: "/img/google/m1.png",
	                     width: 53
                     },
                     {
	                     height: 56,
	                     url: "/img/google/m2.png",
	                     width: 56
                     },
                     {
	                     height: 66,
	                     url: "/img/google/m3.png",
	                     width: 66
                     },
                     {
	                     height: 78,
	                     url: "/img/google/m4.png",
	                     width: 78
                     },
                     {
	                     height: 90,
	                     url: "/img/google/m5.png",
	                     width: 90
                     }
                   ]
            };

            settings = _.extend(settings, options);

            if (!_.isNull(settings.Map) && !_.isNull(settings.markers)) {
//console.log('Initialize marker clusterer in googleMC');
                MC = new MarkerClusterer(settings.Map, settings.markers, {gridSize: settings.mcGridSize, maxZoom: settings.mcMaxZoom, styles: settings.styles});
            }

            return MC;

        }

        return Mc;

	}
);