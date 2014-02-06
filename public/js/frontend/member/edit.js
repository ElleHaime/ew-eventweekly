require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontMemberEditControl',
    'noti',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
    ],
    function($, frontTopPanel, fb, frontMemberEditControl) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        frontMemberEditControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);
