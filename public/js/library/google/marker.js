define('googleMarker',
    ['jquery', 'underscore', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
    function($, _) {

        function Marker(options) {

            var marker = {};

            var settings = {
                Map: null,
                Event: null,
                InfoWindow: null
            };

            settings = _.extend(settings, options);

            if (!_.isNull(settings.Map) && !_.isNull(settings.Event) && !_.isNull(settings.InfoWindow)) {
                var Event = settings.Event, Map = settings.Map, InfoWindow = settings.InfoWindow;

                var eventIds = _.pluck(Map.events, 'ew_id');
                var fbEventIds = _.pluck(Map.events, 'fb_id');
                if (!_.contains(eventIds, Event.id) || (_.isUndefined(Event.id) && !_.contains(fbEventIds, Event.fb_uid))) {
                    var newLatLng = new google.maps.LatLng(Event.venue.latitude, Event.venue.longitude);

                    // get array of markers currently in cluster
                    var allMarkers = Map.markers;

                    // check to see if any of the existing markers match the latlng of the new marker
                    if (allMarkers.length != 0) {
                        for (var i=0; i < allMarkers.length; i++) {
                            var existingMarker = allMarkers[i];
                            var pos = existingMarker.getPosition();
                            if (newLatLng.equals(pos)) {
                                InfoWindow.content = existingMarker.content + " & " + InfoWindow.createInfoPopupContent(Event);
                            }
                        }
                    }

                    // create marker
                    marker = new google.maps.Marker({
                        position: newLatLng,
                        map: Map,
                        title: Event.name
                    });

                    // add content to marker
                    marker.content  = InfoWindow.content;

                    Map.events.push({'ew_id': Event.id, fb_id: Event.fb_uid});
                }
            }

            return marker;
        }

        return Marker;

    }
);