/**
 * Created by slav on 11/19/13.
 */

app.GmapEvents = {

    /**
     * Settings
     */
    settings: {
        autoGetEvents: true,
        eventsUrl: '/eventmap',
        eventsCounter: '#events_count'
    },

    __lastLat: null,
    __lastLng: null,

    /**
     * Constructor
     */
    init: function(options) {
        var $this = this;

        $this.settings = _.extend($this.settings, options);

        if ($this.settings.autoGetEvents) {

            var lat = $.cookie('lastLat');
            var lng = $.cookie('lastLng');

            if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
                $this.getEvents(lat, lng);
            }else {
                $this.getEvents();
            }
        }
    },

    /**
     * Get events from server and draw markers on map
     *
     * @param lat
     * @param lng
     * @param city
     */
    getEvents: function(lat, lng, city) {
        var $this = this;
        $.when($this.__request(lat, lng, city)).then(function(response){
            $this.__responseHandler(response);
        });
    },

    /**
     * Prepare request to sever
     *
     * @param lat
     * @param lng
     * @param city
     * @returns {*}
     * @private
     */
    __request: function(lat, lng, city) {
        var $this = this;

        console.log('CITY - '+city);

        var url = $this.settings.eventsUrl;

        if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
            url = url+'/'+lat+'/'+lng;
        }

        if (!_.isUndefined(city)) {
            url = url+'/'+city;
        }

        return $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json'
        });
    },

    /**
     * Server response handler
     *
     * @param data
     * @private
     */
    __responseHandler: function(data) {
        var $this = this;

        if (data.status == "OK") {

            app.Gmap.markers = [];
            app.Gmap.MC.clearMarkers();

            if (data.message[0].length > 0) //own events
            {
                console.log('My events:'+data.message[0].length);
                $.each(data.message[0], function(index,event) {
                    $this.__drawMarker(event);
                });
            }
            if (data.message[1].length>0) //friend events
            {
                console.log('Friend events:'+data.message[1].length);
                $.each(data.message[1], function(index,event) {
                    $this.__drawMarker(event);
                });
            }

            // change events counter
            $($this.settings.eventsCounter).html(data.message[0].length + data.message[1].length);

            // write last map positions in to cookie
            $.cookie('lastLat', $this.__lastLat, {expires: 1});
            $.cookie('lastLng', $this.__lastLng, {expires: 1});

            app.Gmap.Map.setCenter(new app.__GoogleApi.maps.LatLng($this.__lastLat, $this.__lastLng));

            // add markers to clusterer
            app.Gmap.MC.addMarkers(app.Gmap.markers);
            // redraw clusterer
            app.Gmap.MC.redraw();
        }
    },

    /**
     * Draw markers on map
     *
     * @param event
     * @private
     */
    __drawMarker: function(event) {
        var $this = this;

        if (typeof(event.venue.latitude)!='undefined' && typeof(event.venue.longitude)!='undefined')
        {
            // prepare HTML for popup window on map
            var contentString = '<div class="info-win" id="content">' +
                '<div class="venue-name">'+event.name+'</div><div>'+event.anon+'</div>' +
                '<div>' +
                '<a target="_blank" href="https://www.facebook.com/events/'+event.eid+'">Facebook link</a> ' +
                '<a target="_blank" href="'+window.location.origin+'/event/show/'+event.id+'">Eventweekly link</a></div>' +
                '</div>';

            // initialize popup window
            var infoWindow = new app.__GoogleApi.maps.InfoWindow({
                content: contentString
            });

            $this.__lastLat = event.venue.latitude;
            $this.__lastLng = event.venue.longitude;

            // create marker
            var marker = new app.__GoogleApi.maps.Marker({
                position: new app.__GoogleApi.maps.LatLng(event.venue.latitude,event.venue.longitude),
                map: app.Gmap.Map,
                title: event.name
            });

            // push new marker to storage
            app.Gmap.markers.push(marker);

            // initialize click to marker on map for open information window
            app.__GoogleApi.maps.event.addListener(marker, 'click', function() {
                infoWindow.open(app.Gmap.Map, marker);
            });
        }
    }

};