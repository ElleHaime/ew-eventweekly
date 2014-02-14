require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontEventLike',
    'noty',
    'listListener',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontEventLike, noty, listListener) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init();
		
		frontEventLike.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
