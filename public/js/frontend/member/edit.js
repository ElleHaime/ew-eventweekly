require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontMemberEditControl',
    'noty',
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
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
