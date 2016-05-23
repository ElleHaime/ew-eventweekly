define('googleMarker',
//    ['jquery', 'underscore', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
    ['jquery', 'underscore', 'domReady'],
    function($, _) {

        function Marker(options) {

            var marker = {};

            var settings = {
                Map: null,
                Event: null,
                InfoWindow: null,
                icons: {
                    music: {
                        icon: '/img/google/music.png',
                        clickedIcon: '/img/google/music-hover.png'
                    },
                    sport: {
                        icon: '/img/google/sport.png',
                        clickedIcon: '/img/google/sport-hover.png'
                    },
                    business: {
                        icon: '/img/google/business.png',
                        clickedIcon: '/img/google/business-hover.png'
                    },
                    culture: {
                        icon: '/img/google/culture.png',
                        clickedIcon: '/img/google/culture-hover.png'
                    },
                    social: {
                        icon: '/img/google/social.png',
                        clickedIcon: '/img/google/social-hover.png'
                    },
                    other: {
                        icon: '/img/google/other.png',
                        clickedIcon: '/img/google/other-hover.png'
                    }
                }
            };

            settings = _.extend(settings, options);

            if (!_.isNull(settings.Map) && !_.isNull(settings.Event) && !_.isNull(settings.InfoWindow)) {
                var Event = settings.Event, Map = settings.Map, InfoWindow = settings.InfoWindow;

                var eventIds = _.pluck(Map.events, 'ew_id');
                var fbEventIds = _.pluck(Map.events, 'fb_id');
                if (!_.contains(eventIds, Event.id) || (_.isUndefined(Event.id) && !_.contains(fbEventIds, Event.fb_uid))) {
                    //var newLatLng = new google.maps.LatLng(Event.venue.latitude, Event.venue.longitude);

                    var latitude, longitude;
                    if (!_.isUndefined(Event.latitude) && !_.isUndefined(Event.longitude) && !_.isEmpty(Event.latitude) && !_.isEmpty(Event.longitude) && Event.latitude != '0.00000000' && Event.longitude != '0.00000000') {
                        latitude = Event.latitude;
                        longitude = Event.longitude;
                    }else if (!_.isUndefined(Event.venue) && !_.isUndefined(Event.venue.latitude) && !_.isUndefined(Event.venue.longitude)) {
                        latitude = Event.venue.latitude;
                        longitude = Event.venue.longitude;
                    }else if (!_.isUndefined(Event.location)) {
                        latitude = (Event.location.latitudeMax + Event.location.latitudeMin) / 2;
                        longitude = (Event.location.longitudeMax + Event.location.longitudeMin) / 2;
                    }

                    var newLatLng = new google.maps.LatLng(latitude, longitude);

                    var category = 'other';
                    if (Event.category && !_.isUndefined(Event.category) && !_.isUndefined(Event.category[0]) && !_.isUndefined(Event.category[0].key)) {
                        category = Event.category[0].key;
                    }

                    var Icon = {
                        icon: settings.icons[category].icon,
                        clickedIcon: settings.icons[category].clickedIcon
                    };

                    // create marker
                    marker = new google.maps.Marker({
                        position: newLatLng,
                        map: Map,
                        title: Event.name,
                        icon: Icon.icon,
                        animation: google.maps.Animation.DROP
                    });

                    // get array of markers currently in cluster
                    var allMarkers = Map.markers;

                    // check to see if any of the existing markers match the latlng of the new marker
                    if (allMarkers.length != 0) {
                        for (var i=0; i < allMarkers.length; i++) {
                            var existingMarker = allMarkers[i];
                            var pos = existingMarker.getPosition();
                            if (newLatLng.equals(pos)) {
                                if (_.isUndefined(existingMarker.content)) {
                                    InfoWindow.content = InfoWindow.createInfoPopupContentMany(Event) + InfoWindow.createInfoPopupContentMany(existingMarker.Event);
                                    existingMarker.content = InfoWindow.content;
                                }else {
                                    InfoWindow.content = InfoWindow.createInfoPopupContentMany(Event) + existingMarker.content;
                                }

                                if ($('.events-map', '<div>'+InfoWindow.content+'</div>').length < 4) {
                                    marker.content = InfoWindow.content;
                                } else if ($('.events-map', '<div>'+InfoWindow.content+'</div>').length > 5) {
                                	rep1 = document.URL.replace('/map', '/list');
                                	rep2 = rep1.replace('searchType=in_map', 'searchType=in_list');
                                	batchUrl = rep2 + '&searchLocationLatCurrent=' + Event.latitude + '&searchLocationLngCurrent=' + Event.longitude;
                                	
                                	marker.content = InfoWindow.content + '<a href="' + batchUrl + '" class="btn view-btn btn-block">View all events</a>';
                                }
                            }
                        }
                    }

                    marker.Event = Event;

                    marker.defaultIcon = Icon.icon;
                    marker.clickedIcon = Icon.clickedIcon;

                    Map.events.push({'ew_id': Event.id, fb_id: Event.fb_uid});
                }
            }

            return marker;
        }

        return Marker;

    }
);