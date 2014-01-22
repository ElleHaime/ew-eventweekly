define('gmapEvents',
	['jquery', 'gmap', 'noti', 'underscore', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js'],
	function($, gmap, noti) {
		
		function gmapEvents($, gmap, noti)
		{
		    var self = this;

		   	self.debug = false,
		    self.settings = {
		        autoGetEvents: true,
		        eventsUrl: '/eventmap',
		        eventsCounter: '#events_count',
                searchCityBtn: '.locationCity'
		    },
		    self.__lastLat = null,
		    self.__lastLng = null,

            self.__newLat = null,
            self.__newLng = null,
            self.__newCity = null,

            self.resetLocation = false,


		    self.init = function(options)
		    {
		        self.settings = _.extend(self.settings, options);

		        if (self.settings.autoGetEvents) {

		            var lat = $.cookie('lastLat');
		            var lng = $.cookie('lastLng');
		            var city = $.cookie('lastCity');

                    if (!_.isUndefined(lat) && !_.isUndefined(lng) && !_.isUndefined(city)) {
                        self.getEvents(lat, lng, city);
                    } else if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
		                self.getEvents(lat, lng);
		            } else {
		                self.getEvents();
		            }
		        }
		    }
		    
		    /**
		     * Get events from server and draw markers on map
		     *
		     * @param lat
		     * @param lng
		     * @param city
		     */
		    self.getEvents = function(lat, lng, city) {
                self.__newLat = lat;
                self.__newLng = lng;
                self.__newCity = city;

		        $.when(self.__request(lat, lng, city)).then(function(response) {
		            self.__responseHandler(response);
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
		    self.__request = function(lat, lng, city) {
		        var url = self.settings.eventsUrl;

		        if (!_.isUndefined(lat) && !_.isUndefined(lng)) {
		            url = url + '/' + lat + '/' + lng;
		        }
		        if (!_.isUndefined(city)) {
		            url = url + '/' + city;
		        }
		        if (self.debug) {
					console.log(url);
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
		    self.__responseHandler = function(data) {
		    	if (self.debug) {
		            console.log(data);
		        }
                $(self.settings.searchCityBtn).find('span').text(self.__newCity);
                if (self.resetLocation === true) {
                    //gmap.Map.setCenter(new google.maps.LatLng(self.__newLat, self.__newLng));
                }
		        if (data.status == "OK") {

		            if (_.isNull(gmap.Map) || _.isNull(gmap.MC)) {
		                self.__redirectToMap(data);
		            }

		            gmap.markers = [];
		            gmap.MC.clearMarkers();

		            if (data.message[0].length > 0) //own events
		            {
		                if (self.debug) {
		                    console.log('My events:' + data.message[0].length);
		                }
		                $.each(data.message[0], function(index,event) {
		                    self.__drawMarker(event);
		                });
		            }
		            if (data.message[1].length > 0) //friend events
		            {
		                if (self.debug) {
		                    console.log('Friend events:' + data.message[1].length);
		                }
		                $.each(data.message[1], function(index,event) {
		                    self.__drawMarker(event);
		                });
		            }

		            // change events counter
		            $(self.settings.eventsCounter).html(data.message[0].length + data.message[1].length);

		            // write last map positions in to cookie
		            self.__setCookies(self.__lastLat, self.__lastLng);

		            gmap.Map.setCenter(new google.maps.LatLng(self.__lastLat, self.__lastLng));
		            // add markers to clusterer
		            gmap.MC.addMarkers(gmap.markers);
		            // redraw clusterer
		            gmap.MC.redraw();
		        }else {
                    gmap.Map.setCenter(new google.maps.LatLng(self.__newLat, self.__newLng));
                    $(self.settings.eventsCounter).html(0);
                    noti.createNotification('No event in this area!', 'warning');
                }
		    },

		    /**
		     * Draw markers on map
		     *
		     * @param event
		     * @private
		     */
		    self.__drawMarker = function(event) {
		        if (self.debug) {
		            console.log('Draw event below: ');
		            console.log(event);
		        }

		        if (typeof(event.venue.latitude)!='undefined' && typeof(event.venue.longitude) != 'undefined')
		        {
		            // prepare HTML for popup window on map
		            var contentString = self.__createInfoPopupContent(event);

		            // initialize popup window
		            var infoWindow = new google.maps.InfoWindow({
		                content: contentString
		            });

		            self.__lastLat = event.venue.latitude;
		            self.__lastLng = event.venue.longitude;

		            var newLatLng = new google.maps.LatLng(event.venue.latitude, event.venue.longitude);

                    // get array of markers currently in cluster
                    var allMarkers = gmap.markers;

                    // check to see if any of the existing markers match the latlng of the new marker
                    if (allMarkers.length != 0) {
                        for (var i=0; i < allMarkers.length; i++) {
                            var existingMarker = allMarkers[i];
                            var pos = existingMarker.getPosition();
                            if (newLatLng.equals(pos)) {
                                infoWindow.content = existingMarker.content + " & " + self.__createInfoPopupContent(event);
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
		            marker.content  = infoWindow.content;

		            if (self.debug) {
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

		    
		    self.__redirectToMap = function(data) {
		        var lat = null, 
		        	lng = null;
console.log(data);		        
		        if (!_.isEmpty(data.message[0])) {
		            lat = _.last(data.message[0]).venue.latitude;
		            lng = _.last(data.message[0]).venue.longitude;
		            self.__setCookies(lat, lng);
		        }

		        if (!_.isEmpty(data.message[1])) {
		            lat = _.last(data.message[1]).venue.latitude;
		            lng = _.last(data.message[1]).venue.longitude;
		            self.__setCookies(lat, lng);
		        }

		        window.location.href = '/map';
		    },
		    

		    self.__setCookies = function(lat, lng, path) {
		        if (this.debug) {
		            console.log('Set latitude to cookie: '+lat);
		            console.log('Set longitude to cookie: '+lng);
		        }

		        if (_.isUndefined(path) || _.isEmpty(path)) {
		            path = '/';
		        }

		        // write last map positions in to cookie
		        $.cookie('lastLat', lat, {expires: 1, path: path});
		        $.cookie('lastLng', lng, {expires: 1, path: path});
		    },

		    
		    self.__createInfoPopupContent = function(event) {
		        return '<div class="info-win music-category " id="content"> ' +
                    ' <div class="events-img-box">' +
                        '<img  class="events-img" src="img/eg.jpg" alt="">' +
                        '<div class="events-date-box"><i class="icon-time"></i> 23 Aug 2014 </div> ' +
                    '</div>' +
                    '<div class="events-descriptions-box">' +
                        '<h4><a href="#">Dublin</a> - <span>Irland</span> </h4>' +
		                '<div class="venue-name">'+event.name+'</div><div>'+event.description+'</div>' +
		                '<a href="'+window.location.origin+'/event/show/'+event.id+'">Eventweekly link</a></div>' +
                    '<div>' +
		            '</div>' +
                    '</div>';
		    }
		};
		
		return new gmapEvents($, gmap, noti);
	}
)
