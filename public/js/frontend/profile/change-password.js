require([
    'jquery',
    'frontTopPanel',
    'fb',
    'noti',
    'profileChangePasswordControl',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, noti, profileChangePasswordControl) {
        noti.init();

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        profileChangePasswordControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);