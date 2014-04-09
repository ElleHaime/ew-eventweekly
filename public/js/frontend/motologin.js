require([
    'jquery',
    'fb',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, noty) {
        fb.init();

        if (window.opener) {
            $("#fb-login").click(function(){
                window.opener.$('#popupRedirect').val('reload');
            });
        }

        if ($('#splash_messages').length > 0) {
            var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
        }
    }
);

