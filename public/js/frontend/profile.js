require([
    'jquery',
    'fb',
    'frontMemberEditControl',
    'frontMemberChangeLocation',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
    ],

    function($, fb, frontMemberEditControl, frontMemberChangeLocation, noty) {
        fb.init();

        frontMemberEditControl.init();

        frontMemberChangeLocation.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
