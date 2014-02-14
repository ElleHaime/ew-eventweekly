require([
    'jquery',
    'frontTopPanel',
    'fb',
    'googleMap',
    'newGmapEvents',
    'googleMc',
    'utils',
    'noty',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, googleMap, newGmapEvents, googleMc, utils, noty) {
        var locationElem = $('#current_location');

        console.log('type');
        console.log(typeof googleMap);

        var map = new googleMap({
            mapCenter: {
                lat: locationElem.attr('latitude'),
                lng: locationElem.attr('longitude')
            },
            mapZoom: $('#isMobile').val() === '1' ? 20 : 15
        });

        var Mc = new googleMc({
            Map: map,
            markers: []
        });

        var newGmapEvents = new newGmapEvents(map, Mc);

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        if ($('#conflict_location').length > 0) {
            noty({text: 'Your current location does not match with your facebook profile location. Set your <a href="/profile">default location</a> for Eventweekly', type: 'warning'});
        }

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);