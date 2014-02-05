require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontEventEditControl',
	'normalDatePicker',
	'utils',
	//'resizer',
	'noti',
	'domReady',		
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontEventEditControl, normalDatePicker, utils, noti) {
		noti.init();
		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		frontEventEditControl.init();

		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);


