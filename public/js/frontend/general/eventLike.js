define('frontEventLike',
	['jquery', 'noti', 'utils', 'underscore'],
	function($, noti, utils) {
		function frontEventLike($, noti) 
		{
			var self = this;
			
			self.settings = {
                userEventsLiked: '#userEventsLiked',

		        likeUrl: '/event/like',
		        likeBtn: '.eventLikeBtn',
		        dislikeBtn: '.eventDislikeBtn',
		        eventElem: '.signleEventListElement'
		    },

		    self.target = null,
		    
		    /**
		     * Constructor
		     *
		     * @param options
		     */
		    self.init = function(options){
		        // extend options
		        self.settings = _.extend(self.settings, options);
		        
		        // initialize clicks
		        _.once(self.__bindClicks());
		    },

		    /**
		     * initialize clicks
		     *
		     * @private
		     */
		    self.__bindClicks = function() {
		        $(self.settings.likeBtn).click(function(event) {
		        	event.preventDefault();
		        	self.__clickHandler($(this));
		        });
		        
		        $(self.settings.dislikeBtn).click(function(event) {
		        	event.preventDefault();
		        	self.__clickHandler($(this));
		        });
		    },

		    /**
		     * Click handler
		     *
		     * @returns {Function}
		     * @private
		     */
		    self.__clickHandler = function(elem) {
	            var status = elem.data('status'),
	                eventId = elem.data('id');

	            self.target = elem;

	            var url = self.settings.likeUrl+'/'+eventId+'/'+status;
	            
	            $.when(utils.request('get', url)).then(function(response){
	                self.__responseHandler(response);
	            });
		    },

		    /**
		     * Handle response from server
		     *
		     * @param data
		     * @private
		     */
		    self.__responseHandler = function(data) {
		        if (data.status == true) {
		        	var like = $('button' + self.settings.likeBtn + '[data-id=' + data.event_id + ']');
		        	var dislike = $('button' + self.settings.dislikeBtn + '[data-id=' + data.event_id + ']');
		        	
		        	if (data.member_like == 1) {
		        		like.prop('disabled', true);
		        		dislike.prop('disabled', false);

                        $(self.settings.userEventsLiked).text(data.likeCounter)
		        	} else {
		        		$('div' + self.settings.eventElem + '[event-id=' + data.event_id + ']').remove();

                        if (data.likeCounter != null) {
                            $(self.settings.userEventsLiked).text(data.likeCounter);
                        }
		        	}
		        } else {
		        	if (data.error  == 'not_logged') {
		        		window.location.href = 'login';
		        	} else {
			            noti.createNotification('Oops! Error occurred. Can\'t save you choice.', 'error');		        		
		        	}
		        }
		    }

            self.__plusUserEventsLiked = function()
            {
                var counter = parseInt($(self.settings.userEventsLiked).text()) + 1;
                $(self.settings.userEventsLiked).text(counter);
            }

            self.__minusUserEventsLiked = function()
            {
                console.log('minus');
                var counter = parseInt($(self.settings.userEventsLiked).text()) - 1;
                $(self.settings.userEventsLiked).text(counter);
            }

		};
		
		return new frontEventLike($, noti, utils);
});