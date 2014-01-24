define('fb',
	['jquery', 'utils', 'noti', 'http://connect.facebook.net/en_US/all.js#xfbml=1&appId=166657830211705'],
	function($, utils, noti) {

		function fb($, utils) 
		{
			var self = this;

			self.permissions = 'email,user_activities,user_birthday,user_groups,user_interests,user_likes,' +
								'user_groups,user_interests,user_likes,user_location,user_checkins,user_events,' +
								'friends_birthday,friends_groups,friends_interests,friends_likes,friends_location,' +
								'friends_checkins,friends_events,publish_actions,publish_stream,read_stream,' +
								'create_event,rsvp_event,read_friendlists,manage_friendlists,read_insights';
			self.settings = {
                userEventsGoing: '#userEventsGoing',
                currentEventIdBox: '#current_event_id',

				btnLogin: '#fb-login',
				btnInvite: '#fb-invite',
				btnEventGoing: '#event-join',
				btnEventMaybe: '#event-maybe',
				btnEventDecline: '#event-decline',
				btnEventShare: '#event_share', 
				errorBox: '#login_message',
				status: true,
				appId: '',

                isLogged: '#isLogged'
			};
			self.eventStatuses = {
				join: 'JOIN',
				maybe: 'MAYBE',
				decline: 'DECLINE'
			};
			self.userData = [
				'first_name',
				'last_name', 
				'email', 
				'current_location', 
				'current_address', 
				'username', 
				'pic_big'
			];
			self.accessToken = '';
			self.accessUid = '';

			self.shareImg = '/img/logo200.png';
			self.firstPage = '/map';


			self.init = function(options)
			{
                /*self.settings = $.extend(self.settings, options);

				if (_.isNull(FB)) {
					FB.init({
			            appId: self.settings.appId,
			            status: self.settings.status
			        });
                 }*/

			    self.bindEvents();
			}

			self.bindEvents = function()
			{
				$(self.settings.btnLogin).click(function(e) {
					self.__login();
				});

				/*$(self.settings.btnInvite).click(function(e) {
					self.__inviteFriends();
				});*/

				$(self.settings.btnEventGoing).click(function(e) {
                    if ($(self.settings.isLogged).val() != 1) {
                        noti.createNotification('Please <a href="#" class="fb-login-popup" onclick="return false;">login via Facebook</a> to be able do this', 'warning');
                        return false;
                    }

					self.__goingEvent();
                    self.__shareEvent();
				});

				$(self.settings.btnEventMaybe).click(function(e) {
					self.__changeUserEventState(self.eventStatuses.maybe);
				});

				$(self.settings.btnEventDecline).click(function(e) {
					self.__changeUserEventState(self.eventStatuses.decline);
				});

				$(self.settings.btnEventShare).click(function(e) {
					self.__shareEvent();
				});
			}

			self.__login = function()
			{
			 	FB.login(
		            function(response) {
		                if (response.authResponse) {
		                    self.accessToken = response.authResponse.accessToken;
		                    self.accessUid = response.authResponse.userID;
		                    authParams = { uid: self.accessUid, 
		                    			   access_token: self.accessToken };

		                    $.when(self.__request('post', '/fblogin', authParams)).then(function(data) {
		                    		data = $.parseJSON(data);
		                    		if (data.status == 'OK') {
		                    			var userData = self.userData.join(',');
		                    			FB.api({
							                method: 'fql.query',
						               		query: 'SELECT ' + userData + ' FROM user WHERE uid = ' + self.accessUid },
							               	function(facebookData) {
							               		if (!facebookData) {
							               			alert('Can\'t get your info from FB acc');
							               			return false;
							               		}
							               		self.__register(facebookData[0]);
							               	});
		                    		} else {
		                    			alert('I can\'t authorize you, sorry, bro');
		                    		}
		                    	}); 
		                } else {
		                    alert('You need to be logged in.');
		                }
		            },

 					{scope: self.permissions}
 				);
 			}


			self.__register = function(data)
			{
				params = { uid: self.accessUid,
                           token: self.accessToken,
                           address: data.current_address,
                           location: data.current_location,
                           email: data.email,
                           logo: data.pic_big,
                           first_name: data.first_name,
                           last_name: data.last_name,
                           username: data.username };
                $.when(self.__request('post', '/fbregister', params)).then(function(response) {
                	data = $.parseJSON(response);
                	if (data.status == 'OK') {
                		window.location.href = self.firstPage;
                	} else {
                		$(self.settings.errorBox).html('Facebook return empty result :(');
		                $(self.settings.errorBox).show();
                	}
                });
			}

			self.__shareEvent = function()
			{
                var image = $(self.settings.currentEventIdBox + ' img');

		 		FB.ui({ picture: window.location.host + image.attr('src'),
		            	method: 'feed',
		            	link: window.location.href,
		            	caption: 'I am attending event at Event Weekly!'
		        }, function(response){});
			}

			self.__inviteFriends = function()
			{
				FB.ui({ method: 'send',
		            	link: window.location.href
				});
			}

			self.__changeUserEventState = function(status)
			{
                if ($(self.settings.isLogged).val() != 1) {
                    noti.createNotification('Please <a href="#" class="fb-login-popup" onclick="return false;">login via Facebook</a> to be able do this', 'warning');
                    return false;
                }

				$(self.settings.btnEventGoing).hide();
				$(self.settings.btnEventMaybe).hide();
				$(self.settings.btnEventDecline).hide();

				var params = { 
						answer: status, 
						event_id : $('#current_event_id').attr('event') 
				};
				
				$.when(utils.request('post', '/event/answer', params)).then(function(data) {
					data = $.parseJSON(data);
                    console.log(data);
					if (data.status == 'OK') {
						$('#event-' + data.event_member_status.toLowerCase()).show();
						$('#event-' + data.event_member_status.toLowerCase()).prop('disabled',true);

                        self.__plusUserEventsGoing();

						return true;
					} else {
						if (data.error == 'not_logged') {
							//window.location.href = '/#fb-login';
                            noti.createNotification('Please <a href="#" class="fb-login-popup" onclick="return false;">login via Facebook</a> to be able do this', 'warning');
							return false;
						}
					}
				});
			}

            self.__plusUserEventsGoing = function()
            {
                var counter = parseInt($(self.settings.userEventsGoing).text()) + 1;
                $(self.settings.userEventsGoing).text(counter);
            }

			self.__goingEvent = function()
			{
				if (self.__changeUserEventState(self.eventStatuses.join)) {
			        FB.ui({
			            picture: window.location.host + self.shareImg,
			            method: 'feed',
			            link:   window.location.href,
			            caption: 'You are joined event'
			        }, function(response) {});
				}
			}

			self.__request = function(method, url, params)
			{
				return $.ajax({ url: url, 
						 		data: params,
						 		type: method});
			}
		}

		return new fb($, utils, noti);
	}
);

