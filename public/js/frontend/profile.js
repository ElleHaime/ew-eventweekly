require([
    'jquery',
    'fb',
    'frontMemberEditControl',
    'frontMemberChangeLocation',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie',
    'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'
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
