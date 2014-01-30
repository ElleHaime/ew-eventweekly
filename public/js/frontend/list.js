require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontEventLike',
	'noti',
    'listListener',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontEventLike, noti, listListener) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init();
		noti.init();
		
		frontEventLike.init();

        /*var listListener = new listListener({
            eventsBlock: '.active-events'
        });*/
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);
