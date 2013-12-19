require([
	'jquery',
	'frontTopPanel',
	'fb',
	'utils',
	'noti',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);
