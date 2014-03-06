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
		'noty': 'library/vendors/noty/js/noty/packaged/jquery.noty.packaged',
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
        'frontCounterUpdater': 'frontend/general/counterUpdater',

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
        },
        'noty' : {
            deps: ['jquery'],
            exports: 'noty'
        }
	},

	deps: ['require'],

	callback: function(require) {
        'use strict';

        require(['jquery', 'noty'], function(){
            $.noty.defaults = {
                layout: 'ew',
                theme: 'ew',
                type: 'alert',
                text: '', // can be html or string
                dismissQueue: true, // If you want to use queue feature set this true
                template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
                animation: {
                    open: {height: 'toggle'},
                    close: {height: 'toggle'},
                    easing: 'swing',
                    speed: 200 // opening & closing animation speed
                },
                timeout: 10000, // delay for closing event. Set false for sticky notifications
                force: false, // adds notification to the beginning of queue when set to true
                modal: false,
                maxVisible: 1, // you can set max visible notification for dismissQueue true option,
                killer: false, // for close all notifications before show
                closeWith: ['button'], // ['click', 'button', 'hover']
                callback: {
                    onShow: function() {},
                    afterShow: function() {},
                    onClose: function() {},
                    afterClose: function() {}
                },
                buttons: false // an array of buttons
            };
        });

        window.fbAppId = document.getElementById('fbAppId').value;
        window.fbAppSecret = document.getElementById('fbAppSecret').value;
        var moduleName, fileName = '',
        	re = /(\/[a-zA-Z-_]+)*(\/\d+){1}$/,
            //re1 = /\/event\/(\d+){1}\-([a-zA-Z0-9\-_]+)*$/;
            re1 = /\/([a-zA-Z0-9\-_]+)*\-(\d+){1}$/;
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

        require(['jquery', 'frontSearchPanel', 'frontTopPanel', 'frontCounterUpdater', 'bootstrap'], function($, frontSearchPanel, frontTopPanel, frontCounterUpdater, bootstrap){
            $('.tooltip-text').tooltip();
            frontSearchPanel.init();

            if (moduleName != 'frontend/map') {
                frontCounterUpdater.init();
            }

            frontTopPanel.init({
                searchCityBlock: '.searchCityBlock'
            });
        });
    }	
});
