define('frontCampaignListControl',
	['jquery', 'utils', 'fb', 'domReady'],
	function($, utils) {

		function frontCampaignListControl($, utils) 
		{
			var self = this;

			self.settings = {
				btnEdit: '.editCampaign',
				btnDelete: '.deleteCampaign',
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
		
		return new frontCampaignListControl($, utils);
	}
);