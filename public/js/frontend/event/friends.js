require([
	'jquery',
	'fb',
    'frontEventLike',
    'eventFriendControl',
	'noty',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, fb, frontEventLike, eventFriendControl, noty) {
		fb.init();
        frontEventLike.init();

        eventFriendControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
