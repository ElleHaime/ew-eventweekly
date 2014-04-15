require([
	'jquery',
	'fb',
	'frontEventLike',
    'noty',
    'listListener',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, fb, frontEventLike, noty, listListener) {
		fb.init();
		
		frontEventLike.init();
		//listListener.init();

		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
