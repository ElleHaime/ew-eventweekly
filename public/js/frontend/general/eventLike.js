define('frontEventLike',
	['jquery', 'noty', 'fb', 'utils', 'underscore'],
	function($,  noty, fb, utils) {
		function frontEventLike($, noty, fb)
		{
			var self = this;
			
			self.settings = {
                userEventsLiked: '#userEventsLiked',

		        likeUrl: '/event/like',
		        likeBtnList: '.eventLikeBtn',
		        dislikeBtnList: '.eventDislikeBtn',
		        likeBtnShow: '#event-like-btn',
		        dislikeBtnShow: '#event-dislike-btn',
		        eventElem: '.b-list-of-events-g__item.pure-u-1-3.event-list-event'
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
		        $(self.settings.likeBtnList).click(function(event) {
		        	event.preventDefault();		        	
			    	self.__clickHandler($(this), 'list');
		        });
		        
		        $(self.settings.dislikeBtnList).click(function(event) {
		        	event.preventDefault();
		        	self.__clickHandler($(this), 'list');
		        }); 
		        
		        $(self.settings.likeBtnShow).click(function(event) {
		        	event.preventDefault();		        	
			    	self.__clickHandler($(this), 'show');
		        });
		        
		        $(self.settings.dislikeBtnShow).click(function(event) {
		        	event.preventDefault();
		        	self.__clickHandler($(this), 'show');
		        });
		    },

		    /**
		     * Click handler
		     *
		     * @returns {Function}
		     * @private
		     */
		    self.__clickHandler = function(elem, template) {
	            var status = elem.data('status'),
	                eventId = elem.data('id');
	            var url = self.settings.likeUrl+'/'+eventId+'/'+status;
	            
	            if (template == 'list') {
		            self.target = $(elem).attr('class');	            	
	            } else {
	            	self.target = null;
	            }

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
		    	//console.log(data);
		        if (data.status == true) {
		        	if (!self.target) {
			        	if (data.member_like == 1) {
			        		$(self.settings.likeBtnShow).hide();
			        		$(self.settings.dislikeBtnShow).show();
			        	} else {
			        		$(self.settings.likeBtnShow).show();
			        		$(self.settings.dislikeBtnShow).hide();
			        	}
		        	} else {
			        	var like = $(self.target + self.settings.likeBtnList + '[data-id=' + data.event_id + ']');
			        	var dislike = $(self.target + self.settings.dislikeBtnList + '[data-id=' + data.event_id + ']');
			        	
			        	if (data.member_like == 1) {
	                        like.blur();
	                        //alert('liked');
	                        like.prop('disabled', true);
			        		dislike.prop('disabled', false);
	
	                        $(self.settings.userEventsLiked).text(data.userEventsLiked)
			        	} else {
			        		//alert('disliked');
			        		$('div' + self.settings.eventElem + '[event-id=' + data.event_id + ']').remove();
	
	                        if (data.likeCounter != null) {
	                            $(self.settings.userEventsLiked).text(data.userEventsLiked);
	                        }
			        	}
		        	}
		        } else {
		        	if (data.error  == 'not_logged') {
                        noty({text: 'Please <a href="#" class="fb-login-popup" onclick="return false;">login via Facebook</a> to be able to like events',  type: 'warning'});
		        	} else {
                        noty({text: 'Oops! Error occurred. Can\'t save you choice.', type: 'error'});
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
                var counter = parseInt($(self.settings.userEventsLiked).text()) - 1;
                $(self.settings.userEventsLiked).text(counter);
            }

		};
		
		return new frontEventLike($, noty, fb, utils);
});