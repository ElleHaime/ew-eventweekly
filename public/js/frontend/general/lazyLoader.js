define('lazyLoader',
    ['jquery', 'utils', 'noty', 'lazyload'],
    function($, utils, noty, lazyload) {

        function lazyLoader($, utils, noty, lazyload)
        {
        	var self = this;

        	self.settings = 
        	{
        		loadMoreButton: '#load_more',
        		divToUpdate: '.page__wrapper',
        		divToOverlay: '.page'
        	},

        	self.init = function()
        	{
        		alert('bububu');
        		// initialize clicks
        		self.__bindClicks();
        	},

        	self.__bindClicks = function() 
        	{
        		$(self.settings.loadMoreButton).click(function() {
        			alert('ololo');
        			self.__someFunc();
        		});
        		alert('trampampam');
        	},

        	self.__someFunc = function()
        	{
        		alert('someFunc');
        	}
        };
        
        return new lazyLoader($, utils, noty, lazyload);
});
