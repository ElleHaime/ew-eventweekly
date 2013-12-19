define('frontMemberEditControl',
    ['jquery', 'utils', 'noti', 'domReady'],
    function($, utils, noti) {

        function frontMemberEditControl($, utils, noti)
        {
            var self = this;

            self.settings = {
                btnImg: '#file',
                btnImgUpload: '#logo',

                inpAddress: '#address',
                listAddress: '#address-list',

                settingsBoxCheckbox: '.settings-box-one .checkbox',
                fieldId: '.fieldId',

                filters: '#filters',
                saveFilterBtn: '#saveFilter',

                inpMemberName: ".profile-info input[name='name']",
                inpMemberExtraEmail: ".profile-info input[name='extra_email']",
                btnSaveMember: '#save-member',

                txtProfileName: '.profile-name',
                txtLocationState: '.location-state',
                txtExtraEmail: '.extra-email',
                txtPhone: '.phone'
            },

            self.init = function()
            {
                self.bindEvents();

                // process address
                $(self.settings.inpAddress).keyup(function() {
                    self.__inputFillList(self.settings.inpAddress, self.settings.listAddress);
                });
            }

            self.bindEvents = function()
            {
                $(self.settings.btnImg).click(function() {
                    self.__imitateUpload();
                });

                $(self.settings.saveFilterBtn).click(function(){
                    $(self.settings.filters).submit();
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
                        noti.createNotification('Please enter your name!', 'error');

                        $(self.settings.inpMemberName).addClass('error-mes');
                        isValid = false;
                    }

                    if (!self.checkEmail(self.settings.inpMemberExtraEmail)) {
                        noti.createNotification('Invalid email address!', 'error');

                        $(self.settings.inpMemberExtraEmail).addClass('error-mes');
                        isValid = false;
                    }

                    if (!isValid) return false;
                });
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
        }

        return new frontMemberEditControl($, utils, noti);
    }
);

