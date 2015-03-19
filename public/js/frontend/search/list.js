require([
    'jquery',
    'fb',
    'frontEventLike',
    'noty',
    'lazyLoader',//new  !! eventList.volt has <scriptjquery    
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, frontEventLike, noty, lazyLoader) {

        fb.init();
        frontEventLike.init();
        lazyLoader.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);