define('frontEventEditControl',
	['jquery', 'utils', 'domReady', 'datetimepicker'],
	function($, utils, datetimepicker) {

		function frontEventEditControl($, utils, datetimepicker) 
		{
			var self = this;

			self.settings = {
				inpDateStart: '#date-picker-start',
				inpDateEnd: '#date-picker-end',
				textDateStart: '#start_date',
				textTimeStart: '#start_time',
				inpTimeStart: '#time-picker-start',
				inpTimeEnd: '#time-picker-end',
				textDateEnd: '#end_date',
				textTimeEnd: '#end_time',

				dateLeft: '#date-start',
				timeLeft: '#time-start',
				daysLeftCount: '#days-count',
				stringLeft: '#time-string',

				inpLocation: '#location',
				listLocation: '#locations-list',
				coordsLocationLat: '#location_latitude',
				coordsLocationLng: '#location_longitude',

				inpAddress: '#address',
				listAddress: '#addresses-list',
				coordsAddress: '#address-coords',

				inpVenue: '#venue',
				listVenue: '#venues-list',
				coordsVenueLat: '#venue_latitude',
				coordsVenueLng: '#venue_longitude',

				inpCategory: '#event_category',
				inpCategoryReal: '#category',
				listCategory: '#event-category-selected',

				btnSite: $('#add-web-site'),
				inpSite: $('#sites'),
				inpSiteReal: $('#event_site'),
				listSite: $('#event-site-selected'),

				removeSign: '.icon-remove-sign',

				boxImg: '#img-box',
				btnImg: '#add-img-btn',
				btnImgUpload: '#logo',

				inpCampaign: '#campaign_id'
			},


			self.init = function()
			{
				utils.addEmptyOptionFirst($(self.settings.inpCampaign), 'No campaign');

				self.__presetDate($(self.settings.inpDateStart), $(self.settings.textDateStart), 'start');
				self.__presetDate($(self.settings.inpDateEnd), $(self.settings.textDateEnd), 'end');
				self.__presetTime($(self.settings.inpTimeStart), $(self.settings.textTimeStart));
				self.__presetTime($(self.settings.inpTimeEnd), $(self.settings.textTimeEnd));

				self.bindEvents();
			}

			self.bindEvents = function()
			{
				$(self.settings.btnImgUpload).on('change', function(e) {
					self.__loadImage(e);
				});

				// process date end time
				$(self.settings.inpDateStart).on('changeDate', function(e) {
					self.__drawDateLeft(e.localDate, 'changeLeft');
				});

				$(self.settings.inpDateEnd).on('changeDate', function(e) {
					self.__drawDateLeft(e.localDate);
				});

				$(self.settings.inpTimeStart).on('changeDate', function(e) {
					self.__drawTimeLeft(e.localDate);
				});

				// process locations
				$(self.settings.inpLocation).keyup(function() {
					self.__inputFillList(self.settings.inpLocation, 
										 self.settings.listLocation, 
										 self.settings.coordsLocationLat,
										 self.settings.coordsLocationLng);
				});

				// process address
				$(self.settings.inpAddress).keyup(function() {
					self.__inputFillList(self.settings.inpAddress, self.settings.listAddress, self.settings.coordsAddress);
				});

				// process venues
				$(self.settings.inpVenue).keyup(function() {
					self.__inputFillList(self.settings.inpVenue, self.settings.listVenue, self.settings.coordsVenue);
				});

				// process categories
				$(self.settings.inpCategory).change(function() {
					self.__addCategory();
				});

				$(self.settings.listCategory).on('click', self.settings.removeSign, function(e) {
					e.preventDefault();
					self.__removeCategory($(this));
				});

				self.settings.btnSite.click(function() {
					self.__addSite();
				});
				
				self.settings.listSite.on('click', self.settings.removeSign, function(e) {
					e.preventDefault();
					self.__removeSite($(this));
				});
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

			self.__addCategory = function()
			{
				var list = $(self.settings.listCategory);

				var item = '<div><label>' + $(self.settings.inpCategory + ' :selected').text() + '</label>' +
						'<a href="#" class="icon-remove-sign" catid="' + $(self.settings.inpCategory + ' :selected').val() + '"></div>';
		        $(self.settings.inpCategoryReal).val($(self.settings.inpCategoryReal).val() + $(self.settings.inpCategory + ' :selected').val() + ',');

		        $(self.settings.inpCategory + ' :selected').remove();

		       	list.append(item);
		        list.show();

		        if ($('select' + self.settings.inpCategory + ' option').length == 0) {
		            $(self.settings.inpCategory).hide();
		        }
			}

			self.__removeCategory = function(elem)
			{
				var item = '<option value="' + elem.attr('catid') + '">' + elem.prev('label').html() + '</option>';
		 		$(self.settings.inpCategory).append(item);
		 		$(self.settings.inpCategoryReal).val($(self.settings.inpCategoryReal).val().replace(elem.attr('catid') + ',', ''));
		 		
		        elem.parent('div').remove();

		        if ($(self.settings.listCategory).children('div').length == 0) {
		            $(self.settings.listCategory).hide();
		        }
			}

			self.__addSite = function()
			{
				var url = self.settings.inpSite.val();
				
				if (url.length != 0) {
			        if (url.indexOf('http', 0) < 0) {
			            url = 'http://' + url;
			        }
			        var link = '<div><a target="_blank" href="' + url + '">' + url + '</a>' + 
			        			'<a href="#" class="icon-remove-sign"></a></div>';
			        
			        self.settings.listSite.append(link);
			        self.settings.listSite.show();
			        self.settings.inpSite.val('');
			        self.settings.inpSiteReal.val(self.settings.inpSiteReal.val() + url + ',');
			    }
			}

			self.__removeSite = function(elem)
			{
				var url = elem.parent('div').find('a').text();
				elem.parent('div').remove();
		        elem.remove();
				self.settings.inpSiteReal.val(self.settings.inpSiteReal.val().replace(url + ',', ''));

		        if (self.settings.listSite.children('div').length == 0) {
		        	self.settings.listSite.hide();
		        }
			}

			self.__drawDateLeft = function(val, changeLeft)
			{
				var elem = $(self.settings.textDateStart);
				daysBetween = utils.daysCount(val, new Date());
				if (daysBetween < 0) {
					elem.val('Incorrect date');
					$(self.settings.stringLeft).hide();
					return false;
				}

				if (changeLeft) {
					$(self.settings.daysLeftCount).html(utils.daysDifference(daysBetween));
					$(self.settings.dateLeft).html(utils.dateFormat('%d %b %Y', val));

					$(self.settings.stringLeft).show();
				}
			}

			self.__drawTimeLeft = function(val)
			{
				var elem = $(self.settings.inpTimeStart);
				if (val != '00:00:00' && val != '') {
					$(self.settings.timeLeft).html(utils.dateFormat('%H:%M', val));
				}
			}

			self.__presetDate = function(elem, txt, type)
			{
				elem.datetimepicker({ pickTime: false, 
									  startDate: new Date() });
				if (txt.val() == '') {
					txt.attr('placeholder', type + 's ' + utils.dateFormat('%d/%m/%Y'))
				}
			}

			self.__presetTime = function(elem, txt)
			{
				elem.datetimepicker({ pickDate: false });
				if (txt.val() == '') {
					txt.attr('placeholder', 'at ' + '00:00:00');
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
		           	
		           	if (input == self.settings.inpVenue) {
			           	google.maps.event.addListener(locs, 'place_changed', function() {
			                var lat = locs.getPlace().geometry.location.ob;
			                var lng = locs.getPlace().geometry.location.pb;
			                
			                $(self.settings.coordsVenue).val(lat + ';' + lng);
			            });
		           	}
				}
			}
		};

		return new frontEventEditControl($, utils, datetimepicker);
	}
);
