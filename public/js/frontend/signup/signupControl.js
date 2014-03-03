define('signupControl',
	['jquery', 'noty', 'utils', 'underscore'],
	function($, noty, utils) {
		function signupControl($, noty, utils)
		{
			var self = this;

			self.settings = {
				inpEmail: '#email',
				inpPassword: '#password',
				inpPasswordConfirm: '#confirm_password',

				btnSubmit: '#submit_signup',

				formSubmit: '#form_signup',

				urlCheckMail: '/auth/checkunique'
			},
			self.isValid = true,


			self.init = function()
			{
				self.__bindClicks();
			}

			self.__bindClicks = function()
			{
				$(self.settings.btnSubmit).click(function() {
                    if (!self.__checkEmail()) {
                        return false;
                    }

                    if (!self.__checkIdentical()) {
						return false;
					}

                    self.__checkEmailUnique();
				});
			}

            self.__checkEmail = function()
            {
                var email = $(self.settings.inpEmail).val();
                var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                if (email != '' && !filter.test(email)) {
                    noty({text: 'Invalid email address!', type: 'error'});
                    return false;
                } else {
                    return true;
                }
            }

			self.__checkIdentical = function()
			{
                if ($(self.settings.inpPassword).val() == '') {
                    var msg = 'Password cannot be empty';
                    noty({text: msg, type: 'error'});
                    self.__initError(msg);
                    return false;
                }

                if ($(self.settings.inpPassword).val() != $(self.settings.inpPasswordConfirm).val()) {
					var msg = 'Passwords don\'t match';
                    noty({text: msg, type: 'error'});
					self.__initError(msg);
					return false;
				} 
				return true;
			}

			self.__checkEmailUnique = function()
			{
				var sgEmail = $(self.settings.inpEmail).val();

				if (sgEmail.length <= 0) {
                    noty({text: 'Email incorrect', type: 'error'});
					self.__initError('Email incorrect');
					return false;
				}

				var params = {email: sgEmail };
				$.when(utils.request('post', self.settings.urlCheckMail, params)).then(function(data) {
					data = $.parseJSON(data);
					if (data.status != 'OK') {
						self.errors = data.message;
                        noty({text: data.message, type: 'error'});
						self.__initError(data.message);
					} else {
						self.__submit();
					}
				});
			}

			self.__submit = function()
			{
				$(self.settings.formSubmit).submit();
			}

			self.__initError = function(msg)
			{
				console.log(msg);
				return false;
			}
		};

		return new signupControl($, noty, utils);
	}
);
