define('frontEventListControl',
	['jquery', 'utils', 'fb', 'noti', 'domReady'],
	function($, utils, fb, noti) {

		function frontEventListControl($, utils, fb, noti)
		{
			var self = this;

			self.settings = {
                userEventsCreated: '#userEventsCreated',
                userEventsLiked: '#userEventsLiked',
                userEventsGoing: '#userEventsGoing',

				btnEdit: '.editEvent',
				btnDelete: '.deleteEvent',
				btnPublish: '.publishEvent',
				btnUnpublish: '.unpublishEvent',
				classStatus: '.btn.eventStatus',
				classElement: '.row-fluid.eventListing',

                accSynced: '#acc_synced',
                externalLogged: '#external_logged'
			},
			self.processedElement = null,

			self.init = function()
			{
				self.bindClicks();
			},

			self.bindClicks = function()
			{
				$(self.settings.btnEdit).click(function(e) {
					e.preventDefault();
					self.__redirectEdit($(this));				
				});

				$(self.settings.btnDelete).click(function(e) {
                    if (confirm('Are you sure you want to remove this event?')) {
                        e.preventDefault($(this));
					    self.__delete($(this));
                    }
				});

				$(self.settings.btnPublish).click(function(e) {
					e.preventDefault();
					self.__publish($(this));
				});

				$(self.settings.btnUnpublish).click(function(e) {
					e.preventDefault();
					self.__publish($(this));
				});

                if ($(self.settings.externalLogged).length != 1 && $(self.settings.accSynced).val() !== '1') {
                    noti.createNotification(
                        'To see your facebook events please login using facebook or syncronise your <a href="/profile">Profile</a> with facebook account',
                        'warning'
                    );
                }
			},


			self.__delete = function(elem) 
			{
				var params = {
					id: elem.attr('id')
				};

				$.when(utils.request('post', '/event/delete', params)).then(function(data) {
					data = $.parseJSON(data);
		            if (data.status == 'OK') {
		            	self.__hideEvent(data.id);

                        if (data.userEventsGoing != parseInt($(self.settings.userEventsGoing).text())) {
                            self.__minusUserEventsGoing();
                        }

                        if (data.userEventsLiked != parseInt($(self.settings.userEventsLiked).text())) {
                            $(self.settings.userEventsLiked).text(data.userEventsLiked);
                        }

                        self.__minusUserEventsCreated();
		            }
				});
			}

            self.__minusUserEventsCreated = function()
            {
                var counter = parseInt($(self.settings.userEventsCreated).text()) - 1;
                $(self.settings.userEventsCreated).text(counter);
            }

            self.__minusUserEventsLiked = function()
            {
                var counter = parseInt($(self.settings.userEventsLiked).text()) - 1;
                $(self.settings.userEventsLiked).text(counter);
            }

            self.__minusUserEventsGoing = function()
            {
                var counter = parseInt($(self.settings.userEventsGoing).text()) -1;
                $(self.settings.userEventsGoing).text(counter);
            }

			self.__publish = function(elem)
			{
				if (elem.hasClass('unpublishEvent')) {
					var event_status = 0;
					var method = 'unpublish';
				} else {
					var event_status = 1;
					var method = 'publish';
				}

				var params = {
					id: elem.attr('id'),
					event_status: event_status
				};

				self.processedElement = elem;
				$.when(utils.request('post', '/event/' + method, params)).then(function(data) {
					data = $.parseJSON(data);
		            if (data.status == 'OK') {
		            	self.__changePublishStatus(data.id, data.event_status);
		            }
				});
			},


			self.__changePublishStatus = function(elemId, status)
			{
				if (status == 0) {
					self.processedElement.removeClass('unpublishEvent').addClass('publishEvent');
					self.processedElement.children().text('Publish');
				} else {
					self.processedElement.removeClass('publishEvent').addClass('unpublishEvent');
					self.processedElement.children().text('Unpublish');
				}

				self.processedElement = null;
			},


			self.__hideEvent = function(elemId)
			{
                $("div[event-id=" + elemId + "]").remove();
			},

			self.__redirectEdit = function(elem)
			{
				window.location.href = '/event/edit/' + elem.attr('id');
			}
		};
		
		return new frontEventListControl($, utils, fb, noti);
	}
);