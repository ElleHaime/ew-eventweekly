require([
	'jquery',
	'fb',
	'frontEventEditControl',
	'normalDatePicker',
	'utils',
	'noty',
	'domReady',		
	'underscore',
	'jCookie'
	], 
	function($, fb, frontEventEditControl, normalDatePicker, utils, noty) {
		fb.init(); 
		frontEventEditControl.init();

		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
