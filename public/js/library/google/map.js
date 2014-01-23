define('googleMap',
	['jquery', 'underscore', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
	function($, _) {

        function Map(options) {

            self.markers = [];

            self.events = [];

            var settings = {
                mapContainer: 'map_canvas', // only element ID
                // google map settings
                mapCenter: {
                    lat: null,
                    lng: null
                },
                mapZoom: 14,
                mapTypeId: 'ROADMAP', // https://developers.google.com/maps/documentation/javascript/reference?hl=en#MapTypeId
                // marker clusterer settings
                mcGridSize: 50,
                mcMaxZoom: 15
            };

            settings = _.extend(settings, options);

            var lat = $.cookie('lastLat');
            var lng = $.cookie('lastLng');
            var position = null;

            if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
                position = new google.maps.LatLng(lat, lng);
            } else {
                position = new google.maps.LatLng(settings.mapCenter.lat, settings.mapCenter.lng)
            }

            var mapOptions = {
                center: position,
                zoom: settings.mapZoom,
                mapTypeId: eval('google.maps.MapTypeId.' + settings.mapTypeId)
            };

            var Map = new google.maps.Map(document.getElementById(settings.mapContainer), mapOptions);

            Map.markers = self.markers;
            Map.events = self.events;

            return Map;
        }

        return Map;

    }
);