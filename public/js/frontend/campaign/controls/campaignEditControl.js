define('frontCampaignEditControl',
	['jquery', 'utils', 'domReady', 'datetimepicker'],
	function($, utils, datetimepicker) {

		function frontCampaignEditControl($, utils, datetimepicker) 
		{
			var self = this;

			self.settings = {
				inpLocation: '#location',
				listLocation: '#locations-list',
				coordsLocationLat: '#location_latitude',
				coordsLocationLng: '#location_longitude',

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
		           	var locs = utils.addressAutocomplete($(input)[0]);

		           	if (input == self.settings.inpLocation) {
			           	google.maps.event.addListener(locs, 'place_changed', function() {
			                var lat = locs.getPlace().geometry.location.ob;
			                var lng = locs.getPlace().geometry.location.pb;
			                
			                $(self.settings.coordsLocationLat).val(lat);
			                $(self.settings.coordsLocationLng).val(lng);
			            });
		           	}
		           	
		           	if (input == self.settings.inpAddress) {
			           	google.maps.event.addListener(locs, 'place_changed', function() {
			                var lat = locs.getPlace().geometry.location.ob;
			                var lng = locs.getPlace().geometry.location.pb;
			                
			                $(self.settings.coordsAddress).val(lat + ';' + lng);
			            });
		           	}
				}
			}
		};

		return new frontCampaignEditControl($, utils, datetimepicker);
	}
);
