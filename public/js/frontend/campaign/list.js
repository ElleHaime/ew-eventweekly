require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontCampaignListControl',
	'datetimepicker',
	'utils',
	'noti',
	'domReady',		
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontCampaignListControl, datetimepicker, utils) {
		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		frontCampaignListControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);
