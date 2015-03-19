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
				toggleBtn: '.categories-accordion__arrow',
				filtersWrapper: '.b-filters__wrapper',
				personalPresetState: '#personalPresetActive',
				personalPresetTags: '#tagIds'
			},
			checkboxAction: '',
			panelState: null,
			panelWidth: '350px',
			expandClass: 'categories-accordion__arrow--is-expanded',

			
			init: function() {
				this.__bindClicks();
				this.__setCategoriesChecked();
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
				$(this.settings.tagBox).click(function(e) {
					$this.__tagClicked();
				});

			},

			/*
			**********************
			* =check/uncheck category checkboxes depending on inner tags
			**********************
			*/
			__setCategoriesChecked: function() {
				$(this.settings.filtersWrapper).find(this.settings.categoryBox).each(function() { 
					var isChecked = true;
					if ($(this).closest('.categories-accordion__item').find('.userFilter-tag').length > 0) {
						$(this).closest('.categories-accordion__item').find('.userFilter-tag').each(function() {
	                        if($(this).is(':checked') === false) {
	                        	isChecked = false;
	                        	return false;
	                        }
						});
						this.checked = isChecked;
					} else {
						
					}
				});
			},
			
			/*
			**********************
			* =show/hide filter panel
			**********************
			*/
			__switchPanel: function() {
				if (!this.panelState) {
					$(this.settings.btnSwitchPanel).css('left', this.panelWidth);
					if ($(window).width()<400) {
						$(this.settings.btnSwitchPanel).css('left', $(window).width()-40);
					}
					$(this.settings.btnSwitchPanel).css('z-index', "1000");
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

			/*
			**********************
			* =when tag clicked, check parent category 
			**********************
			*/
			__tagClicked: function() {
				this.__setCategoriesChecked();
			},
			
			/*
			**********************
			* =set default tags
			**********************
			*/
			__applyPersonalize: function() {
				var $this = this;
				
				var personalTags = $(this.settings.personalPresetTags).val().split(',');
				if (personalTags.length == 0) {
            		noty({text: 'Please set your default tags first!', type: 'error'});
            		return true;
            	}
				
				$('.userFilter-tag').each(function() { //loop through each checkbox
					var tagNumber = this.id.replace( /[^\d.]/g, '' );
					if ($.inArray(tagNumber, personalTags) >= 0) {
						this.checked = true;		
					} else {
						this.checked = false;
					}               
            	});
				//$(this.settings.personalPresetTags).val('1');
				$(this.settings.personalPresetState).val('1');				
            	this.__setCategoriesChecked();
			},
			
			/*
			**********************
			* =check all 
			**********************
			*/
			__checkOptions: function() {
				var $this = this;
				
				$('.userFilter-tag').each(function() { //loop through each checkbox
                	this.checked = true;  //select all checkboxes with class "userFilter-tag"               
            	});
            	$('.userFilter-category').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "userFilter-category"               
            	});
            	
            	$(this.settings.personalPresetState).val('0');
//            	$(this.settings.personalPresetTags).val('0');
			},

			/*
			**********************
			* =uncheck all
			**********************
			*/
			__uncheckOptions: function() {
				var $this = this;
				
				$('.userFilter-tag').each(function() { //loop through each checkbox
                	this.checked = false;  //select all checkboxes with class "userFilter-tag"               
            	});
            	$('.userFilter-category').each(function() { //loop through each checkbox
                	this.checked = false;  //select all checkboxes with class "userFilter-category"               
            	});
            	$(this.settings.personalPresetState).val('0');
  //          	$(this.settings.personalPresetTags).val('0');
			},
			
			/*
			**********************
			* =check/uncheck checkboxes in category
			**********************
			*/
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
				$(this.settings.personalPresetState).val('0');
			},
			
			/*
			**********************
			* =show/hide tags in category
			**********************
			*/
			__toggleCategory: function(e) {
				var accordionItem = $(e.target).closest('.categories-accordion__item');
				accordionItem.find('.userTag-subfilters').toggle();
			}
		}
	}
); 