define('frontFilterPanel',
	['jquery', 'utils', 'domReady'],
	function($, utils) {

		return {
			settings: {
				btnSwitchPanel: '#swithFilterPanel',
				boxCheckAll: '#check-all',
				boxUncheckAll: '#uncheck-all',
				boxDefaultChoise: '#default-choise',
				isLogged: '#isLogged',
				overlayBlock: '#filter-panel-overlay',
				filterPanel: '#filters',
				categoryBox: '.userFilter-category',
				categoryExpander: '.userFilter-category-expander',
				tagBox: '.userFilter-tag',
				tagBlock: '.userTag-subfilters'
			},
			panelState: null,
			panelWidth: '390px',
			expandClass: 'categories-accordion__arrow--is-expanded',

			
			init: function() {
				this.__bindClicks();
			},
			
			
			__bindClicks: function() {
				var $this = this;
				
				$(this.settings.btnSwitchPanel).click(function(e) {
					$this.__switchPanel();
				});
				
				$(this.settings.boxCheckAll).click(function(e) {
					$this.__checkOptions();
				});
				
				$(this.settings.boxCheckAll).click(function(e) {
					$this.__uncheckOptions();
				});
				
				$(this.settings.boxDefaultChoise).click(function(e) {
					$this.__applyPersonalize();
				});
			},
			
			__switchPanel: function() {
				if (!this.panelState) {
					$(this.settings.btnSwitchPanel).css('left', this.panelWidth);
					$(this.settings.overlayBlock).show();
					$(this.settings.filterPanel).show();
					
					this.panelState = 'active';
				} else {
					$(this.settings.btnSwitchPanel).css('left', '');
					$(this.settings.overlayBlock).hide();
					$(this.settings.filterPanel).hide();
					
					this.panelState = null;
				}
			},
			
			__applyPersonalize: function() {
				
			},
			
			__checkOptions: function() {
				alert('ololo');
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