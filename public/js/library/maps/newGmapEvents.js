/**
 * Created by slav on 1/22/14.
 */
define('newGmapEvents',
    ['jquery', 'googleMap', 'googleMc', 'googleMarker', 'googleInfoWindow', 'noti', 'underscore', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js'],
    function($, googleMap, googleMc, googleMarker, googleInfoWindow, noti, _) {

        function newGmapEvents(Map, Mc, options) {

            var debug = true;

            var settings = {
                autoGetEvents: true,
                requestInterval: 0, // TODO: set some interval
                eventsUrl: '/eventmap',
                eventsCounter: '#events_count',
                searchCityBtn: '.locationCity',
                userEventsCreated: '#userEventsCreated',
                userFriendsGoing: '#userFriendsGoing'
            };

            var interval = null;

            var __lastLat = null, __lastLng = null;

            var __newLat = null, __newLng = null, __newCity = null;

            var resetMap = true;

            settings = _.extend(settings, options);

            /**
             * Cookies setter
             *
             * @param lat
             * @param lng
             * @param path
             */
            var setCookies = function(lat, lng, path) {
                if (debug) {
                    console.log('Set latitude to cookie: '+lat);
                    console.log('Set longitude to cookie: '+lng);
                }

                if (_.isUndefined(path) || _.isEmpty(path)) {
                    path = '/';
                }

                // write last map positions in to cookie
                $.cookie('lastLat', lat, {expires: 1, path: path});
                $.cookie('lastLng', lng, {expires: 1, path: path});
            };

            /**
             * If no Map redirect
             *
             * @param data
             */
            var redirectToMap = function(data) {
                var lat = null,
                    lng = null;

                if (!_.isEmpty(data.events)) {
                    lat = _.last(data.events).venue.latitude;
                    lng = _.last(data.events).venue.longitude;
                    setCookies(lat, lng);
                }

                window.location.href = '/map';
            };

            /**
             * Server response handler
             *
             * @param data
             * @private
             */
            var responseHandler = function(data) {
                if (debug) {
                    console.log(data);
                }

                if (data.status == true && !_.isUndefined(data.events)) {

                    if (_.isNull(Map)) {
                        redirectToMap(data);
                    }

                    $.each(data.events, function(index, event) {
                       
                        var InfoWindow = new googleInfoWindow(event);

                        if (event.latitude != null && event.longitude != null) {
                            __lastLat = event.latitude;
                            __lastLng = event.longitude;
                        }

                        var marker = new googleMarker({
                            Map: Map,
                            Event: event,
                            InfoWindow: InfoWindow
                        });

                        if (!_.isEmpty(marker)) {
                            // push new marker to storage
                            Map.markers.push(marker);

                            // initialize click to marker on map for open information window
                            google.maps.event.addListener(marker, 'click', function() {
                                marker.setIcon(marker.clickedIcon);
                                InfoWindow.open(Map, marker);
                            });

                            // info window click handle
                            google.maps.event.addListener(InfoWindow,'closeclick',function(){
                                marker.setIcon(marker.defaultIcon);
                            });
                        }
                    });

                    // change events counter
                    $(settings.eventsCounter).html(Map.markers.length);

                    setCookies(__lastLat, __lastLng);

                    if (resetMap) {
                        Map.setCenter(new google.maps.LatLng(__lastLat, __lastLng));
                        resetMap = false;
                    }
                    // add markers to clusterer
                    Mc.addMarkers(Map.markers);
                    // redraw clusterer
                    Mc.redraw();

                } else {
                    Map.setCenter(new google.maps.LatLng(__newLat, __newLng));
                    $(settings.eventsCounter).html(0);
                    noti.createNotification('No event in this area!', 'warning');
                }

                if (data.eventsCreated) {
                    $(settings.userEventsCreated).text(data.eventsCreated);
                }

                if (data.eventsFriendsGoing) {
                    $(settings.userFriendsGoing).text(data.eventsFriendsGoing);
                }

                if (data.stop == true) {
                    clearInterval(interval);
                }
            };

            /**
             * Prepare request to sever
             *
             * @param lat
             * @param lng
             * @param city
             * @returns {*}
             * @private
             */
            var request = function(lat, lng, city) {
                var url = settings.eventsUrl;

                if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
                    url = url + '/' + lat + '/' + lng;
                }
                if (!_.isUndefined(city)) {
                    url = url + '/' + city;
                }

                return $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json'
                });
            };

            /**
             * Get events from server and draw markers on map
             *
             * @param lat
             * @param lng
             * @param city
             */
            var getEvents = function(lat, lng, city) {
                __newLat = lat;
                __newLng = lng;
                __newCity = city;

                var makeRequest = function() {
                    $.when(request(lat, lng, city)).then(function(response) {
                        responseHandler(response);
                    });
                };

                makeRequest();

                if (settings.requestInterval > 0) {
                   interval = setInterval(function(){
                        console.log('new request');
                        makeRequest();
                    }, settings.requestInterval);
                }
            };

            if (settings.autoGetEvents) {
                var lat = $.cookie('lastLat');
                var lng = $.cookie('lastLng');
                var city = $.cookie('lastCity');

                if (!_.isUndefined(lat) && !_.isUndefined(lng) && !_.isUndefined(city)) {
                    getEvents(lat, lng, city);
                } else if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
                    getEvents(lat, lng);
                } else {
                    getEvents();
                }
            }

        }

        return newGmapEvents;

    });