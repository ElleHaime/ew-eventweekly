define('frontEventLike',
	['jquery', 'noti', 'utils', 'underscore'],
	function($, noti, utils) {
		function frontEventLike($, noti) 
		{
			var self = this;
			
			self.settings = {
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
		        	} else {
		        		$('div' + self.settings.eventElem + '[event-id=' + data.event_id + ']').remove();
		        	}
		        } else {
		        	if (data.error  == 'not_logged') {
		        		window.location.href = 'login';
		        	} else {
			            noti.createNotification('Oops! Error occurred. Can\'t save you choice.', 'error');		        		
		        	}
		        }
		    }

		};
		
		return new frontEventLike($, noti, utils);
});