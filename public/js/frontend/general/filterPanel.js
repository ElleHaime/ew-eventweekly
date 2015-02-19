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
				tagBlock: '.userTag-subfilters',
				categoryBtn: '.userFilter-category',
				toggleBtn: '.categories-accordion__arrow'
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
				
				$(this.settings.boxUncheckAll).click(function(e) {
					$this.__uncheckOptions();
				});
				
				$(this.settings.boxDefaultChoise).click(function(e) {
					$this.__applyPersonalize();
				});
				$(this.settings.categoryBtn).click(function(e) {
					$this.__getAllOptions(e);
				});
				$(this.settings.toggleBtn).click(function(e) {
					$this.__toggleCategory(e);
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
				var personalTags = $('#tagIds').val();
            	console.log(personalTags);

            	if (personalTags=='') {
            		noty({text: 'Please login and set pesonal tags first!', type: 'error'});
            		return true;
            	}

            	

				$('.userFilter-tag').each(function() { //loop through each checkbox
					var tagNumber = this.id.replace( /[^\d.]/g, '' );
					this.checked = true;
					if (personalTags.indexOf(","+tagNumber+",") >= 0) {
						this.checked = false;		
					}


                	//this.checked = false;  //select all checkboxes with class "userFilter-tag"               
            	});

			},
			
			__checkOptions: function() {
				$('.userFilter-tag').each(function() { //loop through each checkbox
                	this.checked = true;  //select all checkboxes with class "userFilter-tag"               
            	});
            	$('.userFilter-category').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "userFilter-category"               
            	});
            	
            
			},


			
			__uncheckOptions: function() {
				$('.userFilter-tag').each(function() { //loop through each checkbox
                	this.checked = false;  //select all checkboxes with class "userFilter-tag"               
            	});
            	$('.userFilter-category').each(function() { //loop through each checkbox
                	this.checked = false;  //select all checkboxes with class "userFilter-category"               
            	});
			},
			
			__getAllOptions: function(e) {
				var accordionItem = $(e.target).closest('.categories-accordion__item');
				if( e.target.checked ) {
					accordionItem.find('.userFilter-tag').each(function() { //loop through each checkbox
	                	this.checked = true;  //select all checkboxes with class "userFilter-tag"               
	            	});
	            } else {
	            	accordionItem.find('.userFilter-tag').each(function() { //loop through each checkbox
	                	this.checked = false;  //deselect all checkboxes with class "userFilter-tag"               
	            	});
	            }

			},
			
			__toggleCategory: function(e) {
				var accordionItem = $(e.target).closest('.categories-accordion__item');
				accordionItem.find('.userTag-subfilters').toggle();
			}
		}
	}
); 