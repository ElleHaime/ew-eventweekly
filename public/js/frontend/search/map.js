/**
 * Created by Slava Basko on 12/26/13 <basko.slava@gmail.com>.
 */
require([
    'jquery',
    'fb',
    'frontEventLike',
    'noty',
    'gmap',
    'eventsPointer',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, frontEventLike, noty, gmap, eventsPointer) {
        var locationElem = $('#current_location');

        gmap.init({
            mapCenter: {
                lat: locationElem.attr('latitude'),
                lng: locationElem.attr('longitude')
            },
            mapZoom: $('#isMobile').val() === '1' ? 13 : 12
        });
        eventsPointer.init();

        fb.init();

        frontEventLike.init(); 

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
