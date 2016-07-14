require([
    'jquery',
    'fb',
    'frontMemberEditControl',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie',
    'google!maps,3,other_params:key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places',
    'http://connect.facebook.net/en_US/all.js'
],
    function($, fb) {
        fb.init({
            appId: window.fbAppId,
            status: true
        });

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);
