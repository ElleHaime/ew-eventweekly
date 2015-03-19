define('frontMemberEditControl',
    ['jquery', 'utils', 'noty', 'fb', 'domReady'],
    function($, utils, noty, fb) {

        function frontMemberEditControl($, utils, noty, fb)
        {
            var self = this;

            self.accessToken = '',
            self.accessUid = '',
            self.checkboxAction = '',

            self.settings = {
                btnImg: '#file',
                btnImgUpload: '#logo',
                boxImg: '#img-box',

                inpAddress: '#address',
                listAddress: '#address-list',

                settingsBoxCheckbox: '.settings-box-one .checkbox',
                activeCheckbox: '.settings-box-one .checkbox',
                fieldId: '.fieldId',
                categoryNameCheckbox: '.catNamen',

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

                syncFbAccBtn: '#syncFbAcc',
                syncFbAddTaskUrl: '/member/task-fb',
                syncFbSyncUrl: '/member/sync-fb',
                syncFbLinkUrl: '/member/link-fb',
                syncSuccessMsg: 'Your account was successfully synced with Facebook account. We are updating your account with facebook events, you will have your listings available shortly.',
                syncErrorMsg: 'Error during syncing accounts',
                
                deleteMemberAcc: '#deleteMemberAcc',
                deleteMemberLoc: '/member/annihilate',
                
                passwordWasChanged: '#passwordChanged',
                syncSuccessMessage: 'We will find your events in a few minutes',
                syncErrorMessage: 'Oooops, something went wrong =/',
            },

            self.init = function()
            {
                self.__bindClicks();

                // process address
                $(self.settings.inpAddress).keyup(function() {
                    self.__inputFillList(self.settings.inpAddress, self.settings.listAddress);
                });
            
                if ($(self.settings.passwordWasChanged).val() == 1) {
                	noty({text: 'Your password successfully changed', type: 'success'});
                }
            }

            self.__bindClicks = function()
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

                /*
                **********************
                * =show/hide category
                **********************
                */
                $('.categories-accordion__arrow').click(function () {
                    $(this).closest('.categories-accordion__item').find('.event-site').toggle();
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




                /*
                **********************
                * =select filters
                **********************
                */

                $(self.settings.marker).click(function(){
                    var input = $(this).find("input");

                    $(this).toggleClass(self.settings.disabledMarker);
                    var clickedId = $(this).attr('data-id');

                    self.checkboxAction = 'uncheck_one';
                    self.__setCategoriesChecked();
                });


                $(self.settings.syncFbAccBtn).click(function(){
                	self.__syncFb();
                });


	            $('.checkbox_category').click(function() {
	            	var ifParentChecked = false;
	            	if ($(this).is(':checked') === true) {
	            		ifParentChecked = true;
	            	}
	
	                var divs = $(this).closest( ".categories-accordion__item" ).find('.marker:not(.disabled-marker)');
	                if (divs.length == 0) { 
	                    divs = $(this).closest( ".categories-accordion__item" ).find('div.event-category');
	                };
	                
	                divs.each(function() {
	                    var input = $(this).find("input");
	   
	                    $(this).toggleClass(self.settings.disabledMarker);
	                    var clickedId = $(this).attr('data-id');
	                   	input.prop("checked", ifParentChecked);
	                });
	            });


	            $('.check_all').click(function(){
	                var divs = $(this).closest( "#profile_right" ).find('.form-checkbox');
	                
	                divs.each(function( ) {
	                    var input = $(this).find("input");
	                    $(this).addClass(self.settings.disabledMarker);
	                    var clickedId = $(this).attr('data-id');
                        input.prop("checked", true);
	                });
	
	                //check checkboxes near category names
	                self.__setCategoriesChecked();
	            });


	            $('.uncheck_all').click(function(){
	                var divs = $(this).closest( "#profile_right" ).find('.form-checkbox');
	                
	                divs.each(function( ) {
	                    var input = $(this).find("input");
	                    $(this).addClass(self.settings.disabledMarker);
	                    var clickedId = $(this).attr('data-id');
                        input.prop("checked", false);
	                });
	
	                //check checkboxes near category names
	                self.checkboxAction = 'uncheck_all';
	                self.__setCategoriesChecked();
	            });


            }
            

            //category checkboxes
            self.__setCategoriesChecked = function(){
                $('#profile_right').find('.catNamen').each(function() { 
                    var isChecked = true;
                    if ($(this).closest('.categories-accordion__item').find('.userFilter-tag').length > 0) {
	                    $(this).closest('.categories-accordion__item').find('.userFilter-tag').each(function() {
	                    	
	                        if($(this).is(':checked') === false) {
	                        	isChecked = false;
	                        	return false;
	                        }
	                    });
	                    
	                    $(this).prev('input').prop('checked', isChecked);       
                    } else {
                    	if (self.checkboxAction != 'uncheck_all' && self.checkboxAction != 'uncheck_one') {
                    			isChecked = true;
                    			$(this).prev('input').prop('checked', isChecked);
                   		}
                   	}
                });
                self.checkboxAction = '';
            }


             self.__syncFb = function()
             {
            	 $.when(fb.__checkLoginStatus()).then(function(status) {
            		 // status: connected, session_expired, not_logged, unknown
		 
                     if (status === 'connected') {
                    	 $.when(self.__request('post', self.settings.syncFbAddTaskUrl)).then(function(response) {
                    		 self.__syncFbShowResult(response);
                    	 });
                     } else {
                    	 fb.accessType = 'sync';
                    	 $.when(fb.__login()).then(function(response) {
                    		 self.__syncFbShowResult(response);
                    	 });
                     }
            	 });
             }
             
             
             self.__syncFbShowResult = function(response)
             {
            	 if (response.status == 'OK') {
        			 noty({text: self.settings.syncSuccessMessage, type: 'success'});
        		 } else {
        			 noty({text: self.settings.syncErrorMessage, type: 'error'});
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

        return new frontMemberEditControl($, utils, noty, fb);
    }
);

