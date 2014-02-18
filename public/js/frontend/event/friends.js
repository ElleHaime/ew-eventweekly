require([
	'jquery',
	'frontTopPanel',
	'fb',
    'frontEventLike',
    'eventFriendControl',
	'noty',
	'utils',
	'domReady',
	'underscore',
	'jCookie'
	], 
	function($, frontTopPanel, fb, frontEventLike, eventFriendControl, noty) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init();
        frontEventLike.init();

        eventFriendControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
            noty({text: fMessage.attr('flashMsgText'), type: fMessage.attr('flashMsgType')});
		}
	}
);
