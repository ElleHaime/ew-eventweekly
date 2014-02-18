require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontMemberEditControl',
    'frontMemberChangeLocation',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
    ],

    function($, frontTopPanel, fb, frontMemberEditControl, frontMemberChangeLocation, noty) {
        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init();

        frontMemberEditControl.init();

        frontMemberChangeLocation.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
