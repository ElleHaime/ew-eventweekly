define('frontListSuggestCategory',
	['jquery', 'noti', 'underscore'],
	function($, noti) {
		function frontListSuggestCategory($, noti) 
		{
			var self = this;
			
			self.settings = {
		        btn: '#suggestCategoryBtn',
		        categoriesBlock: '#suggestCategoriesBlock',
		        uncategorizedLabel: '.uncategorized_label',
                eventCategoryTitle: '.event-title span'
		    },
		    
		    self.selectedCategory = null,
            self.selectedCategoryKey = null,

		    self.init = function(options){
		        self.settings = _.extend(self.settings, options);
		        self.__bindClicks();
		    },

		    self.__bindClicks = function(){
		        $(self.settings.btn).click(function(){
		            self.__showCategories();
		        });

		        $('body').on('click', self.settings.categoriesBlock+' a', function(e){
		            e.preventDefault();
		            self.__setCategory($(e.target));
		        });
		    },

		    self.__showCategories = function(){
		        $(self.settings.categoriesBlock).show();
		    },

		    self.__setCategory = function(linkObj) {
		        var url = linkObj.attr('href');
	        
		        self.selectedCategory = linkObj.text();
                self.selectedCategoryKey = linkObj.attr('data-catkey');

		        $.when(self.__sendRequest(url)).then(function(response){
		            self.__responseHandler(response);
		        });
		    },

		    self.__sendRequest = function(url){
		        return $.ajax({
		            url: url,
		            type: 'GET',
		            dataType: 'json'
		        });
		    },

		    self.__responseHandler = function(response){
		        if (response[0].status == true) {
                    $(self.settings.eventCategoryTitle).text(self.selectedCategory);
                    $('.other-title').removeClass('other-title').addClass(self.selectedCategoryKey + '-title');
                    $('.other-color').removeClass('other-color').addClass(self.selectedCategoryKey + '-color');

		            $(self.settings.uncategorizedLabel).text(self.selectedCategory);
		            $(self.settings.btn).remove();
		            $(self.settings.categoriesBlock).remove();
		        } else {
		        	if (response[0].error == 'not_logged') {
		        		//window.location.href = '/#fb-login';
                        noti.createNotification('Please <a href="#" class="fb-login-popup" onclick="return false;">login via Facebook</a> to do this', 'warning');
		        	} else {
		        		alert('Oops! Some error occurred. Call to administrator');
		        	}
		        }
		    }

		};
		
	return new frontListSuggestCategory($, noti);
});