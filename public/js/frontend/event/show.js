require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontListSuggestCategory',
	'noty',
    'frontEventInviteFriend',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontListSuggestCategory, noty, frontEventInviteFriend) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		
		frontListSuggestCategory.init();

        frontEventInviteFriend.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);