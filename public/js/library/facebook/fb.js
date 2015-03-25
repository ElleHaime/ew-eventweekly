define('fb',
	['jquery', 'utils', 'noty', 'http://connect.facebook.net/en_US/sdk.js'],
	function($, utils, noty) {

		function fb($, utils, noty)
		{
			var self = this;

            self.permissions = 'email,user_likes,user_location,user_events,' +
					            'user_friends,friends_events,publish_actions,publish_stream,' +
					            'create_event,rsvp_event,read_friendlists,read_insights,manage_pages';
            
            self.basicPermList = ['basic_info', 'email', 'friends_events', 'installed', 'public_profile', 'read_friendlists', 'user_events', 'user_friends', 'user_likes', 'user_location'];
            self.publishPermList = ['create_note', 'photo_upload', 'publish_actions', 'publish_checkins', 'publish_stream', 'share_item', 'status_update', 'video_upload'];
            self.managePermList = ['create_event', 'manage_pages', 'read_insights', 'rsvp_event'];

			self.settings = {
                userEventsGoing: '#userEventsGoing',
                currentEventIdBox: '#current_event_id',

				btnLogin: '#fb-login',
				btnInvite: '#fb-invite',
				btnEventGoing: '#event-join',
				btnEventMaybe: '#event-maybe',
				btnEventDecline: '#event-decline',
				btnEventShare: '.share-event',
				errorBox: '#login_message',
				status: true,

                isLogged: '#isLogged',
                externalLogged: '#external_logged',
                
                pageWasChanged: '#pageWasChanged'
			};
			self.eventStatuses = {
				join: 'JOIN',
				maybe: 'MAYBE',
				decline: 'DECLINE'
			};
			self.userData = {};
			self.accessToken = '';
			self.accessUid = '';
			self.accessType = 'login';
			self.relocateAfterLogin = true;

			self.shareImg = '/img/logo200.png';
			self.firstPage = '/map';
			self.demoPage = '/search/map?searchTitle=&searchLocationField=Dublin%2C+Ireland&searchLocationLatMin=51.4221955&searchLocationLngMin=-10.6694501&searchLocationLatMax=55.3884899&searchLocationLngMax=-5.99471&searchLocationType=country&&searchStartDate=&searchEndDate=&searchCategory%5B%5D=2&searchTag=racing&searchCategoriesType=global&searchType=in_map';
			self.reDemo = /.*\/motologin$/;
			self.demoLat = '53.34460075';
			self.demoLng = '-6.26577123';
			self.demoCity = 'Dublin';
			self.demoCookiePath = '/';
			

			self.init = function(options)
			{
                FB.init({
                    appId: window.fbAppId,
                    version: window.fbAppVersion,
                    status: self.settings.status
                });
                
                FB.XFBML.parse();

			    self.bindEvents();
			}

			self.bindEvents = function()
			{
				$(self.settings.btnLogin).click(function(e) {
					if (window.opener) {
						window.location = encodeURI("https://www.facebook.com/dialog/oauth?client_id="+window.fbAppId+"&scope=" + self.permissions + "&redirect_uri=http://" + window.location.host + "/auth/fbauthresponse&response_type=token");
					} else {
						self.__login();
					} 
				});

				$(self.settings.btnEventGoing).click(function(e) {
					status = self.__checkLoginStatus();
					
                    if (status === 'not_logged') {
                    	if ($(self.settings.isLogged).val() != 1) {
	                    	noty({text: 'Please <a href="#" class="fb-login-popup" onclick="return false;">login</a> to be able do this', type: 'warning'});
	                    	return false;
                    	} else {
                    		self.__goingEvent();
                    	}
                    } else if(status === 'session_expired') {
                    	self.__goingEvent();
                    	noty({text: 'Your facebook authorization has expired =/ We can\'t share your action. <br>Please <a href="#" class="fb-login-popup" onclick="return false;">re-auth via Facebook</a> to be able to share this', type: 'info', timeout: false, maxVisible: 20});
                    } else {
                    	self.__goingEvent();
                    	self.__shareEvent(this);
                    }
				});

				$(self.settings.btnEventMaybe).click(function(e) {
					self.__changeUserEventState(self.eventStatuses.maybe);
				});

				$(self.settings.btnEventDecline).click(function(e) {
					self.__changeUserEventState(self.eventStatuses.decline);
				});

				$(self.settings.btnEventShare).click(function(e) {
					self.__shareEvent(this);
				});
				
				$(self.settings.pageWasChanged).change(function(event) {
		        	$(self.settings.btnEventShare).each(function() {
           				$(this).unbind('click').bind('click', function() {
           					self.__shareEvent(this);		
           				});
           			});
		        });
			}
			
			self.__login = function()
			{
			 	FB.login(function(response) {
			 		self.__getLoginResponse(response);
			 	}, 
			 	{scope: self.permissions});
			}


			self.__register = function(data)
			{
				params = self.userData;
				params.uid = self.accessUid;
				params.token = self.accessToken;

                $.when(self.__request('post', '/fbregister', params)).then(function(response) {
                	data = $.parseJSON(response);
                	if (data.status == 'OK') {
                		self.__checkPermissions();
                	} else {
                		$(self.settings.errorBox).html('Facebook return empty result :(');
		                $(self.settings.errorBox).show();
                	}
                });  
			}
			
			self.__getLoginResponse = function(response)
			{
				if (response.status === 'connected') {
					 self.accessToken = response.authResponse.accessToken;
					 self.accessUid = response.authResponse.userID;
					 if (response.relocate != undefined) {
						 self.relocateAfterLogin = false;
					 }
					 
	                 self.__getLoginData();
			    } else {
			    	alert('You need to be logged in.');
				}
			}
			
			self.__getLoginData = function()
			{
				authParams = { uid: self.accessUid, 
         			   		   access_token: self.accessToken,
         			   		   access_type: self.accessType };

		        $.when(self.__request('post', '/fblogin', authParams)).then(function(data) {
		         		data = $.parseJSON(data);
		         		if (data.status == 'OK') {
		         			if (self.accessType == 'login') {
			         			FB.api('/me', 	function(facebookData) {
					               		if (!facebookData) {
					               			alert('Can\'t get your info from FB acc');
					               			return false;
					               		}
					               		self.userData.user_name = facebookData.name;
					               		if (facebookData.first_name) {
					               			self.userData.first_name = facebookData.first_name;
					               		}
					               		if (facebookData.last_name) {
					               			self.userData.last_name = facebookData.last_name;
					               		}
					               		if (facebookData.email) {
					                    	self.userData.email = facebookData.email;	
					                    }
					        			if (facebookData.location) {
					        				self.userData.locationId = facebookData.location.id;
					        			}
					               		FB.api('/me/picture', function(response) {
	           								if (response) {
	           									self.userData.logo = response.data.url;
	           								}
	           								if (self.userData.locationId) {
	           									FB.api('/' + self.userData.locationId, function(response) {
	           										if (response) {
	           											self.userData.locationLat = response.location.latitude; 
	           											self.userData.locationLng = response.location.longitude;
	           										}
	           										self.__register(facebookData);
	           									});
	           								} else {
	           									self.__register(facebookData);
	           								}
	           							});
					               	});
		         			} else if(self.accessType = 'sync') {
		         				syncResponse = {'status': data.status};
		         				return syncResponse;
		         			}
		         		} else {
		         			alert('I can\'t authorize you, sorry, bro');
		         		}  
		        });  
			}

			self.__checkPermissions = function()
			{
				FB.api(
                	'/me/permissions',
	               	function(permData) {
	               		permission_base = 1;
	               		permission_publish = 1;
	               		permission_manage = 1;
	               		
	               		self.basicPermList.forEach(function(item) {
	    					if (!(item in permData.data[0])) {
	    						permission_base = 0;
	    					}
	    				});
	               		self.publishPermList.forEach(function(item) {
	    					if (!(item in permData.data[0])) {
	    						permission_publish = 0;
	    					}
	    				});
	               		self.managePermList.forEach(function(item) {
	    					if (!(item in permData.data[0])) {
	    						permission_manage = 0;
	    					}
	    				});
	               		
	               		params = {'permission_base': permission_base,
	               				  'permission_publish': permission_publish,
	               				  'permission_manage': permission_manage};

	               		$.when(self.__request('post', '/fbpermissions', params)).then(function(response) {
	               			data = $.parseJSON(response);
	                    	if (data.status == 'OK') {
	                    		if (self.reDemo.test(location.pathname) == true) {
	                                $.cookie('lastLat', self.demoLat, {expires: 1, path: self.demoCookiePath});
	                                $.cookie('lastLng', self.demoLng, {expires: 1, path: self.demoCookiePath});
	                                $.cookie('lastCity', self.demoCity, {expires: 1, path: self.demoCookiePath});
	                                
	                                if (window.opener) {
                                		window.opener.location.href = self.demoPage;
	                                	window.close();
	                                } else {
	                                	window.location.href = self.demoPage;
	                                }  
	                    		} else {
	                    			if (window.opener) {
	                                	if (self.relocateAfterLogin === true) {
	                                		window.opener.location.href = self.firstPage;
	                                	} else {
	                                		window.opener.location.href = self.firstPage;
	                                	}
	                    				window.close();
	                    			} else {
	                    				if (self.relocateAfterLogin == true) {
	                    					window.location.href = self.firstPage;
	                    				} else {
	                    					return;
	                    				}
	                    			}
	                    		}
	                    	} else {
	                    		$(self.settings.errorBox).html('Facebook return empty permissions :(');
	    		                $(self.settings.errorBox).show();
	                    	} 
	               		})
                	});
			}
			
			
			self.__checkLoginStatus = function()
			{
				result = '';
				if ($(self.settings.isLogged).val() != 1 || $(self.settings.externalLogged).length == 0) {
					return 'not_logged';
				} else {
					FB.getLoginStatus(function(response) {
						if (response.status == 'connected') {
							result = 'connected';
						} else if (response.status == 'not_authorized') {
							 if ($(self.settings.externalLogged).length > 0) {
								 result = 'session_expired';
							 } else {
								 result = 'not_logged';
							 }
						} else {
							result = response.status;
						}
					});
				}
				return result;
			}

			self.__shareEvent = function(obj)
			{
                var image = window.location.host + $(obj).data('image-source');
                var source = window.location.host + $(obj).data('event-source');

                FB.ui({ picture: image,
		            	method: 'feed',
		            	link: source,
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
                    noty({text: 'Please <a href="#" class="fb-login-popup" onclick="return false;">login via Facebook</a> to be able do this', type: 'warning'});
                    return false;
                }
                
               	$(self.settings.btnEventGoing).hide();	
               	$(self.settings.btnEventDecline).hide();	

				var params = { 
						answer: status, 
						event_id : $('#current_event_id').attr('event') 
				};

				$.when(utils.request('post', '/event/answer', params)).then(function(data) {
					data = $.parseJSON(data);
					//console.log(data);
					
					if (data.status == 'OK') {
						if (data.event_member_status == self.eventStatuses.decline) {
							$(self.settings.btnEventGoing).show();
						} else {
							$(self.settings.btnEventDecline).show();
						}

                        self.__plusUserEventsGoing();

						return true;
					} else {
						if (data.error == 'not_logged') {
							noty({text: 'Please <a href="#" class="fb-login-popup" onclick="return false;">login via Facebook</a> to be able do this', type: 'warning'});
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

		return new fb($, utils, noty);
	}
);

