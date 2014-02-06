require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontMemberEditControl',
    'frontMemberChangeLocation',
    'noti',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
    ],

    function($, frontTopPanel, fb, frontMemberEditControl, frontMemberChangeLocation, noti) {
        noti.init();
        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        frontMemberEditControl.init();

        frontMemberChangeLocation.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);
