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
		'normalDatePicker': 'library/vendors/normalBootstrapDateTimepicker',
		'niceDate': 'library/vendors/date',

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
        'eventFriendControl': 'frontend/event/controls/eventFriendControl',
        'profileChangePasswordControl': 'frontend/profile/controls/profileChangePasswordControl',
        'profileRestorePasswordControl': 'frontend/profile/controls/profileRestorePasswordControl',

        'SingleEvent': 'frontend/list/singleEvent',
        'listListener': 'frontend/list/listListener',

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
        'normalDatePicker': {
        	deps: ['jquery'],
        	exports: 'normalDatePicker'
        },
        'bootstrap' : {
            deps: ['jquery']
        }
	},

	deps: ['require'],

	callback: function(require) {
        'use strict';

        window.fbAppId = document.getElementById('fbAppId').value;
        window.fbAppSecret = document.getElementById('fbAppSecret').value;
        var moduleName, fileName = '',
        	re = /(\/[a-zA-Z-_]+)*(\/\d+){1}$/,
            re1 = /\/event\/(\d+){1}\-([a-zA-Z0-9\-_]+)*$/;
        if (re1.test(location.pathname) == true) {
            fileName = '/event/show';
        } else if (re.test(location.pathname) != 'undefined') {
            fileName = location.pathname.replace(/(\/\d+)?$/, '');
        } else {
            fileName = location.pathname.match(/(\/\w+)*?$/)
        }

        var restoreRel = /\/reset\/.+/;
        if (restoreRel.test(location.pathname)) {
            fileName = '/restore'
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
