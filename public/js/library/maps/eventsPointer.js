define('eventsPointer',
	['jquery', 'gmap', 'noti', 'underscore', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js'],
	function($, gmap, noti) {

        return {

            settings: {
                debug: false,
                autoGetEvents: true,
                eventsUrl: '/eventmap',
                eventsCounter: '#events_count',
                truncateLength: 30
            },

            __lastLat: null,
            __lastLng: null,

            init: function(options)
            {
                var $this = this;

                $this.settings = _.extend($this.settings, options);

                $this.pointEvents(searchResults);
            },

            pointEvents: function(events) {
                var $this = this;

                if (_.isEmpty(events)) {
                    noti.createNotification('No events for your request!', 'warning');
                }else {
                    _.each(events, function(node) {
                        $this.__drawMarker(node);
                    });

                    // add markers to clusterer
                    gmap.MC.addMarkers(gmap.markers);
                    // redraw clusterer
                    gmap.MC.redraw();

                    gmap.Map.setCenter(new google.maps.LatLng($this.__lastLat, $this.__lastLng));
                }
            },

            __drawMarker: function(event) {
                var $this = this;
                if ($this.settings.debug) {
                    console.log('Draw event: ');
                    console.log(event);
                }

                if (!_.isUndefined(event.venue.latitude) && !_.isUndefined(event.venue.longitude))
                {
                    // prepare HTML for popup window on map
                    var contentString = $this.__createInfoPopupContentSingle(event);

                    // initialize popup window
                    var infoWindow = new google.maps.InfoWindow({
                        content: contentString
                    });

                    $this.__lastLat = event.venue.latitude;
                    $this.__lastLng = event.venue.longitude;

                    var newLatLng = new google.maps.LatLng(event.venue.latitude, event.venue.longitude);

                    // get array of markers currently in cluster
                    var allMarkers = gmap.markers;

                    // check to see if any of the existing markers match the latlng of the new marker
                    if (allMarkers.length != 0) {
                        for (var i=0; i < allMarkers.length; i++) {
                            var existingMarker = allMarkers[i];
                            var pos = existingMarker.getPosition();
                            if (newLatLng.equals(pos)) {
                                infoWindow.content = existingMarker.content + " & " + $this.__createInfoPopupContentSingle(event);
                            }
                        }
                    }

                    // create marker
                    var marker = new google.maps.Marker({
                        position: newLatLng,
                        map: gmap.Map,
                        title: event.name
                    });

                    // add content to marker
                    marker.content = infoWindow.content;

                    if ($this.settings.debug) {
                        console.log('New map marker was created below:');
                        console.log(marker);
                    }

                    // push new marker to storage
                    gmap.markers.push(marker);

                    // initialize click to marker on map for open information window
                    google.maps.event.addListener(marker, 'click', function() {
                        infoWindow.open(gmap.Map, marker);
                    });
                }
            },

            __createInfoPopupContentSingle: function(event) {
                return '<div class="info-win" id="content">' +
                    '<div class="venue-name">'+event.name+'</div><div>'+event.description+'</div>' +
                    '<div>' +
                    '<a target="_blank" href="https://www.facebook.com/events/'+event.fb_uid+'">Facebook link</a> ' +
                    '<a href="'+window.location.origin+'/event/show/'+event.id+'">Eventweekly link</a></div>' +
                    '</div>';
            }

        };

	}
);
