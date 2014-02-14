require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontMemberEditControl',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie',
    'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places',
    'http://connect.facebook.net/en_US/all.js'
],
    function($, frontTopPanel, fb) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
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
