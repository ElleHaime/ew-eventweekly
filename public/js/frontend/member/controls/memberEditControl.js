define('frontMemberEditControl',
    ['jquery', 'utils', 'noty', 'fb', 'domReady'],
    function($, utils, noty, fb) {

        function frontMemberEditControl($, utils, noty, fb)
        {
            var self = this;

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
                buttonCheckbox: '.checkbox',

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
                
                passwordWasChanged: '#passwordChanged'
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
                * =deprecated in new design
                **********************
                */
                // $(self.settings.activeCheckbox).click(function(){
                //     self.__clearTags($(this).parent());
                // });

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

                    //alert('piu');

                    $(this).toggleClass(self.settings.disabledMarker);
                    
                    var clickedId = $(this).attr('data-id');

                    var tagIds = $(self.settings.inpTagIds).val().split(',');
                    //console.log(tagIds);
                    
                    


                    if (jQuery.inArray(clickedId, tagIds) == -1) {
                        tagIds.push(clickedId);
                        input.prop("checked",false);
                    } else {
                        tagIds.splice( tagIds.indexOf(clickedId), 1);
                        input.prop("checked",true);
                    }

                    $(self.settings.inpTagIds).val(tagIds.join());
                    console.log( $(self.settings.inpTagIds).val() );
                });
                





                $(self.settings.syncFbAccBtn).click(function(){
                	self.__syncFb();
                });
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
        			 noty({text: self.syncSuccessMessage, type: 'warning'});
        		 } else {
        			 noty({text: self.syncErrorMessage, type: 'error'});
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
                alert('ololo');
                var tagsToClear = $(element).find('.marker:not(.disabled-marker)');

                var tagIds = null;
                tagsToClear.each(function( index, element) {
                    $(this).toggleClass('disabled-marker');

                    tagIds = $(self.settings.inpTagIds).val().split(',');
                    tagIds.splice( tagIds.indexOf($(this).attr('data-id')), 1);
                    $(self.settings.inpTagIds).val(tagIds.join());
                });
            }


            /*
            **********************
            * =select all by clicking on category name, in new design
            **********************
            */
            $(self.settings.buttonCheckbox).click(function(){
                // var divs = $(this).parent().find('div.event-category');
                var divs = $(this).parent().find('.marker:not(.disabled-marker)');
                if (divs.length == 0) { 
                    divs = $(this).parent().find('div.event-category');
                };
                divs.each(function( ) {
                    var input = $(this).find("input");
                
                    $(this).toggleClass(self.settings.disabledMarker);
                    
                    var clickedId = $(this).attr('data-id');

                    var tagIds = $(self.settings.inpTagIds).val().split(',');

                    if (jQuery.inArray(clickedId, tagIds) == -1) {
                        tagIds.push(clickedId);
                        input.prop("checked",false);
                    } else {
                        tagIds.splice( tagIds.indexOf(clickedId), 1);
                        input.prop("checked",true);
                    }

                    $(self.settings.inpTagIds).val(tagIds.join());
                });
               
            });



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

