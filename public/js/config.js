/*window.onerror = ErrorLog;
function ErrorLog (msg, url, line) {
    console.log("error: " + msg + "\n" + "file: " + url + "\n" + "line: " + line);
    return true; // avoid to display an error message in the browser
} */

require.config({
	baseUrl: '/js',
	paths: {
		// plugins
		'async': 'requirePlugins/async',
		'google': 'requirePlugins/google',
		'propertyParser': 'requirePlugins/propertyParser',
		'domReady': 'requirePlugins/domReady',
		
		// utils
		'utils': 'library/utils/utils',
		'base': 'library/base',

		// vendors
		'jquery': 'https://code.jquery.com/jquery',
		'jCookie': 'library/vendors/jquery.cookie',
		'underscore': 'library/vendors/underscore',
		'bootstrap': 'library/vendors/bootstrap.min',
		'datetimepicker': 'library/vendors/datetimepicker.min',

		// maps
		'gmap': 'library/maps/gmap',
		'gmapEvents': 'library/maps/gmapEvents',

		// facebook
		'fb': 'library/facebook/fb',

		//frontend
		'noti': 'frontend/general/noti',
		'signupControl': 'frontend/signup/signupControl',
		'frontListEventLike': 'frontend/list/eventLike',
		'frontListSuggestCategory': 'frontend/list/suggestCategory',
		'frontTopPanel': 'frontend/general/topPanel',
		'frontEventEditControl': 'frontend/event/controls/eventEditControl',
		'frontEventListControl': 'frontend/event/controls/eventListControl',
		'frontCampaignEditControl': 'frontend/campaign/controls/campaignEditControl',
		'frontCampaignListControl': 'frontend/campaign/controls/campaignListControl'
	},

	shim: {
		'underscore': {
            exports: '_'
        },
        'datetimepicker': {
        	deps: ['jquery'],
        	exports: 'datetimepicker'
        }
	},

	//waitSeconds: 10,
	deps: ['require'],

	callback: function(require) {
        'use strict';

        var moduleName,
        	re = /(\/[a-zA-Z-_]+)*(\/\d+){1}$/;
        if (re.test(location.pathname) != 'undefined') {
			var fileName = location.pathname.replace(/(\/\d+)?$/, '');
        } else {
        	var fileName = location.pathname.match(/(\/\w+)*?$/)
        }
//console.log(fileName);

        if (!fileName || fileName == '/' || fileName == '') {
        	moduleName = 'frontend/index';
        } else {
        	moduleName = 'frontend' + fileName;
        };
//console.log(moduleName);
  		require([moduleName]);
    }	
});
