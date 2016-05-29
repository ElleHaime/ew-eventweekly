define('eventsPointer',
//	['jquery', 'gmap', 'noty', 'googleMarker', 'googleInfoWindow', 'underscore', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js'],
	['jquery', 'gmap', 'noty', 'googleMarker', 'googleInfoWindow', 'underscore', 'google!maps,3,other_params:key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
	function($, gmap, noty, googleMarker, googleInfoWindow) {

        return {

            settings: {
                debug: true,
                autoGetEvents: true,
                eventsUrl: '/search/map',
                eventsUrlParams: '.urlParams',
                truncateLength: 30,
                pageCurrent: 1,
                requestInterval: 20000
            },

            __lastLat: null,
            __lastLng: null,

            init: function(options)
            {
                var $this = this;

                $this.settings = _.extend($this.settings, options);
                $this.pointEvents(searchResults);
                
                if ($this.settings.autoGetEvents) {
                	$this.settings.pageCurrent++;
                	
                	$this.__getNextEvents();
                }
            },

            pointEvents: function(events) {
                var $this = this;

                if (_.isEmpty(events) && $this.settings.pageCurrent == 1) {
                    noty({text: 'No events for your request!', type: 'warning'});
                } else {
                    _.each(events, function(node) {
                        $this.__drawMarker(node);
                    });
                    // add markers to clusterer
                    gmap.MC.addMarkers(gmap.Map.markers);
                    // redraw clusterer
                    //gmap.MC.redraw();
                    gmap.MC.repaint();

                    //gmap.Map.setCenter(new google.maps.LatLng($this.__lastLat, $this.__lastLng));
                }
            },

            __drawMarker: function(event) {
                var $this = this;
                if ($this.settings.debug) {
                    //console.log(event);
                }

                //if (!_.isUndefined(event.latitude) && !_.isUndefined(event.longitude)) {
                if (event.latitude != null && event.longitude != null && !_.isUndefined(event.latitude) && !_.isUndefined(event.longitude)) {
                    $this.__lastLat = event.latitude;
                    $this.__lastLng = event.longitude;
                }else if (!_.isUndefined(event.venue) && !_.isUndefined(event.venue.latitude) && !_.isUndefined(event.venue.longitude)) {
                    $this.__lastLat =  event.venue.latitude;
                    $this.__lastLng = event.venue.longitude;
                }


                    var InfoWindow = new googleInfoWindow(event);

                    var marker = new googleMarker({
                        Map: gmap.Map,
                        Event: event,
                        InfoWindow: InfoWindow
                    });

                    if (!_.isEmpty(marker)) {
                        // push new marker to storage
                        gmap.Map.markers.push(marker);

                        // initialize click to marker on map for open information window
                        google.maps.event.addListener(marker, 'click', function() {
                            marker.setIcon(marker.clickedIcon);
                            InfoWindow.open(gmap.Map, marker);
                        });

                        // info window click handle
                        google.maps.event.addListener(InfoWindow,'closeclick',function(){
                            marker.setIcon(marker.defaultIcon);
                        });
                    }
            },

            __createInfoPopupContentSingle: function(event) {
                var eventlink = window.location.origin+'/event/'+event.id;
                if (!_.isUndefined(event.slugUri)) {
                    eventlink = '/'+event.slugUri;
                }
                return '<div class="info-win" id="content">' +
                    '<div class="venue-name">'+event.name+'</div><div>'+event.description+'</div>' +
                    '<div>' +
                    '<a target="_blank" href="https://www.facebook.com/events/'+event.fb_uid+'">Facebook link</a> ' +
                    '<a href="'+eventlink+'">Eventweekly link</a></div>' +
                    '</div>';
            },
            
            __getNextEvents: function()
            {
            	var $this = this;
            	
            	var makeRequest = function() {
            		//var url = $this.settings.eventsUrl + '?' + searchUrlParams + '&page=' + $this.settings.pageCurrent;
            		var url = searchUrlParams + '&page=' + $this.settings.pageCurrent;
//console.log(url);
            		$.when($.ajax({ url: url,
                        			type: 'GET',
                        			dataType: 'json',
                        error: function(response, error) {
//console.log('error:');
//console.log(error);
//console.log(response.responseText);
                        	$this.settings.pageCurrent = 1;
                            clearInterval(interval);
                        }
                    })).done(function(response) {
//console.log(response.data);
                    	$this.__responseHandler(response);
                    }).always(function(response) {
                    }); 	
            	};
            	
            	makeRequest();
            	
                if ($this.settings.requestInterval > 0) {
                    interval = setInterval(function() {
                         makeRequest();
                     }, $this.settings.requestInterval);
                 }

            },
            
            __responseHandler: function(data) {
            	var $this = this;
            	$this.pointEvents(data['data']);

            	if (data['stop'] == true) {
            		//console.log('clear interval');
                	$this.settings.pageCurrent = 1;
                    clearInterval(interval);
                } else {
               		$this.settings.pageCurrent++;
                }
            }

        };

	}
);
