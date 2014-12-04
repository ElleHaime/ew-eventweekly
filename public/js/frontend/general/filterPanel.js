define('frontFilterPanel',
	['jquery', 'utils', 'domReady'],
	function($, utils) {

		return {
			settings: {
				btnSwitchPanel: '#swithFilterPanel',
				boxCheckAll: '#check-all',
				boxUncheckAll: '#uncheck-all',
				boxDefaultChoise: '#default-choise',
				isLogged: '#isLogged'
			},
			panelState: null,
			panelWidth: '390px',

			
			init: function() {
				this.__bindClicks();
			},
			
			__bindClicks: function() {
				$(this.settings.btnSwitchPanel).click(function(e) {
					this.__switchPanel();
				});
				
				$(this.settings.boxCheckAll).click(function(e) {
					this.__checkOptions();
				});
				
				$(this.settings.boxCheckAll).click(function(e) {
					this.__uncheckOptions();
				});
				
				$(this.settings.boxDefaultChoise).click(function(e) {
					this.__applyPersonalize();
				});
			},
			
			__switchPanel: function() {
				
			},
			
			__applyPersonalize: function() {
				if ()
			},
			
			__checkOptions: function() {
				
			},
			
			__uncheckOptions: function() {
				
			},
			
			__getAllOptions: function() {
				
			},
			
			__toggleCategory: function() {
				
			}
		}
	}
); 