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
		        likeStatusBar: '#status-bar-like',
		        eventElem: '.b-list-of-events-g__item.pure-u-1-3.event-list-event',
		        likeClass: 'ew-button',
		        dislikeClass: 'ew-button-dis',
                pageWasChanged: '#pageWasChanged'
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
		        
		        $(self.settings.pageWasChanged).change(function(event) {
		        	$(self.settings.likeBtnList).each(function() {
           				$(this).unbind('click').bind('click', function() {
           					event.preventDefault();		
           					self.__clickHandler($(this), 'list');		
           				});
           			});
		        	
		        	$(self.settings.dislikeBtnList).each(function() {
           				$(this).unbind('click').bind('click', function() {
           					event.preventDefault();		
           					self.__clickHandler($(this), 'list');		
           				});
           				
           			});
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
		            self.target = $(elem).attr('class').replace(/\s+/g, '.');	            	
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
		        if (data.status == true) {
		        	if (!self.target) {
			        	if (data.member_like == 1) {
			        		$(self.settings.likeBtnShow).hide();
			        		$(self.settings.dislikeBtnShow).show();
			        		$(self.settings.likeStatusBar).show();
			        	} else {
			        		$(self.settings.likeBtnShow).show();
			        		$(self.settings.dislikeBtnShow).hide();
			        		$(self.settings.likeStatusBar).hide();
			        	}
		        	} else {
			        	var like = $('.' + self.target + '[data-id=' + data.event_id + ']');
			        	var dislike = $('.' + self.target + '[data-id=' + data.event_id + ']');
		       		        	
			        	if (data.member_like == 1) {
			        		$(like).find('a').addClass(self.settings.dislikeClass);
			        	} else {
			        		var elem = $(self.settings.eventElem + '[data-event-id=' + data.event_id + ']');
			        		$(like).find('a').removeClass(self.settings.dislikeClass);
			        		$(elem).remove();
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