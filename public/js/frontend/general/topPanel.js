define('frontTopPanel',
	['jquery', 'utils', 'gmapEvents', 'gmap', 'domReady'],
	function($, utils, gmapEvents) {

		function frontTopPanel($, utils, gmapEvents) {
			var self = this;

			self.settings = {
		        searchCityBtn: '.locationCity',
		        advancedSearchBtn: '.advancedSearchBtn',
		        searchCityBlock: '.searchCityBlock',
		        searchCityInput: '#topSearchCity',
		        advancedSearchBlock: '.advancedSearchBlock',
		        sendCoordsUrl: '',

				userControlBox: '.user-box',
				btnUserControl: {
					elem: '#user-down-caret',
					container: '#userControlBox'
				},
				userControlList: {
					elem: '#user-down',
					container: '#userControlBox'
				},

				btnHz: '#back-to'
		    },
		    self.__city = null,
		    
		    self.init = function(options)
		    {
		    	// extends options
		        self.settings = $.extend(self.settings, options);
		        $(self.settings.searchCityBtn).attr('data-state', 'close');

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

		    	htmlBody.on('click', self.settings.searchCityBtn, function(e){
		            e.preventDefault();

		            self.__changeVisibility('city');
		            $(self.settings.searchCityBlock).find('input').focus();

		            var list = utils.addressAutocomplete($(self.settings.searchCityInput)[0]);

		            google.maps.event.addListener(list, 'place_changed', function() {
		                var lat = list.getPlace().geometry.location.lat();
		                var lng = list.getPlace().geometry.location.lng();

		                self.__city = list.getPlace().vicinity;

                        if (_.isUndefined(self.__city)) {
                            self.__city = list.getPlace().name;
                        }

                        if (_.isUndefined(self.__city)) {
                            self.__city = list.getPlace().formatted_address;
                        }
		                //$(self.settings.searchCityBtn).find('span').text(self.__city);

		                self.__sendCoords(lat, lng);
		            });
		        });

		        htmlBody.on('click', self.settings.advancedSearchBtn, function(e) {
		            e.preventDefault();

		            self.__changeVisibility('advanced');
		        });

		    }
		    
		    self.__changeVisibility = function(type)
		    {
		    	$(self.settings.searchCityBtn).closest('div').removeClass('active-box');
		        $(self.settings.advancedSearchBtn).closest('div').removeClass('active-box');

		        if (type == 'city' && !$(self.settings.searchCityBlock).is(":visible")) {
		            // hide advanced block
		            $(self.settings.advancedSearchBlock).hide();

		            // show city block
		            $(self.settings.searchCityBtn).closest('div').addClass('active-box');
		            $(self.settings.searchCityBlock).show();
		        } else if (type == 'city' && $(self.settings.searchCityBlock).is(":visible")) {
		            $(self.settings.searchCityBtn).closest('div').removeClass('active-box');
		            $(self.settings.searchCityBlock).hide();
		        }

		        if (type == 'advanced' && !$(self.settings.advancedSearchBlock).is(":visible")) {
		            // hide search block
		            $(self.settings.searchCityBlock).hide();

		            // show advanced block
		            $(self.settings.advancedSearchBtn).closest('div').addClass('active-box');
		            $(self.settings.advancedSearchBlock).show();
		        } else if (type == 'advanced' && $(self.settings.advancedSearchBlock).is(":visible")) {
		            $(self.settings.advancedSearchBtn).closest('div').removeClass('active-box');
		            $(self.settings.advancedSearchBlock).hide();
		        }

		    }
		    
		    self.__sendCoords = function(lat, lng)
		    {
		    	self.__changeVisibility('city');
                gmapEvents.resetLocation = true;

                $.cookie('lastLat', lat, {expires: 1, path: '/'});
                $.cookie('lastLng', lng, {expires: 1, path: '/'});
                $.cookie('lastCity', self.__city, {expires: 1, path: '/'});
                window.location.href = '/map';
		    	//gmapEvents.getEvents(lat, lng, self.__city);
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
                    popup.onbeforeunload = function(){
                        popup.close();

                        var ppVal = $('#popupRedirect').val();
                        if (ppVal == 'reload') {
                            location.reload();
                        }
                    }
                }

                $('body').on('click', '.fb-login-popup', showPopup);
            }
		}; 
		
		return new frontTopPanel($, utils, gmapEvents); 
	}
); 