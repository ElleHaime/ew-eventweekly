require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontEventLike',
	'noti',
    'frontSearchPanel',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontEventLike, noti, frontSearchPanel) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init();
		noti.init();
		
		frontEventLike.init();

        frontSearchPanel.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);
