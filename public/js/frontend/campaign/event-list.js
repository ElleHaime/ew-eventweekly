require([
    'jquery',
    'fb',
    'frontEventListControl',
    'datetimepicker',
    'utils',
    'noty',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, frontEventListControl, datetimepicker, utils, noty) {
        fb.init();

        frontEventListControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
