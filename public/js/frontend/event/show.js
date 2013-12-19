require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontListSuggestCategory',
	'noti',
    'frontEventInviteFriend',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontListSuggestCategory, noti, frontEventInviteFriend) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		
		frontListSuggestCategory.init();
		noti.init();

        frontEventInviteFriend.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);