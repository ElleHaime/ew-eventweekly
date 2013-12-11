define('frontEventListControl',
	['jquery', 'utils', 'fb', 'domReady'],
	function($, utils, fb) {

		function frontEventListControl($, utils) 
		{
			var self = this;

			self.settings = {
				btnEdit: '.editEvent',
				btnDelete: '.deleteEvent',
				btnPublish: '.publishEvent',
				btnUnpublish: '.unpublishEvent'
			},
			
			self.init = function()
			{
				self.bindClicks();
			},

			self.bindClicks = function()
			{
				$(self.settings.btnEdit).click(function(e) {
					e.preventDefault();
					self.__redirectEdit($(this));				
				});

				$(self.settings.btnDelete).click(function(e) {
					e.preventDefault();
				});

				$(self.settings.btnPublish).click(function(e) {
					e.preventDefault();
					self.__unpublish($(this));
				});

				$(self.settings.btnUnpublish).click(function(e) {
					e.preventDefault();
					self.__unpublish($(this));
				});

			},

			self.__publish = function()
			{

			},

			self.__unpublish = function()
			{

			},

			self.__redirectEdit = function(elem)
			{
				console.log(elem);
				window.location.href = '/event/edit/' + elem.attr('id');
			},

			self.__delete = function()
			{

			}
		};
		
		return new frontEventListControl($, utils, fb);
	}
);