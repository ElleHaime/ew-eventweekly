require([
	'jquery',
	'frontTopPanel',
	'fb',
	'frontCampaignEditControl',
	'datetimepicker',
	'utils',	
	'noti',
	'domReady',		
	'underscore',
	'jCookie',	
	'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places',
	'http://connect.facebook.net/en_US/all.js#xfbml=1&appId=423750634398167'
	], 
	function($, frontTopPanel, fb, frontCampaignEditControl, datetimepicker, utils) {
		frontTopPanel.init({
					searchCityBlock: '.searchCityBlock'	
				});
		fb.init({
		            appId: '303226713112475', //'423750634398167',
		            status: true
		        }); 
		frontCampaignEditControl.init();
		
		if ($('#splash_messages').length > 0) {
			var fMessage = $('#splash_messages');
			noti.createNotification(fMessage.attr('flashMsgText'), fMessage.attr('flashMsgType'));
		}
	}
);
