define('frontTopPanel',
	['jquery', 'utils', 'domReady'],
	function($, utils) {

		function frontTopPanel($, utils) {
			var self = this;
		
			self.settings = {
				userControlBox: '.user-box',
				btnUserControl: {
					elem: '#user-down-caret',
					container: '#userControlBox'
				},
				userControlList: {
					elem: '#user-down',
					container: '#userControlBox'
				},

				btnHz: '#back-to',

                emailLoginBtn: '#email-login',
                fbLoginBtn: '.top-line__link'
		    },
		    self.__city = null,
	    
		    self.init = function(options)
		    {
		    	// extends options
		        self.settings = $.extend(self.settings, options);
		        // initialize clicks
		        self.__bindClicks();
                // initialize login popup
                self.__popupLogin();
		    }
		    
		    self.__bindClicks = function()
		    {
		    	htmlBody = $('body');
		    	
		    	htmlBody.on('click', self.settings.btnUserControl.elem, function(e) {
		    		self.__userControl();
		    	});
		    }
		    

		    self.__userControl = function()
		    {	
		    	var controlBox = $(self.settings.userControlBox);
		    	var controlList = $(self.settings.userControlList.elem);

		    	if (controlBox.hasClass('active-box')) {
                    controlBox.removeClass('active-box');
		    	} else {
                    controlBox.addClass('active-box');
		    	}
                controlList.toggle();
		    }

            self.__popupLogin = function()
            {
                var showPopup = function(){
                    var width = 600;
                    var height = 550;
                    var left = (screen.width/2) - (width/2);
                    var top = (screen.height/2) - (height/2);

                    var popup = window.open(
                        "/member/login", "_blank",
                        "toolbar=yes, scrollbars=yes, resizable=yes, top=" + top + ", left=" + left + ", width=" + width + ", height=" + height
                    );
                }

                $('body').on('click', self.settings.fbLoginBtn, showPopup);
            }
		}; 
		
		return new frontTopPanel($, utils); 
	}
); 