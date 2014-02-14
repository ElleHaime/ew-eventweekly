require([
    'jquery',
    'frontTopPanel',
    'fb',
    'noty',
    'profileChangePasswordControl',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, noty, profileChangePasswordControl) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        profileChangePasswordControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);