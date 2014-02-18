require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontCampaignListControl',
	'datetimepicker',
	'utils',
	'noty',
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
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
