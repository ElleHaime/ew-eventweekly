define('frontCampaignEditControl',
	['jquery', 'utils', 'datetimepicker', 'noti', 'domReady'],
	function($, utils, datetimepicker, noti) {

		function frontCampaignEditControl($, utils, datetimepicker, noti)
		{
			var self = this;

			self.settings = {
                form: 'form',

                inpName: '#name',

				inpLocation: '#location',
				listLocation: '#locations-list',
				coordsLocationLat: '#location_latitude',
				coordsLocationLng: '#location_longitude',
				coordsLocationId: '#location_id',

				inpAddress: '#address',
				listAddress: '#addresses-list',
				coordsAddress: '#address-coords',

				boxImg: '#img-box',
				btnImg: '#add-img-btn',
				btnImgUpload: '#add-img-upload',
				inpLogo: '#logo',	

				btnCancel: '#btn-cancel'			
			}

			self.init = function()
			{
				self.bindEvents();
			}

			self.bindEvents = function()
			{
				$(self.settings.btnImg).click(function() {
					self.__imitateUpload();
				});

				$(self.settings.btnImgUpload).on('change', function(e) {
					self.__loadImage(e);
				});

				$(self.settings.inpLocation).keyup(function() {
					$(self.settings.coordsLocationId).val('');
					self.__inputFillList(self.settings.inpLocation, 
										 self.settings.listLocation, 
										 self.settings.coordsLocationLat,
										 self.settings.coordsLocationLng);
				});

				$(self.settings.inpAddress).keyup(function() {
					self.__inputFillList(self.settings.inpAddress, self.settings.listAddress, self.settings.coordsAddress);
				});


				$(self.settings.btnCancel).click(function() {
					window.location.href = "/campaign/list";
				});

                $(self.settings.form).bind("keyup keypress", function(e) {
                    var code = e.keyCode || e.which;
                    if (code  == 13) {
                        e.preventDefault();
                        return false;
                    }
                });

                $(self.settings.form).submit(function(){
                    if (!self.__checkRequiredFields()) return false;
                });
			}

			self.__loadImage = function(content)
			{
				var reader = new FileReader();
				var file = content.target.files[0];

				reader.onload = (function(f) {
					if (f) {
						$(self.settings.inpLogo).attr('value', f.name);
						return function(e) {
							$(self.settings.boxImg).attr('src', e.target.result);
						}
					}
				})(file);

				reader.readAsDataURL(file);
			}

			self.__imitateUpload = function()
			{
				$(self.settings.btnImgUpload).click();
			}


			self.__checkInputFill = function(input, list)
			{
				if($(input).val() == '') {
					$(list).parent('div').addClass('hidden');
					return false;
				}
				
				return true;
			}

			// input -- input element (usualy type == text)
			// list -- destination element (found values will be rendered here)
			self.__inputFillList = function(input, list, coordsLat, coordsLng)
			{
				if (self.__checkInputFill(input, list))	{
		           	if (input == self.settings.inpLocation) {
                        var locs = utils.addressAutocomplete($(input)[0]);
			           	google.maps.event.addListener(locs, 'place_changed', function() {
			                var lat = locs.getPlace().geometry.location.ob;
			                var lng = locs.getPlace().geometry.location.pb;
			                
			                $(self.settings.coordsLocationLat).val(lat);
			                $(self.settings.coordsLocationLng).val(lng);
			            });
		           	}
		           	
		           	if (input == self.settings.inpAddress) {
                        var addr = utils.addressAutocomplete($(input)[0], 'geocode');
			           	google.maps.event.addListener(addr, 'place_changed', function() {
			                var lat = addr.getPlace().geometry.location.ob;
			                var lng = addr.getPlace().geometry.location.pb;
			                
			                $(self.settings.coordsAddress).val(lat + ';' + lng);
			            });
		           	}
				}
			}

            self.__checkRequiredFields = function()
            {
                var isValid = true;

                var fields = [
                    { element : self.settings.inpName, text : 'campaign title' }
                ];

                var text = 'Please enter: ';
                fields.forEach(function(field) {
                    if ($(field.element).val() == '') {
                        text += field.text + ', ';
                        isValid = false;
                    }
                });

                if (!isValid) {
                    text = text.substring(0, text.length - 2);
                    noti.createNotification(text, 'error');
                }

                return isValid;
            }
		};

		return new frontCampaignEditControl($, utils, datetimepicker, noti);
	}
);
