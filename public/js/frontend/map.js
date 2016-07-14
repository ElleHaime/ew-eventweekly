require([
    'jquery',
    'fb',
//    'googleMap',
    'gmap',
    'newGmapEvents',
    'googleMc',
    'utils',
    'noty',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, googleMap, newGmapEvents, googleMc, utils, noty) {
        var locationElem = $('#current_location');

//        var map = new googleMap({
        var map = new gmap({	
            mapCenter: {
                lat: locationElem.attr('latitude'),
                lng: locationElem.attr('longitude')
            },
            mapZoom: $('#isMobile').val() === '1' ? 13 : 12
        });

        var Mc = new googleMc({
            Map: map,
            markers: []
        });

        var newGmapEvents = new newGmapEvents(map, Mc);

        fb.init();

        if ($('#conflict_location').length > 0) {
            noty({text: 'Your current location does not match with your facebook profile location. Set your <a href="/member/profile">default location</a> for Eventweekly', type: 'warning'});
        }

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);