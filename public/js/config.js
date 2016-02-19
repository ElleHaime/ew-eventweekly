require.config({
	baseUrl: '/js',
    urlArgs: "bust=" + (new Date()).getTime(),
    waitSeconds: 240,
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
		'fbSdk': 'http://connect.facebook.net/en_US/sdk',
        'lazyload': 'library/vendors/lazyload',
		'jCookie': 'library/vendors/jquery.cookie',
		'jTruncate': 'library/vendors/jquery.truncate',
		'underscore': 'library/vendors/underscore',
		'resizer': 'library/vendors/resizer',
		'bootstrap': 'library/vendors/bootstrap',
		'datetimepicker': 'library/vendors/datetimepicker.min',
		'bootstrapDatepicker': 'library/vendors/bootstrap-datepicker',
		'normalDatePicker': 'library/vendors/normalBootstrapDateTimepicker',
		'niceDate': 'library/vendors/date',
		'idangerous': '../_new-layout-eventweekly/libs/idangerous.swiper/idangerous.swiper.min',

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
		'lazyLoader': 'frontend/general/lazyLoader',
		'signupControl': 'frontend/signup/signupControl',
		'frontListSuggestCategory': 'frontend/list/suggestCategory',
		'frontTopPanel': 'frontend/general/topPanel',
		'frontFilterPanel': 'frontend/general/filterPanel',
		'frontEventEditControl': 'frontend/event/controls/eventEditControl',
		'frontEventListControl': 'frontend/event/controls/eventListControl',
		'frontEventInviteFriend': 'frontend/event/controls/eventInviteFriend',
		'frontCampaignEditControl': 'frontend/campaign/controls/campaignEditControl',
		'frontCampaignListControl': 'frontend/campaign/controls/campaignListControl',
        'frontMemberEditControl': 'frontend/member/controls/memberEditControl',
        'frontMemberChangeLocation': 'frontend/member/controls/memberChangeLocation',
        'frontSearchPanel': 'frontend/general/searchPanel',
        'eventFriendControl': 'frontend/event/controls/eventFriendControl',
        'eventSliderControl': 'frontend/event/controls/eventSliderControl',
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
        'googleInfoWindow': 'library/google/infoWindow',
        'llCalc': 'library/google/llCalc'
	},

	shim: {
		'underscore': {
            exports: '_'
        },
        'lazyload': {
        	deps: ['jquery'],
        	exports: 'lazyload'
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
        'idangerous' : {
            deps: ['jquery'],
            exports: 'idangerous'
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
        window.fbAppVersion = document.getElementById('fbAppVersion').value;
        
        var moduleName, fileName = '', isPreview = 0,
        	freelisting = /\/freelisting/,
        	re = /(\/[a-zA-Z-_]+)*(\/[\d_]+){1}$/,
            re1 = /\/([a-zA-Z0-9\-_]+)*\-([\d_]+){1}$/,
            restoreRel = /\/auth\/reset\/.+/,
            trendingRel = /\/[a-zA-Z\-\s]+\/trending/,
        	featuredRel = /^\/[a-zA-Z\-\s]+$/,
        	whatsonRel = /^(\/whats\-on\-in){1}[a-zA-Z\-]+$/,
        	seoDaysRel = /^\/[a-z\-]+\/(personalised\/)?(today|tomorrow|this-week|this-weekend)$/,
        	seoDatesRel = /^\/[a-z\-]+\/(personalised\/)?[a-z0-9]+(\-[a-z0-9]+)?$/;
        
        if (whatsonRel.test(location.pathname)) {
		} else if (freelisting.test(location.pathname)) {
        	fileName = '/'
        } else if (restoreRel.test(location.pathname)) {
            fileName = '/auth/restore'
        } else if (re1.test(location.pathname) == true) {
            fileName = '/event/show';
        } else if (featuredRel.test(location.pathname.replace(/%20/ig, ' '))) {
        	fileName = '/event/featured';
        } else if (trendingRel.test(location.pathname.replace(/%20/ig, ' '))) {
        	fileName = '/event/trending';
        } else if (re.test(location.pathname) != 'undefined') {
            fileName = location.pathname.replace(/(\/[\d_]+)?$/, '');
        } else if (seoDaysRel.test(location.pathname) == true || seoDatesRel.test(location.pathname) == true) {
        	fileName = '/search/list';
        } else {
            fileName = location.pathname.match(/(\/\w+)*?$/)
        } 

        if (!fileName || fileName == '/' || fileName == '') {
        	moduleName = 'frontend/index';
        } else {
        	moduleName = 'frontend' + fileName;
        };
        
        if (moduleName == 'frontend/event/preview') {
        	isPreview = 1;
        }
console.log(moduleName);
  		require([moduleName]);

  		if (moduleName != 'frontend/member/login' && moduleName != 'frontend/auth/fbauthresponse' && isPreview != 1) {
	        require(['jquery', 'frontSearchPanel', 'frontTopPanel', 'frontFilterPanel', 'frontCounterUpdater', 'bootstrap'], 
	      		function($, frontSearchPanel, frontTopPanel, frontFilterPanel, frontCounterUpdater, bootstrap)
	      		{
		            //$('.tooltip-text').tooltip();
		            
		            frontSearchPanel.init();
		            frontFilterPanel.init();
		            frontTopPanel.init();
	        	}
	       );
  		}
    }	
});
