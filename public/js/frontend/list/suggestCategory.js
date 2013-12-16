define('frontListSuggestCategory',
	['jquery', 'noti', 'underscore'],
	function($, noti) {
		function frontListSuggestCategory($, noti) 
		{
			var self = this;
			
			self.settings = {
		        btn: '#suggestCategoryBtn',
		        categoriesBlock: '#suggestCategoriesBlock',
		        uncategorizedLabel: '.uncategorized_label'
		    },
		    
		    self.selectedCategory = null,

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

		        if (response.status == true) {
		            $(self.settings.uncategorizedLabel).text(self.selectedCategory);
		            $(self.settings.btn).remove();
		            $(self.settings.categoriesBlock).remove();
		        } else {
		            alert('Oops! Some error occurred. Call to administrator');
		        }
		    }

		};
		
	return new frontListSuggestCategory($, noti);
});