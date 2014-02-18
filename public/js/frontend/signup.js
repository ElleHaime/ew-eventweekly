require([
	'jquery',
	'frontTopPanel',
	'fb',
	'noty',
	'signupControl',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, noty, signupControl) {
		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		signupControl.init();

		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
