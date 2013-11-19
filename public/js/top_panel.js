/**
 * Created by slav on 11/14/13.
 */

var topPanel = {

    settings: {
        searchCityBtn: '.location-city',
        searchCityBlock: '.location-search',
        sendCoordsUrl: ''
    },

    map: null,

    markers: [],

    mc: null,

    completeList: '.pac-container',

    address: null,

    lastLatlng: null,

    init: function(options) {
        var $this = this;

        //console.log('init top panel');

        // extends options
        $this.settings = $.extend($this.settings, options);

        $($this.settings.searchCityBtn).attr('data-state', 'close');

        // initialize clicks
        $this.__bindClicks();
    },

    __bindClicks: function() {
        var $this = this;
        $('body').on('click', $this.settings.searchCityBtn, function(e){
            e.preventDefault();
            $this.__changeVisibility($this.settings.searchCityBlock);
            $($this.settings.searchCityBlock).find('input').focus();

            var list = addressAutoComplete('topSearchCity');

            google.maps.event.addListener(list, 'place_changed', function() {
                var lat = list.getPlace().geometry.location.ob;
                var lng = list.getPlace().geometry.location.pb;

                $this.address = list.getPlace().formatted_address;

                $this.__sendCoords(lat, lng);
            });


        });
    },

    __changeVisibility: function(elem) {
        var element = $(elem), state = element.is(":visible");

        if (state) {
            element.hide();
        }else {
            element.show();
        }
    },

    __sendCoords: function(lat, lng) {
        var $this = this;
        $.post('/eventmap/'+lat+'/'+lng,
            function(data) {
                data = jQuery.parseJSON(data);
                if (data.status == "OK") {

                    $this.__clearMapMarkers();
                    $($this.settings.searchCityBtn).find('span').text($this.address);

                    if (data.message[0].length > 0) //own events
                    {
                        totalEvents=data.message[0].length;
                        //console.log('My events count:'+data.message[0].length);
                        $.each(data.message[0], function(index,event) {
                            $this.__showEvent(event);
                        });
                    }
                    if (data.message[1].length>0) //friend events
                    {
                        totalEvents=data.message[1].length;
                        //console.log('Friend events count:'+data.message[1].length);
                        $.each(data.message[1], function(index,event) {
                            $this.__showEvent(event);
                        });
                    }
                }
            }).done(function (){
                $('#events_count').html(totalEvents);
                var mcOptions = { gridSize: 50, maxZoom: 15};
                $this.mc = new MarkerClusterer(window.map, $this.markers, mcOptions);
                $this.__changeVisibility($this.settings.searchCityBlock);

                window.map.setCenter($this.lastLatlng);
            });
    },

    __showEvent: function(event) {
        var $this = this;
        if (typeof(event.venue.latitude)!='undefined' && typeof(event.venue.longitude)!='undefined')
        {
            var contentString = '<div class="info-win" id="content">' +
                '<div class="venue-name">'+event.name+'</div><div>'+event.anon+'</div>' +
                '<div>' +
                '<a target="_blank" href="https://www.facebook.com/events/'+event.eid+'">Facebook link</a> ' +
                '<a target="_blank" href="'+window.location.origin+'/event/show/'+event.id+'">Eventweekly link</a></div>' +
                '</div>';
            //contentString+='<div>Lat: '+event.venue.latitude+'</div><div>Lng: '+event.venue.longitude+'</div>';
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            var myLatlng = new google.maps.LatLng(event.venue.latitude,event.venue.longitude);

            $this.lastLatlng = myLatlng;

            var marker = new google.maps.Marker({
                position: myLatlng,
                map: window.map,
                title:event.name
            });

            $this.markers.push(marker);

            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(window.map, marker);
            });
        }
    },

    __clearMapMarkers: function() {
        var $this = this;
        for (var i = 0; i < window.markers.length; i++) {
            window.markers[i].setMap(null);
        }
        window.markers = [];
        for (var i = 0; i < $this.markers.length; i++) {
            $this.markers[i].setMap(null);
        }
        $this.markers = [];
        try {
            window.mc.clearMarkers();
            $this.mc.clearMarkers();
        }catch(err) {
            console.log(err);
        }
    }

};