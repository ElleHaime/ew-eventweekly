require([
	'jquery',
	'fb',
	'noty',
    'frontEventInviteFriend',
    'eventSliderControl',
    'frontEventLike',
    'idangerous',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, fb, noty, frontEventInviteFriend, eventSliderControl, frontEventLike) {
		fb.init(); 
        frontEventInviteFriend.init();
        frontEventLike.init();
        eventSliderControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);