define('frontEventEditControl',
	['jquery', 'utils', 'datetimepicker', 'noti', 'domReady'],
	function($, utils, datetimepicker, noti) {

		function frontEventEditControl($, utils, datetimepicker, noti)
		{
			var self = this;

			self.settings = {
                form: 'form',

                inpName: '#name',
                inpCampaignId: '#hiddenCampaignId',

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
				coordsLocationId: '#location_id',

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

				removeSign: '.icon-remove',

                linkAddImg: '.add-img a',
				boxImg: '.img-box',
                btnImg: '.add-img-btn',

                btnImgLogoUpload: '#add-img-logo-upload',
				inpLogo: '#logo',

                btnImgPosterUpload: '#add-img-poster-upload',
                inpPoster: '#poster',

                btnImgFlyerUpload: '#add-img-flyer-upload',
                inpFlyer: '#flyer',

				inpCampaign: '#campaign_id',
				inpCampaignExists: '#is_campaign',

				btnCancel: '#btn-cancel',
                btnSubmit: '#btn-submit',

                defaultCategories: '#defaultCategories',

                memberExtUid: '#member_ext_uid',
                eventFbStatus: '#event_fb_status',
                accSynced: '#acc_synced',
                externalLogged: '#external_logged',
                btnPreview: '#btn-preview',
                deleteImage: '.delete-logo'
			},


			self.init = function()
			{
                utils.addEmptyOptionFirst($(self.settings.inpCategory), 'Choose categories');

                if ($(self.settings.inpCampaignId).val() == '' || $(self.settings.inpCampaignId).val() == 0) {
                    utils.addEmptyOptionFirst($(self.settings.inpCampaign), 'Choose event campaign');
                } else {
                    utils.addNotSelectedEmptyOptionFirst($(self.settings.inpCampaign), 'Choose promoter');
                }

				var camp = $(self.settings.inpCampaignExists).val();
				if (camp != 0) {
					$(self.settings.inpCampaign + ' option[value=' + camp + ']').attr('selected', true);
				}

				self.__presetDate($(self.settings.inpDateStart), $(self.settings.textDateStart), 'start');
				self.__presetDate($(self.settings.inpDateEnd), $(self.settings.textDateEnd), 'end');
				self.__presetTime($(self.settings.inpTimeStart), $(self.settings.textTimeStart));
				self.__presetTime($(self.settings.inpTimeEnd), $(self.settings.textTimeEnd));

                self.__initCategoryList();

                self.__initFieldsForDateTimePicker();

				self.bindEvents();

                self.__setupDateTimePicker();

                self.__initFacebookPublish();
			}

			self.bindEvents = function()
			{
				$(self.settings.btnImg).click(function() {
					self.__imitateUpload($(this).parent().find('input[type="file"]'));
				});

				$(self.settings.btnImgLogoUpload).on('change', function(e) {
					self.__loadImage(e, self.settings.inpLogo);
				});

                $(self.settings.btnImgPosterUpload).on('change', function(e) {
                    self.__loadImage(e, self.settings.inpPoster);
                });

                $(self.settings.btnImgFlyerUpload).on('change', function(e) {
                    self.__loadImage(e, self.settings.inpFlyer);
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
					$(self.settings.coordsLocationId).val('');
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
					self.__inputFillList(self.settings.inpVenue,
										 self.settings.listVenue,
										 self.settings.coordsVenueLat,
										 self.settings.coordsVenueLng);
				});

				$(self.settings.inpCategory).change(function() {
                    self.__removeCategoryConflict();

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

				$(self.settings.btnCancel).click(function() {
					window.location.href = "/event/list";
				});

                $(self.settings.form).bind("keyup keypress", function(e) {
                    var code = e.keyCode || e.which;
                    if (code  == 13) {
                        e.preventDefault();
                        return false;
                    }
                });

                $(self.settings.form).submit(function(){
                    if ($(self.settings.inpCategoryReal).val().trim() == '') {
                        $(self.settings.inpCategoryReal).val($(self.settings.defaultCategories).text());
                    }

                    if (!self.__checkRequiredFields()) return false;
                    if (!self.__checkDatesContradictions()) return false;

                    $(self.settings.btnSubmit).prop('disabled', true);
                    $(self.settings.btnSubmit).text('Saving...');
                });

                $(self.settings.form).on('click', self.settings.btnPreview, function(e) {
                    e.preventDefault();
                    self.__eventPreview();
                });

                $(self.settings.linkAddImg).click(function(){
                    return false;
                });

                $(self.settings.deleteImage).click(function(){
                    var $image = $(this).prev().find('img');

                    if ($image.hasClass('img-logo')) {
                        $.post('/event/delete-logo', { id: $image.attr('data-id') }, function(data){});
                        $image.removeClass('img-logo');
                    } else if ($image.attr('data-id') != undefined) {
                        $.post('/event/delete-image', { id: $image.attr('data-id') }, function(data){});
                    }

                    $image.removeAttr('data-id');
                    $image.attr('src', '/img/demo/q1.jpg');
                    $image.parents().eq(2).find('input[type="hidden"]').val('');
                });
			}

            self.__eventPreview = function() {
                if ($(self.settings.inpCategoryReal).val().trim() == '') {
                    $(self.settings.inpCategoryReal).val($(self.settings.defaultCategories).text());
                }

                if (!self.__checkRequiredFields()) return false;
                if (!self.__checkDatesContradictions()) return false;

                $(self.settings.form).attr('target', 'eventPreview_iframe').attr('action', '/event/preview').submit();
                $(self.settings.form).removeAttr('target').removeAttr('action');
                $(self.settings.btnSubmit).prop('disabled', false);
                $(self.settings.btnSubmit).text('Save');

                $('#previewEvent').on('show', function () {
                    modalBody = $(this).find('.modal-body');
                });
            }

            self.__removeCategoryConflict = function()
            {
                var defaultCategories = $(self.settings.defaultCategories).text().split(',');
                var ind = $(self.settings.inpCategory + ' :selected').val();

                if (defaultCategories.indexOf(ind) === -1) {
                    defaultCategories.forEach(function(cat){
                        self.__removeCategory($("a[catid=" + cat + "]"));
                    });
                } else {
                    var ind = $(self.settings.inpCategory + ' :selected').val();

                    var catsToDelete = $(self.settings.inpCategoryReal).val();
                    defaultCategories.forEach(function(cat){
                        catsToDelete = catsToDelete.replace(cat + ',', '');
                    });
                    var categories = catsToDelete.split(',');

                    categories.forEach(function(cat) {
                        if (cat == "") return;

                        if (defaultCategories.indexOf(ind) !== -1) {
                            self.__removeCategory($("a[catid=" + cat + "]"));
                        }
                    });
                }
            }

			self.__loadImage = function(content, inpImage)
			{
				var reader = new FileReader();
				var file = content.target.files[0];

				reader.onload = (function(f) {
					$(inpImage).attr('value', f.name);
					return function(e) {
                        var img = new Image();
                        img.src = e.target.result;

                        if (img.width < 180 || img.height < 60) {
                            noti.createNotification('Image size should be min 180x60 pixels!', 'warning');
                        }

                        $(inpImage).parent().find(self.settings.boxImg).attr('src', img.src);
					}
				})(file);

				reader.readAsDataURL(file);
			}

            /*self.__loadImage = function(content)
            {
                var reader = new FileReader();
                var file = content.target.files[0];

                reader.onload = (function(f) {
                    $(self.settings.inpLogo).attr('value', f.name);
                    return function(e) {
                        var img = new Image();
                        img.src = e.target.result;
                        //alert(img.width);

                        if (img.width < 180 || img.height < 60) {
                            noti.createNotification('Image size should be min 180x60 pixels!', 'warning');
                        }

                        $(self.settings.boxImg).attr('src', img.src);
                    }
                })(file);

                reader.readAsDataURL(file);
            }*/

			self.__makePreview = function(img, size)
			{
				var img = image,
					w = img.width, h = img.height,
					s = w / h;

					if(w > size && h > size) {
						if(img.width > img.height) {
							img.width = size;
							img.height = size / s;
						} else {
							img.height = size;
							img.width = size * s;
						}
					}

  					return img;
			}


			self.__imitateUpload = function(fileElement)
			{
                fileElement.click();
			}


			self.__addCategory = function()
			{
                if ($(self.settings.inpCategoryReal).val() == $(self.settings.defaultCategories).text()) {
                    $(self.settings.inpCategoryReal).val('');
                }

				var list = $(self.settings.listCategory);

				var item = '<div class="ecat_elem"><label>' + $(self.settings.inpCategory + ' :selected').text() + '</label>' +
						'<a href="#" class="icon-remove" catid="' + $(self.settings.inpCategory + ' :selected').val() + '"></div>';
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
                if (elem.attr('catid') == undefined) return;

				var item = '<option value="' + elem.attr('catid') + '">' + elem.prev('label').html() + '</option>';
		 		$(self.settings.inpCategory).append(item);
		 		$(self.settings.inpCategoryReal).val($(self.settings.inpCategoryReal).val().replace(elem.attr('catid') + ',', ''));

		        elem.parent('div').remove();

		        if ($(self.settings.listCategory).children('div').length == 0) {
                    $(self.settings.inpCategoryReal).val($(self.settings.defaultCategories).text());

		            $(self.settings.listCategory).hide();
		        }
			}

            self.__initCategoryList = function()
            {
                var categories = $(self.settings.inpCategoryReal).val().split(',');

                categories.forEach(function(cat) {
                    if (cat == "") return;

                    $(self.settings.inpCategory + " option[value='" + cat + "']").remove();
                });
            }

			self.__addSite = function()
			{
				var url = self.settings.inpSite.val();

				if (url.length != 0) {
			        if (url.indexOf('http', 0) < 0) {
			            url = 'http://' + url;
			        }
			        var link = '<div><a target="_blank" href="' + url + '">' + url + '</a>' +
			        			'<a href="#" class="icon-remove"></a></div>';

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
				if (val != '00:00' && val != '') {
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
					txt.attr('placeholder', 'at ' + '00:00');
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

            // setup datetimepicker to close on click
            self.__setupDateTimePicker = function()
            {
                var startDate = $(self.settings.inpDateStart).datetimepicker()
                    .on('changeDate', function(ev) {
                        startDate.datetimepicker('hide');
                    });

                var endDate = $(self.settings.inpDateEnd).datetimepicker()
                    .on('changeDate', function(ev) {
                        endDate.datetimepicker('hide');
                    });
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

		           	if (input == self.settings.inpVenue) {
                        var ven = utils.addressAutocomplete($(input)[0], 'establishment');
			           	google.maps.event.addListener(ven, 'place_changed', function() {
			                var lat = ven.getPlace().geometry.location.ob;
			                var lng = ven.getPlace().geometry.location.pb;

			                $(self.settings.coordsVenueLat).val(lat);
			                $(self.settings.coordsVenueLng).val(lng);
			            });
		           	}
				}
			}

            self.__checkDatesContradictions = function()
            {
                var isValid = true;

                var startDate = $(self.settings.textDateStart).val();
                if ($(self.settings.textTimeStart).val() != '') {
                    startDate += ' ' + $(self.settings.textTimeStart).val();
                } else {
                    startDate += ' ' + "00:00:00";
                }
                startDate = self.__getTimeInMs(startDate);

                var endDate = $(self.settings.textDateEnd).val();
                if ($(self.settings.textTimeEnd).val() != '') {
                    endDate += ' ' + $(self.settings.textTimeEnd).val();
                } else {
                    endDate += ' ' + '23:59:59';
                }
                endDate = self.__getTimeInMs(endDate);

                if (startDate > endDate) {
                    isValid = false;
                    noti.createNotification('Start date cannot be greater than end date', 'error');
                }

                return isValid;
            }

            self.__getTimeInMs = function(date)
            {
                var pieces = date.split('/');
                var temp = pieces.shift();

                var newDate = pieces.shift() + '/' + temp + '/' + pieces.join('/');

                return Date.parse(newDate);
            }

            self.__checkRequiredFields = function()
            {
                var isValid = true;

                var fields = [
                    { element : self.settings.inpName, text : 'event title' },
                    { element : self.settings.textDateStart, text : ' start date' },
                    { element : self.settings.textDateEnd, text : 'end date' },
                    { element : self.settings.inpLocation, text : 'location' }
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

            self.__initFieldsForDateTimePicker = function()
            {
                var fields = [
                    $(self.settings.inpDateStart),
                    $(self.settings.inpDateEnd),
                    $(self.settings.inpTimeStart),
                    $(self.settings.inpTimeEnd)
                ];

                fields.forEach(function(field) {
                    field.click(function(){
                        $(this).datetimepicker('show');
                    });
                });
            }

            self.__initFacebookPublish = function()
            {
                if ($(self.settings.externalLogged).length != 1 && $(self.settings.accSynced).val() !== '1') {
                    $(self.settings.eventFbStatus).parent().append(
                        '<br/><span>To publish events on facebook link or sync with your Facebook account at <a href="/profile">profile</a></span>'
                    );
                    $(self.settings.eventFbStatus).prop('checked', false);
                    $(self.settings.eventFbStatus).attr('disabled', true);
                }
            }
		};

		return new frontEventEditControl($, utils, datetimepicker, noti);
	}
);
