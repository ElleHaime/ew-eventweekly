require([
    'jquery',
    'frontTopPanel',
    'profileRestorePasswordControl',
    'fb',
    'noti',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, noti, profileRestorePasswordControl) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();
        noti.init();

        profileRestorePasswordControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);
