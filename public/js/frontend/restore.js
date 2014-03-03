require([
    'jquery',
    'profileRestorePasswordControl',
    'fb',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, noty, profileRestorePasswordControl) {
        fb.init();

        profileRestorePasswordControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
