require([
    'jquery',
    'frontTopPanel',
    'fb',
    'frontMemberEditControl',
    'noti',
    'utils',
    'domReady',
    'underscore',
    'jCookie',
    'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places',
    'http://connect.facebook.net/en_US/all.js#xfbml=1&appId=166657830211705'
],
    function($, frontTopPanel, fb, frontMemberEditControl) {

        frontTopPanel.init({
            searchCityBlock: '.searchCityBlock'
        });
        fb.init({
            appId: '166657830211705',
            status: true
        });

        frontMemberEditControl.init();

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
        }
    }
);
