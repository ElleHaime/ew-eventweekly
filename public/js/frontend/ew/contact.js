require([
	'jquery',
	'fb',
	'noty',
	'signupControl',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, fb, noty, signupControl) {
		fb.init(); 
		signupControl.init();

		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
