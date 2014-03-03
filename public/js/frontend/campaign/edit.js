require([
	'jquery',
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
	function($, fb, frontCampaignEditControl, datetimepicker, utils, noty) {
		fb.init(); 
		frontCampaignEditControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
