/**
 * Created by Slava Basko on 12/26/13 <basko.slava@gmail.com>.
 */
require([
    'jquery',
    'fb',
    'frontEventLike',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, frontEventLike, noty) {
        fb.init();

        frontEventLike.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
