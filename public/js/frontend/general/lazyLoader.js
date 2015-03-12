define('lazyLoader',
    ['jquery', 'utils', 'noty', 'lazyload'],
    function($, utils, noty, lazyload) {

        function lazyLoader($, utils, noty, lazyload)
        {
        	var self = this;

        	self.settings = 
            {
                loadMoreButton: '#load_more',
                divToUpdate: '.page__wrapper_ajax_search',
                divToOverlay: '.page',
                preloader: '#preloader',
                pageNumber: 1,
                url: window.location.href,
                nextPageUrl: '',
                totalPagesJs: 0,
            },

        	self.init = function()
        	{
        		$(self.settings.loadMoreButton).hide();
                self.settings.totalPagesJs = document.getElementById("totalPagesJs").textContent;
    
                if ( self.settings.totalPagesJs>1 ) {
                    $(self.settings.loadMoreButton).show();
                }       
                $("img.lazy").lazyload();
        		// initialize clicks
        		self.__bindClicks();
        	},

        	self.__bindClicks = function() 
        	{
        		$(self.settings.loadMoreButton).click(function() {
        			//alert('ololo');
        			self.__someFunc();
        		});
        		//alert('trampampam');
        	},

        	self.__someFunc = function()
        	{
                 $("img.lazy").lazyload();
        
                //overlay before request done
                $(self.settings.divToOverlay)
                    .css({
                        'opacity' : 1,
                        'opacity' : 0.4,
                        //'background-color': 'black',
                    });
                $(self.settings.preloader)
                    .css({
                        'position' : 'fixed',
                        'top': '30%',
                        'left': $(window).width()/2-128/2,
                        'display': 'inline'
                    });
                self.settings.pageNumber++;

                if (self.settings.pageNumber>=self.settings.totalPagesJs ) {
                    $(self.settings.loadMoreButton).hide();
                }

                nextPageUrl = self.settings.url + '&page=' + self.settings.pageNumber;
                $.ajax({url:nextPageUrl,
                	success: function(result) 
                	{
console.log(result);                		
                		$(self.settings.divToUpdate).last().html(result);
                		$(self.settings.divToOverlay).css({'opacity' : 1});
                            //'background-color': 'transparent'
                		$(self.settings.preloader).css({'display': 'none'});
                	}
                });
        	}
        };
        
        return new lazyLoader($, utils, noty, lazyload);
});
