define('signupControl',
	['jquery', 'noti', 'utils', 'underscore'],
	function($, noti, utils) {
		function signupControl($, noti, utils) 
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
                    if (!self.__checkIdentical()) {
						return false;
					}

                    self.__checkEmailUnique();
				});
			}

			self.__checkIdentical = function()
			{
                if ($(self.settings.inpPassword).val() == '') {
                    var msg = 'Password cannot be empty';
                    noti.createNotification(msg, 'error');
                    self.__initError(msg);
                    return false;
                }

                if ($(self.settings.inpPassword).val() != $(self.settings.inpPasswordConfirm).val()) {
					var msg = 'Passwords don\'t match';
                    noti.createNotification(msg, 'error');
					self.__initError(msg);
					return false;
				} 
				return true;
			}

			self.__checkEmailUnique = function()
			{
				var sgEmail = $(self.settings.inpEmail).val();

				if (sgEmail.length <= 0) {
                    noti.createNotification('Email incorrect', 'error');
					self.__initError('Email incorrect');
					return false;
				}

				var params = {email: sgEmail };
				$.when(utils.request('post', self.settings.urlCheckMail, params)).then(function(data) {
					data = $.parseJSON(data);
					if (data.status != 'OK') {
						self.errors = data.message;
                        noti.createNotification(data.message, 'error');
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

		return new signupControl($, noti, utils);
	}
);
