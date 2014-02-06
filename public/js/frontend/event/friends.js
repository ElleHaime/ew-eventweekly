require([
	'jquery',
	'frontTopPanel',
	'fb',
    'frontEventLike',
    'eventFriendControl',
	'noti',	
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontEventLike, eventFriendControl, noti) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		noti.init();
        frontEventLike.init();

        eventFriendControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);
