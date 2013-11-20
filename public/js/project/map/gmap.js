/**
 * Created by slav on 11/19/13.
 */

app.Gmap = {

    /**
     * Setting
     */
    settings: {
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

    /**
     * Google map holder
     */
    Map: null,

    /**
     * MarkerClusterer holder
     */
    MC: null, // MarkerClusterer http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/docs/reference.html

    /**
     * Storage of all map markers
     */
    markers: [],

    /**
     * Constructor
     *
     * @param options
     */
    init: function(options) {
        var $this = this;

        $this.settings = _.extend($this.settings, options);

        // try initialize map
        if (_.once($this.__initializeMap())) {
            // initialize clusterer if map was created
            $this.__initializeMarkerClusterer();
        }
    },

    /**
     * Create google map instance
     *
     * @returns {*}
     * @private
     */
    __initializeMap: function() {
        var $this = this;

        var lat = $.cookie('lastLat');
        var lng = $.cookie('lastLng');

        var position = null;

        if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
            position = new app.__GoogleApi.maps.LatLng(lat, lng);
        }else {
            position = new app.__GoogleApi.maps.LatLng($this.settings.mapCenter.lat, $this.settings.mapCenter.lng)
        }

        var mapOptions = {
            center: position,
            zoom: $this.settings.mapZoom,
            mapTypeId: eval('google.maps.MapTypeId.'+$this.settings.mapTypeId)
        };

        // create map
        $this.Map = new app.__GoogleApi.maps.Map(document.getElementById($this.settings.mapContainer), mapOptions);

        return _.isNull($this.Map);
    },

    /**
     * Create MarkerClusterer instance
     *
     * @returns {*}
     * @private
     */
    __initializeMarkerClusterer: function() {
        var $this = this;

        $this.MC = new MarkerClusterer($this.Map, $this.markers, {gridSize: $this.settings.mcGridSize, maxZoom: $this.settings.mcMaxZoom});

        return _.isNull($this.MC);
    }

};