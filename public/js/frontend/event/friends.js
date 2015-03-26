require([
	'jquery',
	'fb',
    'frontEventLike',
    'eventFriendControl',
	'noty',
	'lazyLoader',		
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, fb, frontEventLike, eventFriendControl, noty, lazyLoader) {
		fb.init();
        frontEventLike.init();
        lazyLoader.init();

        eventFriendControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
