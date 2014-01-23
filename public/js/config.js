/*window.onerror = ErrorLog;
function ErrorLog (msg, url, line) {
    console.log("error: " + msg + "\n" + "file: " + url + "\n" + "line: " + line);
    return true; // avoid to display an error message in the browser
} */

require.config({
	baseUrl: '/js',
   // urlArgs: "bust=" + (new Date()).getTime(),
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
		'jTruncate': 'library/vendors/jquery.truncate',
		'underscore': 'library/vendors/underscore',
		'resizer': 'library/vendors/resizer',
		'bootstrap': 'library/vendors/bootstrap.min',
		'datetimepicker': 'library/vendors/datetimepicker.min',
		'bootstrapDatepicker': 'library/vendors/bootstrap-datepicker',
		'niceDate': 'library/vendors/date',
        'jTruncate': 'library/vendors/jquery.truncate',

		// maps
		'gmap': 'library/maps/gmap',
		'gmapEvents': 'library/maps/gmapEvents',
		'eventsPointer': 'library/maps/eventsPointer',

		// facebook
		'fb': 'library/facebook/fb',

		//frontend
		'noti': 'frontend/general/noti',
		'frontEventLike': 'frontend/general/eventLike',		
		'signupControl': 'frontend/signup/signupControl',
		'frontListSuggestCategory': 'frontend/list/suggestCategory',
		'frontTopPanel': 'frontend/general/topPanel',
		'frontEventEditControl': 'frontend/event/controls/eventEditControl',
		'frontEventListControl': 'frontend/event/controls/eventListControl',
		'frontEventInviteFriend': 'frontend/event/controls/eventInviteFriend',
		'frontCampaignEditControl': 'frontend/campaign/controls/campaignEditControl',
		'frontCampaignListControl': 'frontend/campaign/controls/campaignListControl',
        'frontMemberEditControl': 'frontend/member/controls/memberEditControl',
        'frontMemberChangeLocation': 'frontend/member/controls/memberChangeLocation',
        'frontSearchPanel': 'frontend/general/searchPanel',


        // New
        'newGmapEvents': 'library/maps/newGmapEvents',
        'googleMap': 'library/google/map',
        'googleMc': 'library/google/mc',
        'googleMarker': 'library/google/marker',
        'googleInfoWindow': 'library/google/infoWindow'
	},

	shim: {
		'underscore': {
            exports: '_'
        },
        'datetimepicker': {
        	deps: ['jquery'],
        	exports: 'datetimepicker'
        },
        'jTruncate': {
        	deps: ['jquery'],
        	exports: 'jQuery'
        },
        'bootstrapDatepicker': {
        	deps: ['jquery'],
        	exports: 'bootstrapDatepicker'
        },
        'bootstrap' : {
            deps: ['jquery']
        }
	},

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

        if (!fileName || fileName == '/' || fileName == '') {
        	moduleName = 'frontend/index';
        } else {
        	moduleName = 'frontend' + fileName;
        };

        console.log('Call module: '+moduleName);

  		require([moduleName]);

        require(['jquery', 'frontSearchPanel', 'bootstrap'], function($, frontSearchPanel, bootstrap){
            $('.tooltip-text').tooltip();
            frontSearchPanel.init();
        });
    }	
});
