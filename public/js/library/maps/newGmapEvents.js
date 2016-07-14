/**
 * Created by slav on 1/22/14.
 */
define('newGmapEvents',
    ['jquery', 'googleMap', 'googleMc', 'googleMarker', 'googleInfoWindow', 'noty', 'underscore'],    
    function($, googleMap, googleMc, googleMarker, googleInfoWindow, noty, _) {

        function newGmapEvents(Map, Mc, options) {

            var debug = true;

            var settings = {
                autoGetEvents: true,
                requestInterval: 2000,
                eventsUrl: '/event/test-get',
                eventsCounter: '#events_count',
                searchCityBtn: '.locationCity',
                searchPage: 1
            };

            var interval = null;

            var __lastLat = null, __lastLng = null;

            var __newLat = null, __newLng = null, __newCity = null;

            var resetMap = false;

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
                    //console.log('Set latitude to cookie: '+lat);
                    //console.log('Set longitude to cookie: '+lng);
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
               	//console.log(data);

                $(settings.searchCityBtn).find('span').text(__newCity);

                if (data.status == true && !_.isUndefined(data.events)) {
                    if (_.isNull(Map)) {
                        redirectToMap(data);
                    }
                    
                    function processLargeArray(array) {
                        // set this to whatever number of items you can process at once
                        var chunk = 50;
                        var i = 0;
                        var len = array.length;
                        function doChunk() {
                            var cnt = chunk;
                            while (cnt-- && i < len) {
                                // process array[index] here
                                var event = array[i];
                                var InfoWindow = new googleInfoWindow(event);

                                if (event.latitude != null && event.longitude != null && !_.isUndefined(event.latitude) && !_.isUndefined(event.longitude)) {
                                    __lastLat = event.latitude;
                                    __lastLng = event.longitude;
                                }

                                //console.log(cnt);

                                var marker = new googleMarker({
                                        Map: Map,
                                        Event: event,
                                        InfoWindow: InfoWindow
                                });

                                if (!_.isEmpty(marker)) {
                                    // push new marker to storage
                                    Map.markers.push(marker);

                                    // initialize click to marker on map for open information window
                                    (function(marker, InfoWindow){
                                        google.maps.event.addListener(marker, 'click', function() {
                                            marker.setIcon(marker.clickedIcon);
                                            InfoWindow.open(Map, marker);
                                        });
                                    }(marker, InfoWindow));


                                    // info window click handle
                                    (function(marker){
                                        google.maps.event.addListener(InfoWindow,'closeclick',function(){
                                            marker.setIcon(marker.defaultIcon);
                                        });
                                    }(marker));
                                }
                            }

                            $(settings.eventsCounter).html(Map.markers.length);

                            ++i;

                            if (i < array.length) {
                                setTimeout(doChunk, 0);
                            } else {
                                // change events counter


                                //setCookies(__lastLat, __lastLng);

                                /*if (resetMap) {
                                 Map.setCenter(new google.maps.LatLng(__lastLat, __lastLng));
                                 resetMap = false;
                                 }*/
                                // add markers to clusterer
                                Mc.addMarkers(Map.markers);
                                // redraw clusterer
                                Mc.redraw();
                                //
                            }
                        }
                        doChunk();
                    }
                    processLargeArray(data.events);

                } else {
                    if (data.stop == true) {
                        Map.setCenter(new google.maps.LatLng(__newLat, __newLng));
                        $(settings.eventsCounter).html(0);
                        settings.searchPage = 1;
                        noty({text: 'No event in this area!', type: 'warning'});
                    }
                }

                if (data.stop == true) {
                    //console.log('interval cleared');
                	settings.searchPage = 1;
                    clearInterval(interval);
                } else {
                	if (!_.isUndefined(data.nextPage)) {
                		settings.searchPage = data.nextPage;
                	}
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
                url = url + '/' + settings.searchPage;
                
                if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
                    url = url + '/' + lat + '/' + lng;
                }
                if (!_.isUndefined(city)) {
                    url = url + '/' + city;
                }

                tmp = $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json'
                });

                return tmp;
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
                    var url = settings.eventsUrl;
                    url = url + '/' + settings.searchPage;
                    
                    if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
                        url = url + '/' + lat + '/' + lng;
                    }
                    if (!_.isUndefined(city)) {
                        url = url + '/' + city;
                    }
                    console.log(url);
                    
                    $.when($.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json'
                    })).done(function(response) {
                        responseHandler(response);
                    }).always(function() {
                        //console.log('empty result');
                    });
                };

                makeRequest();
                //$('.overlay').show();

                if (settings.requestInterval > 0) {
                   interval = setInterval(function(){
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