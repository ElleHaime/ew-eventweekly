define('base',
	['jquery'],
	function($) {

		function base($)
		{
			this.bindClick = function()
			{
				console.log(1232131);
			}
		};

		return new base($);
	}
);