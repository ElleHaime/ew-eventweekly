define('frontCampaignListControl',
	['jquery', 'utils', 'fb', 'domReady'],
	function($, utils) {

		function frontCampaignListControl($, utils) 
		{
			var self = this;

			self.settings = {
				btnEdit: '.editCampaign',
				btnDelete: '.deleteCampaign'
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
					e.preventDefault($(this));
                    self.__delete($(this));
				});
			},

			self.__redirectEdit = function(elem)
			{
				window.location.href = '/campaign/edit/' + elem.attr('id');
			},

            self.__delete = function(elem)
            {
                var params = {
                   id: elem.attr('id')
                };
                $.when(utils.request('post', '/campaign/delete', params)).then(function(data) {
                    data = $.parseJSON(data);
                    if (data.status == 'OK') {
                        $('#element_' + data.id).remove();
                    }
                });
            }
		};
		
		return new frontCampaignListControl($, utils);
	}
);