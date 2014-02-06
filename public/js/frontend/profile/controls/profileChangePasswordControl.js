define('profileChangePasswordControl',
    ['jquery', 'utils', 'noti', 'domReady'],
    function($, utils, noti) {

        function profileChangePasswordControl($, utils, noti)
        {
            var self = this;

            self.settings = {
                formChangePassword: '#change-password-form',

                inpOldPassword: '#old_password',
                inpNewPassword: '#password',
                inpConfPassword: '#conf_password'
            },

            self.init = function()
            {
                self.bindEvents();
            }

            self.bindEvents = function()
            {
                $(self.settings.formChangePassword).submit(function(){
                    if (!self.__checkForm(true)) return false;

                    $(this).submit();
                });
            }

            self.__checkForm = function(showNoti)
            {
                var isValid = true;

                var requiredFields = [
                    { element : self.settings.inpOldPassword, text : 'old password' },
                    { element : self.settings.inpNewPassword, text : 'new password' },
                    { element : self.settings.inpConfPassword, text : 'confirm password' }
                ];

                var text = 'Please enter: ';
                requiredFields.forEach(function(field) {
                    if ($(field.element).val() == '') {
                        text += field.text + ', ';
                        isValid = false;
                    }
                });

                if (isValid && ($(self.settings.inpNewPassword).val().length < 6 || $(self.settings.inpConfPassword).val().length < 6)) {
                    text = 'New password is less than the minimum 6 characters  ';
                    isValid = false;
                }

                if (isValid && ($(self.settings.inpNewPassword).val() != $(self.settings.inpConfPassword).val())) {
                    text = 'Password doesn\'t match confirmation';
                    isValid = false;
                }

                if (!isValid && showNoti) {
                    text = text.substring(0, text.length - 2);
                    noti.createNotification(text, 'error');
                }

                return isValid;
            }
        }

        return new profileChangePasswordControl($, utils, noti);
    }
);

