require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontCampaignEditControl',
	'datetimepicker',
	'utils',	
	//'resizer',
	'noty',
	'domReady',		
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontCampaignEditControl, datetimepicker, utils, noty) {
		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		frontCampaignEditControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
