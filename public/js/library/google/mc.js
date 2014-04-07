define('googleMc',
	['jquery', 'underscore', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
	function($, _) {

        function Mc(options) {

            var MC = null;

            var settings = {
                Map: null,
                markers: null,
                mcGridSize: 50,
                mcMaxZoom: 15
            };

            settings = _.extend(settings, options);

            if (!_.isNull(settings.Map) && !_.isNull(settings.markers)) {
                //console.log('Initialize marker clusterer');
                MC = new MarkerClusterer(settings.Map, settings.markers, {gridSize: settings.mcGridSize, maxZoom: settings.mcMaxZoom});
            }

            return MC;

        }

        return Mc;

	}
);