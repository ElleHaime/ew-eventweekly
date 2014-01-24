require([
    'jquery',
    'frontTopPanel',
    'fb',
    'noti',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, noti) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();
        noti.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);
