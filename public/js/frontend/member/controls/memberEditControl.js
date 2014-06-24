define('frontMemberEditControl',
    ['jquery', 'utils', 'noty', 'domReady'],
    function($, utils, noty) {

        function frontMemberEditControl($, utils, noty)
        {
            var self = this;

            /*self.permissions = 'email,user_activities,user_birthday,user_groups,user_interests,user_likes,' +
                'user_groups,user_interests,user_likes,user_location,user_checkins,user_events,' +
                'friends_birthday,friends_groups,friends_interests,friends_likes,friends_location,' +
                'friends_checkins,friends_events,publish_actions,publish_stream,read_stream,' +
                'create_event,rsvp_event,read_friendlists,manage_friendlists,read_insights,manage_pages', */
            
            self.permissions = 'email,user_likes,user_location,user_events,' +
					            'user_friends,friends_events,publish_actions,publish_stream,' +
					            'create_event,rsvp_event,read_friendlists,read_insights,manage_pages';
            
            self.basicPermList = ['basic_info', 'email', 'friends_events', 'installed', 'public_profile', 'read_friendlists', 'user_events', 'user_friends', 'user_likes', 'user_location'];
            self.publishPermList = ['create_note', 'photo_upload', 'publish_actions', 'publish_checkins', 'publish_stream', 'share_item', 'status_update', 'video_upload'];
            self.managePermList = ['create_event', 'manage_pages', 'read_insights', 'rsvp_event'];

            self.userData = [
                'first_name',
                'last_name',
                'email',
                'current_location',
                'current_address',
                'username',
                'pic_big'
            ],
            self.accessToken = '',
            self.accessUid = '',

            self.settings = {
                btnImg: '#file',
                btnImgUpload: '#logo',
                boxImg: '#img-box',

                inpAddress: '#address',
                listAddress: '#address-list',

                settingsBoxCheckbox: '.settings-box-one .checkbox',
                activeCheckbox: '.settings-box-one .checkbox',
                fieldId: '.fieldId',

                filters: '#filters',
                saveFilterBtn: '#saveFilter',

                inpMemberName: ".profile-info input[name='name']",
                inpMemberExtraEmail: ".profile-info input[name='extra_email']",
                btnSaveMember: '#save-member',

                txtProfileName: '.profile-name',
                txtLocationState: '.location-state',
                txtExtraEmail: '.extra-email',
                txtPhone: '.phone',

                marker: '.marker',
                disabledMarker: 'disabled-marker',
                inpTagIds: '#tagIds',

                linkToFbAccBtn: '#linkToFbAcc',
                syncFbAccBtn: '#syncFbAcc',
                
                deleteMemberAcc: '#deleteMemberAcc',
                deleteMemberLoc: '/member/annihilate',
                
                passwordWasChanged: '#passwordChanged'
            },

            self.init = function()
            {
                self.bindEvents();

                // process address
                $(self.settings.inpAddress).keyup(function() {
                    self.__inputFillList(self.settings.inpAddress, self.settings.listAddress);
                });
            
                if ($(self.settings.passwordWasChanged).val() == 1) {
                	noty({text: 'Your password successfully changed', type: 'success'});
                }
            }

            self.bindEvents = function()
            {
            	$(self.settings.deleteMemberAcc).click(function() {
            		noty({text: 'Warning: this cannot be undone! Are you sure you want to delete your account? If you click “OK”, all your details, preferences, settings, and events will be deleted permanently. <br>Please click “Cancel” if you want to keep your account. (We would love for you to stay with us!)', 
            			  type: 'warning',
            			  buttons: [
            			            {
            			             addClass: 'btn btn-noty-ok', text: 'Ok', 
            			             onClick: function($noty) { 
            			            	 $noty.close();
            			            	 self.__confirmSuicide(); 
            			             }
            			            },
            			            {
            			             addClass: 'btn btn-noty-close', text: 'Cancel',
            			             onClick: function($noty) {
        			            		$noty.close(); 
            			             }
            			            }
            			  ]});
                });
            	
                $(self.settings.btnImg).click(function() {
                    self.__imitateUpload();
                });

                $(self.settings.saveFilterBtn).click(function(){
                    $(self.settings.filters).submit();
                });

                $(self.settings.activeCheckbox).click(function(){
                    self.__clearTags($(this).parent());
                });

                $(self.settings.settingsBoxCheckbox).click(function () {
                    $(this).parent().toggleClass('active-box');

                    var val = $(this).parent().find(self.settings.fieldId).attr('value');

                    var el = $(self.settings.filters + " input[value='" + val + "']");
                    el.prop('checked', function(i, val) {
                        return !val;
                    });
                });

                $(self.settings.txtProfileName).click(function(){
                    var val = $(this).text();

                    $(this).replaceWith(
                        $(".profile-info input[name='name']")
                    );
                });

                $(self.settings.txtLocationState).click(function(){
                    var val = $(this).text();

                    $(this).replaceWith(
                        $(".profile-info input[name='address']")
                    );
                });

                $(self.settings.txtExtraEmail).click(function(){
                    var val = $(this).text();

                    $(this).replaceWith(
                        $(".profile-info input[name='extra_email']")
                    );
                });

                $(self.settings.txtPhone).click(function(){
                    var val = $(this).text();

                    $(this).replaceWith(
                        $(".profile-info input[name='phone']")
                    );
                });

                $(self.settings.btnSaveMember).click(function(){
                    var isValid = true;

                    if (!self.checkFill(self.settings.inpMemberName)) {
                        noty({text: 'Please enter your name!', type: 'error'});

                        $(self.settings.inpMemberName).addClass('error-mes');
                        isValid = false;
                    }

                    if (!self.checkEmail(self.settings.inpMemberExtraEmail)) {
                        noty({text: 'Invalid email address!', type: 'error'});

                        $(self.settings.inpMemberExtraEmail).addClass('error-mes');
                        isValid = false;
                    }

                    if (!isValid) return false;
                });

                $(self.settings.btnImgUpload).on('change', function(e) {
                    self.__loadImage(e);
                });

                $(self.settings.marker).click(function(){
                    $(this).toggleClass(self.settings.disabledMarker);

                    var clickedId = $(this).attr('data-id');

                    var tagIds = $(self.settings.inpTagIds).val().split(',');

                    if (jQuery.inArray(clickedId, tagIds) == -1) {
                        tagIds.push(clickedId);
                    } else {
                        tagIds.splice( tagIds.indexOf(clickedId), 1);
                    }

                    $(self.settings.inpTagIds).val(tagIds.join());
                });

                $(self.settings.linkToFbAccBtn).click(function(){
                    FB.login(
                        function(response) {
                            if (response.authResponse) {
                                self.accessToken = response.authResponse.accessToken;
                                self.accessUid = response.authResponse.userID;

                                var userData = self.userData.join(',');
                                FB.api(
                                    { method: 'fql.query', query: 'SELECT ' + userData + ' FROM user WHERE uid = ' + self.accessUid },
                                    function(facebookData) {
                                        if (!facebookData) {
                                            alert('Can\'t get your info from FB acc');
                                            return false;
                                        }
                                        self.__linkFBAccount(facebookData[0], 'link');
                                    }
                                );
                            }
                        },
                        { scope: self.permissions }
                    );
                });

                $(self.settings.syncFbAccBtn).click(function(){
                    FB.login(
                        function(response) {
                            if (response.authResponse) {
                                self.accessToken = response.authResponse.accessToken;
                                self.accessUid = response.authResponse.userID;

                                var userData = self.userData.join(',');
                                FB.api(
                                    { method: 'fql.query', query: 'SELECT ' + userData + ' FROM user WHERE uid = ' + self.accessUid },
                                    function(facebookData) {
                                        if (!facebookData) {
                                            alert('Can\'t get your info from FB acc');
                                            return false;
                                        }
                                        self.__linkFBAccount(facebookData[0], 'sync');
                                    }
                                );
                            }
                        },
                        { scope: self.permissions }
                    );
                });
               
            }

            self.__linkFBAccount = function(data, action)
            {
                var url = '/member/link-fb';
                var successMsg = 'Your account was successfully linked with Facebook account. We are updating your account with facebook events, you will have your listings available shortly.';
                var errorMsg = 'Error during linking accounts';

                if (action == 'sync') {
                    url = '/member/sync-fb';
                    successMsg = 'Your account was successfully synced with Facebook account. We are updating your account with facebook events, you will have your listings available shortly.';
                    errorMsg = 'Error during syncing accounts';
                }

                params = { uid: self.accessUid,
		                    token: self.accessToken,
		                    address: data.current_address,
		                    location: data.current_location,
		                    email: data.email,
		                    logo: data.pic_big,
		                    first_name: data.first_name,
		                    last_name: data.last_name,
		                    username: data.username };
                $.when(self.__request('post', url, params)).then(function(response) {
                    data = $.parseJSON(response);
                    if (data.errors !== 'false') {
                        noty({text: successMsg, type: 'warning'});

                        self.__checkPermissions(action);
                        /*if (action == 'link') {
                            $(self.settings.linkToFbAccBtn).parent().prepend('<button id="syncFbAcc" class="btn btn-block ">Facebook sinc</button>');
                            $(self.settings.linkToFbAccBtn).remove();
                        }*/

                    } else {
                        noty({text: errorMsg, type: 'error'});
                    }
                });
            }
            
            self.__checkPermissions = function(action)
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
	                    	if (data.status != 'OK') {
	                    		$(self.settings.errorBox).html('Facebook return empty permissions :(');
	    		                $(self.settings.errorBox).show();
	                    	} 
	               		})
                	});
            	
                if (action == 'link') {
	                $(self.settings.linkToFbAccBtn).parent().prepend('<button id="syncFbAcc" class="btn btn-block ">Facebook sinc</button>');
	                $(self.settings.linkToFbAccBtn).remove();
            	}
			}

            self.checkFill = function(field)
            {
                if ($(field).val() == '') {
                    return false;
                } else {
                    return true;
                }
            }

            self.checkEmail = function(email)
            {
                var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                if ($(email).val() != '' && !filter.test($(email).val())) {
                    return false;
                } else {
                    return true;
                }
            }

            self.__imitateUpload = function()
            {
                $(self.settings.btnImgUpload).click();
            }

            // input -- input element (usualy type == text)
            // list -- destination element (found values will be rendered here)
            self.__inputFillList = function(input, list)
            {
                if (self.__checkInputFill(input, list))	{
                    var locs = utils.addressAutocomplete($(input)[0]);
                }
            }

            self.__checkInputFill = function(input, list)
            {
                if($(input).val() == '') {
                    $(list).parent('div').addClass('hidden');
                    return false;
                }

                return true;
            }

            self.__loadImage = function(content)
            {
                var reader = new FileReader();
                var file = content.target.files[0];

                reader.onload = (function(f) {
                    $(self.settings.btnImgUpload).attr('value', f.name);
                    return function(e) {
                        $(self.settings.boxImg).attr('src', e.target.result);
                    }
                })(file);

                reader.readAsDataURL(file);
            }

            self.__clearTags = function(element)
            {
                var tagsToClear = $(element).find('.marker:not(.disabled-marker)');

                var tagIds = null;
                tagsToClear.each(function( index, element) {
                    $(this).toggleClass('disabled-marker');

                    tagIds = $(self.settings.inpTagIds).val().split(',');
                    tagIds.splice( tagIds.indexOf($(this).attr('data-id')), 1);
                    $(self.settings.inpTagIds).val(tagIds.join());
                });
            }

            self.__request = function(method, url, params)
            {
                return $.ajax({ url: url,
                    data: params,
                    type: method});
            }
            
            self.__confirmSuicide = function()
            {
            	window.location.href = self.settings.deleteMemberLoc;
            }
        }

        return new frontMemberEditControl($, utils, noty);
    }
);

