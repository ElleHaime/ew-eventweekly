require([
    'jquery',
    'fb',
    'noty',
    'profileChangePasswordControl',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, noty, profileChangePasswordControl) {
        fb.init();

        profileChangePasswordControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);