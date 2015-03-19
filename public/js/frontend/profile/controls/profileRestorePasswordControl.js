define('profileRestorePasswordControl',
    ['jquery', 'utils', 'noty', 'domReady'],
    function($, utils, noty) {

        function profileRestorePasswordControl($, utils, noty)
        {
            var self = this;

            self.settings = {
                formRestorePassword: '#restore-password-form',
                formResetPassword: '#form_signup',

                inpEmail: '#email',
                inpPassword: '#password',
                inpConfirmPassword: '#conf_password'
            },

            self.init = function()
            {
                self.bindEvents();
            }

            self.bindEvents = function()
            {
                $(self.settings.formRestorePassword).submit(function(){
                    if (!self.__checkRestoreForm(true)) return false;

                    $(this).submit();
                });

                $(self.settings.formResetPassword).submit(function(){
                    if (!self.__checkResetForm(true)) return false;

                    $(this).submit();
                });
            }

            self.__checkRestoreForm = function(showNoti)
            {
                var isValid = true;

                var requiredFields = [
                    { element : self.settings.inpEmail, text : 'your email address' }
                ];

                var text = 'Please enter: ';
                requiredFields.forEach(function(field) {
                    if ($(field.element).val() == '') {
                        text += field.text + ', ';
                        isValid = false;
                    }
                });

                if (isValid && !/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($(self.settings.inpEmail).val())) {
                    text = 'Invalid email address!';
                    isValid = false;
                }

                if (!isValid && showNoti) {
                    text = text.substring(0, text.length - 2);
                    noty({text: text, type: 'error'});
                }

                return isValid;
            }

            self.__checkResetForm = function(showNoti)
            {
                var isValid = true;

                var requiredFields = [
                    { element : self.settings.inpPassword, text : 'new password' },
                    { element : self.settings.inpConfirmPassword, text : 'confirm password' }
                ];

                var text = 'Please enter: ';
                requiredFields.forEach(function(field) {
                    if ($(field.element).val() == '') {
                        text += field.text + ', ';
                        isValid = false;
                    }
                });

                if (isValid && ($(self.settings.inpPassword).val().length < 6 || $(self.settings.inpConfirmPassword).val().length < 6)) {
                    text = 'New password is less than the minimum 6 characters  ';
                    isValid = false;
                }

                if (isValid && ($(self.settings.inpPassword).val() != $(self.settings.inpConfirmPassword).val())) {
                    text = 'Password doesn\'t match confirmation   ';
                    isValid = false;
                }

                if (!isValid && showNoti) {
                    text = text.substring(0, text.length - 2);
                    noty({text: text, type: 'error'});
                }

                return isValid;
            }
        }

        return new profileRestorePasswordControl($, utils, noty);
    }
);

