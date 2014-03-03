require([
	'jquery',
	'fb',
	'frontListSuggestCategory',
	'noty',
    'frontEventInviteFriend',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, fb, frontListSuggestCategory, noty, frontEventInviteFriend) {
		fb.init(); 
		
		frontListSuggestCategory.init();

        frontEventInviteFriend.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);