define('gmap', 
	['jquery', 'underscore', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
	function($) {	
		function gmap($) {
			var self = this;

			self.settings = {
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
		    },
		    self.Map = null,
		    self.MC = null, // MarkerClusterer http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/docs/reference.html
		    self.markers = [],

			self.init = function(options)
			{
				self.settings = _.extend(self.settings, options);

		        // try initialize map
		        if (_.once(self.__initializeMap())) {
		            // initialize clusterer if map was created
		            self.__initializeMarkerClusterer();
		        } 
			}

			self.__initializeMap = function() {
		        var lat = $.cookie('lastLat');
		        var lng = $.cookie('lastLng');
		        var position = null;

		        if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
		            position = new google.maps.LatLng(lat, lng);
		        } else {
		            position = new google.maps.LatLng(self.settings.mapCenter.lat, self.settings.mapCenter.lng)
		        }

		        var mapOptions = {
		            center: position,
		            zoom: self.settings.mapZoom,
		            mapTypeId: eval('google.maps.MapTypeId.' + self.settings.mapTypeId)
		        };

		        // create map
		        self.Map = new google.maps.Map(document.getElementById(self.settings.mapContainer), mapOptions);

                self.Map.markers = [];
                self.Map.events = [];

		        return _.isNull(self.Map);
		    } 

		    self.__initializeMarkerClusterer = function() {
                //console.log('Initialize marker clusterer');
		        self.MC = new MarkerClusterer(self.Map, 
		        							  self.markers, 
		        							  {gridSize: self.settings.mcGridSize, maxZoom: self.settings.mcMaxZoom});

		        return _.isNull(self.MC);
		    }
		};

		return new gmap($);
	}
);