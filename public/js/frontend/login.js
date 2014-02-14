require([
	'jquery',
	'frontTopPanel',
	'fb',
    'noty',
    'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, noty) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init(); 
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
