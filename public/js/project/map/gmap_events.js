/**
 * Created by slav on 11/19/13.
 */

app.GmapEvents = {

    debug: true,

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

        if ($this.debug) {
            console.log('CITY - '+city);
        }

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

            if (_.isNull(app.Gmap.Map) || _.isNull(app.Gmap.MC)) {
                $this.__redirectToMap(data);
            }

            app.Gmap.markers = [];
            app.Gmap.MC.clearMarkers();

            if (data.message[0].length > 0) //own events
            {
                if ($this.debug) {
                    console.log('My events:'+data.message[0].length);
                }
                $.each(data.message[0], function(index,event) {
                    $this.__drawMarker(event);
                });
            }
            if (data.message[1].length>0) //friend events
            {
                if ($this.debug) {
                    console.log('Friend events:'+data.message[1].length);
                }
                $.each(data.message[1], function(index,event) {
                    $this.__drawMarker(event);
                });
            }

            // change events counter
            $($this.settings.eventsCounter).html(data.message[0].length + data.message[1].length);

            // write last map positions in to cookie
            $this.__setCookies($this.__lastLat, $this.__lastLng);

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

        if ($this.debug) {
            console.log('Draw event below: ');
            console.log(event);
        }

        if (typeof(event.venue.latitude)!='undefined' && typeof(event.venue.longitude)!='undefined')
        {
            // prepare HTML for popup window on map
            var contentString = $this.__createInfoPopupContent(event);

            // initialize popup window
            var infoWindow = new app.__GoogleApi.maps.InfoWindow({
                content: contentString
            });

            $this.__lastLat = event.venue.latitude;
            $this.__lastLng = event.venue.longitude;

            var newLatLng = new app.__GoogleApi.maps.LatLng(event.venue.latitude,event.venue.longitude);

            // create marker
            var marker = new app.__GoogleApi.maps.Marker({
                position: newLatLng,
                map: app.Gmap.Map,
                title: event.name
            });

            // add content to marker
            marker.content  = infoWindow.content;

            // get array of markers currently in cluster
            var allMarkers = app.Gmap.markers;

            // check to see if any of the existing markers match the latlng of the new marker
            if (allMarkers.length != 0) {
                for (var i=0; i < allMarkers.length; i++) {
                    var existingMarker = allMarkers[i];
                    var pos = existingMarker.getPosition();
                    if (newLatLng.equals(pos)) {
                        infoWindow.content = existingMarker.content + " & " + $this.__createInfoPopupContent(event);
                    }
                }
            }

            if ($this.debug) {
                console.log('New map marker was created below:');
                console.log(marker);
            }

            // push new marker to storage
            app.Gmap.markers.push(marker);

            // initialize click to marker on map for open information window
            app.__GoogleApi.maps.event.addListener(marker, 'click', function() {
                infoWindow.open(app.Gmap.Map, marker);
            });
        }
    },

    __redirectToMap: function(data) {
        var $this = this, lat = null, lng = null;
        if (!_.isEmpty(data.message[0])) {
            lat = _.last(data.message[0]).venue.latitude;
            lng = _.last(data.message[0]).venue.longitude;
            $this.__setCookies(lat, lng);
        }

        if (!_.isEmpty(data.message[1])) {
            lat = _.last(data.message[1]).venue.latitude;
            lng = _.last(data.message[1]).venue.longitude;
            $this.__setCookies(lat, lng);
        }

        window.location.href = '/map';
    },

    __setCookies: function(lat, lng) {
        if (this.debug) {
            console.log('Set latitude to cookie: '+lat);
            console.log('Set longitude to cookie: '+lng);
        }
        // write last map positions in to cookie
        $.cookie('lastLat', lat, {expires: 1});
        $.cookie('lastLng', lng, {expires: 1});
    },

    __createInfoPopupContent: function(event) {
        return '<div class="info-win" id="content">' +
            '<div class="venue-name">'+event.name+'</div><div>'+event.anon+'</div>' +
            '<div>' +
            '<a target="_blank" href="https://www.facebook.com/events/'+event.eid+'">Facebook link</a> ' +
            '<a href="'+window.location.origin+'/event/show/'+event.id+'">Eventweekly link</a></div>' +
            '</div>';
    }

};