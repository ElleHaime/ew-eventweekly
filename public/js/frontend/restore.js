require([
    'jquery',
    'frontTopPanel',
    'profileRestorePasswordControl',
    'fb',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, noty, profileRestorePasswordControl) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        profileRestorePasswordControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
