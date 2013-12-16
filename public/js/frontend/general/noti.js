/**
 * Noti - plugin for notification.
 * Supported types - info, error, warning, prompt
 *
 * Simple info notification
 * <code>
 *     noti.createNotification('Hello User!', 'info');
 * </code>
 *
 * Prompt Example
 * <code>
 *     noti.createNotification('Are you sure?', 'prompt', function(state) {
 *          console.log(state);
 *      });
 * </code>
 *
 * Created by Slava Basko on 12/10/13 <basko.slava@gmail.com>
 */

define('noti',
	['jquery', 'underscore'],
	function($) {
		function noti($) {
			
			var self = this;
			
			self.settings = {
		        notiBtn: '.notiBtn',
		        notiBlock: '.notiBlock',
		        timeOut: 9000,
		        defaultHeight: 50,
		        defaultSpeed: 300,
		        yesText: 'Ok',
		        noText: 'Cancel',
		        promptBtnClass: 'notiABtn',
		        notiBtnArea: '.notiBtnArea'
		    };
		    self.types = ['info', 'error', 'warning', 'prompt'];
		    self.promptCallback = null;

		    // extend options
/*		    self.settings = _.extend(self.settings, options);

		    self.window = $(self.settings.notiBlock);

		    // bind click to close notification window
		    self.window.on('click', '.notiHide', function(e) {
		        e.preventDefault();
		        self.__hideWindow().call(self);
		    });

		    // bind click on prompt buttons
		    self.window.on('click', '.'+self.settings.promptBtnClass, function(e) {
		        e.preventDefault();
		        self.promptCallback($(e.target).data('type'));
		    }); */
		    
		    self.init = function(options)
		    {
			    self.settings = _.extend(self.settings, options);
		    	self.window = $(self.settings.notiBlock);

			    // bind click to close notification window
			    self.window.on('click', '.notiHide', function(e) {
			        e.preventDefault();
			        self.__hideWindow().call(self);
			    });

			    // bind click on prompt buttons
			    self.window.on('click', '.'+self.settings.promptBtnClass, function(e) {
			        e.preventDefault();
			        self.promptCallback($(e.target).data('type'));
			    });
		    	
		    }

		    /**
		     * Create notification window
		     *
		     * @param text
		     * @param type
		     * @param promptOption
		     */
		    self.createNotification = function(text, type, promptOption) {

		        self.window.find('#notiText').html(text);

		        var cssClass = 'Info';

		        if (!self.__isEmpty(type)) {
		            if (_.include(self.types, type)) {
		                switch (type) {
		                    case 'error': cssClass = 'Error';
		                        break;
		                    case 'warning': cssClass = 'Warning';
		                        break;
		                }
		            }else {
		                return false;
		            }
		        }

		        if (type === 'prompt') {
		            self.__preparePrompt(promptOption);
		        }

		        self.window.addClass('notiWindowType'+cssClass);

		        self.__showWindow();

		        if (type !== 'prompt') {
		            _.delay(self.__hideWindow(), self.settings.timeOut);
		        }
		    };

		    /**
		     * Prepare OK and CANCEL buttons
		     *
		     * @param callback
		     * @private
		     */
		    self.__preparePrompt = function(callback) {
		        var ok = '<a href="#" data-type="yes" class="'+self.settings.promptBtnClass+'">'+self.settings.yesText+'</a>';
		        var cancel = '<a href="#" data-type="no" class="'+self.settings.promptBtnClass+'">'+self.settings.noText+'</a>';

		        $(self.settings.notiBtnArea).html('').html(ok+' '+cancel);

		        self.promptCallback = callback;
		    };

		    /**
		     * Show notification window
		     *
		     * @private
		     */
		    self.__showWindow = function() {
		        console.log('show');
		        
		        self.window.animate({
		            height: self.settings.defaultHeight+'px'
		        }, self.settings.defaultSpeed);
		    };

		    /**
		     * Hide notification window
		     *
		     * @returns {Function}
		     * @private
		     */
		    self.__hideWindow = function() {
		        return function() {
		            self.window.animate({
		                height: 0
		            }, self.settings.defaultSpeed);
		        }
		    };

		    /**
		     * Check if variable exist and not empty
		     *
		     * @param variable
		     * @returns {*}
		     * @private
		     */
		    self.__isEmpty = function(variable) {
		        return (_.isUndefined(variable) && _.isNull(variable) && _.isNaN(variable));
		    }
		    
		}
		
		return new noti($);
});