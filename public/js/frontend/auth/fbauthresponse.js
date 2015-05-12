require([
    'jquery',
    'fb',
    'noty',
    'fbSdk',
    'utils',
    'domReady',
    'underscore',
    'jCookie'
],
    function($, fb, noty, fbSdk) {
        fb.init();

        FB.Event.subscribe('auth.statusChange', function(response) {
        	if (response.authResponse != null) {
            	response.relocate = true;
            	
            	fb.__getLoginResponse(response);
        	} else {
        		alert('You are not logged in');
        		window.close();
        		window.opener.location.href = "/";
        	}
        });
    }
);
