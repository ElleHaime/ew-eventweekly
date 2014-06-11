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
        
        /*FB.Event.subscribe('auth.authResponseChange', function(response) {
        	alert('123');
        	response.relocate = true;
        	
        	fb.__getLoginResponse(response);
        }); */
        
        FB.Event.subscribe('auth.statusChange', function(response) {
        	if (response.authResponse != null) {
            	response.relocate = true;
            	
            	fb.__getLoginResponse(response);
        	} else {
        		alert('You are not logged in');
        		window.close();
        		window.opener.location.href = "/map";
        	}
        });
    }
);
