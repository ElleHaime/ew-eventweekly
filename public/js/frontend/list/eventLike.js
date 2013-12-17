define('frontListEventLike',
	['jquery', 'noti', 'underscore'],
	function($, noti) {
		function frontListEventLike($, noti) 
		{
			var self = this;
			
			self.settings = {
		        likeUrl: '/event/like',
		        likeBtn: '.eventLikeBtn',
		        likeBlock: '.like-box',
		        thank: 'Thank!'
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
		        $(self.settings.likeBtn).click(function() {
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

	            $.when(self.__sendStatus(eventId, status)).then(function(response){
	                self.__responseHandler(response);
	            });
		    },

		    /**
		     * Send request to server
		     *
		     * @param eventId
		     * @param status
		     * @returns {*}
		     * @private
		     */
		    self.__sendStatus = function(eventId, status) {
		        var url = self.settings.likeUrl+'/'+eventId+'/'+status;
        
		        return $.ajax({
		            url: url,
		            type: 'GET',
		            dataType: 'json'
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
		            $(self.target).closest(self.settings.likeBlock).html(self.settings.thank);
		        } else {
		        	if (data.error  == 'not_logged') {
		        		window.location.href = 'login';
		        	} else {
			            noti.createNotification('Oops! Error occurred. Can\'t save you choice.', 'error');		        		
		        	}
		        }
		    }

		};
		
		return new frontListEventLike($, noti);
});