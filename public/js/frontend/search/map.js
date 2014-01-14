/**
 * Created by Slava Basko on 12/26/13 <basko.slava@gmail.com>.
 */
require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontEventLike',
    'noti',
    'gmap',
    'eventsPointer',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, frontEventLike, noti, gmap, eventsPointer) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();
        noti.init();

        frontEventLike.init();

        var locationElem = $('#current_location');
        gmap.init({
            mapCenter: {
                lat: locationElem.attr('latitude'),
                lng: locationElem.attr('longitude')
            }
        });

        eventsPointer.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);
