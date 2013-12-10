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
		'frontTopPanel': 'frontend/topPanel',
		'frontEventControl': 'frontend/eventControls'
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

	waitSeconds: 10,
	deps: ['require'],

	callback: function(require) {
        'use strict';

        var fileName = location.pathname.match(/(\/\w*)*$/),
            moduleName;

        if (!fileName || fileName[0] == '/' || fileName[0] == '') {
        	moduleName = 'frontend/index';
        } else {
        	moduleName = 'frontend' + fileName[0];
        };

  		require([moduleName]);
    }	
});
