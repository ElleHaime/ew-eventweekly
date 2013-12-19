require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontCampaignEditControl',
	'datetimepicker',
	'utils',	
	//'resizer',
	'noti',
	'domReady',		
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontCampaignEditControl, datetimepicker, utils) {
		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		frontCampaignEditControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);
