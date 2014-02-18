require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontEventEditControl',
	'normalDatePicker',
	'utils',
	'noty',
	'domReady',		
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontEventEditControl, normalDatePicker, utils, noty) {
		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		frontEventEditControl.init();

		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);


