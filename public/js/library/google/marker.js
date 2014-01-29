define('googleMarker',
    ['jquery', 'underscore', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
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
                    entertainment: {
                        icon: '/img/google/entertainment.png',
                        clickedIcon: '/img/google/entertainment-hover.png'
                    },
                    theater: {
                        icon: '/img/google/theater.png',
                        clickedIcon: '/img/google/theater-hover.png'
                    },
                    social: {
                        icon: '/img/google/social.png',
                        clickedIcon: '/img/google/social-hover.png'
                    },
                    special: {
                        icon: '/img/google/special.png',
                        clickedIcon: '/img/google/special-hover.png'
                    },
                    art: {
                        icon: '/img/google/art.png',
                        clickedIcon: '/img/google/art-hover.png'
                    },
                    family: {
                        icon: '/img/google/family.png',
                        clickedIcon: '/img/google/family-hover.png'
                    },
                    outdoor: {
                        icon: '/img/google/outdoor.png',
                        clickedIcon: '/img/google/outdoor-hover.png'
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
                    var newLatLng = new google.maps.LatLng(Event.latitude, Event.longitude);

                    var Icon = {
                        icon: settings.icons[Event.category[0].key].icon,
                        clickedIcon: settings.icons[Event.category[0].key].clickedIcon
                    };

                    // create marker
                    marker = new google.maps.Marker({
                        position: newLatLng,
                        map: Map,
                        title: Event.name,
                        icon: Icon.icon
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
                                }else if ($('.events-map', '<div>'+InfoWindow.content+'</div>').length < 5) {
                                    marker.content = InfoWindow.content + '<a href="/list" class="btn view-btn btn-block">View all events</a>';
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