require([
	'jquery',
	'frontTopPanel',
	'fb',
	'utils',
	'noti',
	'domReady',
	'underscore',
	'jCookie',	
	'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places',
	'http://connect.facebook.net/en_US/all.js#xfbml=1&appId=166657830211705'
	], 
	function($, frontTopPanel, fb) {

		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init({
    				appId: '166657830211705',
		            status: true
		        }); 
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);
