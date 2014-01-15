require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontEventListControl',
    'datetimepicker',
    'utils',
    //'resizer',
    'noti',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, frontTopPanel, fb, frontEventListControl, datetimepicker, utils, noti) {
        noti.init();
        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        frontEventListControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);
