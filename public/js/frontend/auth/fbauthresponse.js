require([
    'jquery',
    'fb',
    'noty',
    'utils',
    'domReady',
    'underscore',
    'jCookie',
    'http://connect.facebook.net/en_US/all.js'
],
    function($, fb, noty) {
        fb.init({
            appId: window.fbAppId,
            status: true
        });
        
        FB.Event.subscribe('auth.authResponseChange', function(response) {
        	response.relocate = true;
        	
        	fb.__getLoginResponse(response);
        });
    }
);
