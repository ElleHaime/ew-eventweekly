define('frontSearchPanel', 
        ['jquery', 'noty', 'utils', 'normalDatePicker', 'underscore', 'domReady', 'google!maps,3,other_params:key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
        function($, noty, utils, normalDatePicker) {

		    return {
		        /**
		         * Settings
		         */
		        settings: {
		            searchFormId: 'topSearchForm',
		            searchForm: '#topSearchForm',
		            searchSubmit: '#searchSubmit',
		            searchTitle: '#searchTitle',
		            searchLocation: '#searchLocationField',
		            searchLocationFormattedAddress: '#searchLocationFormattedAddress',
		            searchTypeResult: '#searchTypeResult',
		            searchTypeResultMenu: '#searchTypeResultMenu',
		            searchTypeResultCurrent: '#searchTypeResultCurrent',
		            startDatePicker: '#js-selectDateTimeStart',
		            startDateField: '#searchPanel-startDate',
		            startDateInput: '#searchStartDate',
		            startDateMain: '#startDate-main',
		            startDateReserve: '#startDate-reserve',
		            endDatePicker: '#js-selectDateTimeEnd',
		            endDateField: '#searchPanel-endDate',
		            endDateInput: '#searchEndDate',
		            endDateMain: '#endDate-main',
		            endDateReserve: '#endDate-reserve',
		            searchGrid: '#searchGridElem',
		            searchGridOption: '.searchGridMenuTab',
		            searchGridInput: '#searchGrid',
		            filterPanelSwitchGrid: '.switchGridButton',
		            isLoggedUser: '#isLogged',
		            addSearchParamUrl: '/search/addSearchParam',
		            datepickerOptions: {
		                format: 'yyyy-mm-dd',
		                pickDate: false,
		                autoclose: true,
		                minView: 2
		            } 
		        },
		        
		        __startDate: false,
		        
		        __endDate: false,
		        
		        /**
		         * TRUE if at least ona option is chosen
		         */
		        __formFilled: false,
		
		        /**
		         * Search type - global or private
		         */
		        __state: null,
		
		        /**
		         * Array of global categories
		         */
		        __globalCategories: [],
		
		        /**
		         * Array of private categories
		         */
		        __privateCategories: [],
		
		        /**
		         * Button that was clicked
		         */
		        __clickedSwitchBtn: null,
		
		        __locationChosen: false,
		
		        /**
		         * Initialize clicks. Constructor
		         */
		        init: function(options) {
		            var $this = this;
		
		            // Extend options
		            $this.settings = _.extend($this.settings, options);
		
		            // Get search type
		            $this.__locationChosen = $($this.settings.searchLocation).data('locationChosen');
		
		            // Bind click on form
		            _.once($this.__bindClicks());
		        },
		
		        /**
		         * Click binding
		         *
		         * @private
		         */
		        __bindClicks: function() {
		            var $this = this;
		            /**
		             *
		             * @type {jQuery}
		             */
		            var body = $('body');
		            //$(body).on('submit', $this.settings.searchForm, function(e) { $this.__submitHandler(); });
		            $(body).on('click', $this.settings.searchSubmit, $this.__submitBtnHandler());
		         
		            // add address autocomplete
		            var list = utils.addressAutocomplete($($this.settings.searchLocation)[0]);
		
		            google.maps.event.addListener(list, 'place_changed', function() {
		                var formattedAddress = {};
              
		                $.each(list.getPlace().address_components, function(index, val) {
		                	formattedAddress[val.types[0]] = val.long_name;
		                });
		                formattedAddress['place_id'] = list.getPlace().place_id;
		                formattedAddress = JSON.stringify(formattedAddress);

		                $this.__setSearchLocation(formattedAddress);
		            });
		
		            // add date picker
		            var nowTemp = new Date();
		            var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

		            $this.__startDate = $($this.settings.startDatePicker)
		            	.datetimepicker($this.settings.datepickerOptions)
		            	.on('changeDate', function(e) {
			            	selMonth = (0+((e.date.getMonth()+1)).toString()).slice(-2);
			            	selDay = (0+(e.date.getDate()).toString()).slice(-2);
			            	
			            	selectedDate = e.date.getFullYear() + '-' + selMonth + '-'+ selDay;  
			            	$($this.settings.startDateField).html(selectedDate);
			            	$($this.settings.startDateInput).val(selectedDate);
		            	});
		            
		            $this.__endDate = $($this.settings.endDatePicker)
		            	.datetimepicker($this.settings.datepickerOptions)
		            	.on('changeDate', function(e) {
			                selMonth = (0+((e.date.getMonth()+1)).toString()).slice(-2);
			                selDay = (0+(e.date.getDate()).toString()).slice(-2);
			                
			                selectedDate = e.date.getFullYear() + '-' + selMonth + '-'+ selDay;  
			                $($this.settings.endDateField).html(selectedDate);
			                $($this.settings.endDateInput).val(selectedDate);
			            }); 

		            $($this.settings.searchGridOption).on('click', function() {
		            	$this.__switchGridHandler($(this));
		            });
		            
		            $($this.settings.searchTypeResultMenu + ' li').click(function(e) {
		            	$this.__switchResultTypeHandler(this);
		            });
		            
		           $this.__disableDatesByGrid();
		        },
		
		        /**
		         * Prevent native submit form
		         *
		         * @returns {Function}
		         * @private
		         */
		        __submitHandler: function() {
		            return function(event) {
		                event.preventDefault();
		                return false;
		            }
		        },
		
		        /**
		         * Search form submit handler
		         *
		         * @returns {Function}
		         * @private
		         */
		        __submitBtnHandler: function() {
		            var $this = this;
		            
		            return function(event) {
		                event.preventDefault();
		
		                /**
		                 * @type {jQuery}
		                 */
		                var form = $($this.settings.searchForm);
		                var nativeForm = document.getElementById($this.settings.searchFormId);
		
		                // Check if at least one input field filled
		                var textInputs = form.find('input[type="text"]');
		                _.each(textInputs, function(node){
		                    if ($(node).val() != '') {
		                        $this.__formFilled = true;
		                        return false;
		                    }
		                });
		
		                var activeGrid = $($this.settings.searchGridInput).val();
		                /* sent data from form2(filters) */
		                $.each ($('#form2 input[data-grid="' + activeGrid + '"]').serializeArray(), function ( i, obj ) {
		                	$('<input type="hidden">').prop(obj).appendTo(nativeForm);
		                } );
		
		                // If no option was chosen show notification or submit form
		                if ($this.__formFilled === false) {
		                    noty({text: 'Please choose at least one option!', type: 'error'});
		                } else {
		                	$($this.settings.searchLocation).prop('disabled', true);
		                    searchParams = form.serialize();
console.log(searchParams);	
//return false;
		    	            $.when(utils.request('post', $this.settings.addSearchParamUrl, searchParams)).then(function(response){
console.log(response);		    
//return false;
		    	                if (response.status == 'OK') {
		    	                	nativeForm.action = response.actionUrl;
				                    nativeForm.submit();
		    	                } else {
		    	                	console.log('Oooops, problems');
		    	                }
		    	            });
		                }
		            }
		        },
		
		
		        /**
		         * Click on category handler. Toggle active statement.
		         *
		         * @returns {Function}
		         * @private
		         */
		        __categoryClickHandler: function() {
		            return function(event) {
		                event.preventDefault();
		
		                var element = $(this);
		                if (element.attr('data-active') == 0) {
		                    element.addClass('active-line').attr('data-active', 1);
		                    $('.'+element.attr('id')).prop('checked', true);
		                }else {
		                    element.removeClass('active-line').attr('data-active', 0);
		                    $('.'+element.attr('id')).prop('checked', false);
		                }
		            }
		        },
		
		        
		        __switchSearchTypeHandler: function() {
		            var $this = this;
		            
		            return function(event) {
		                event.preventDefault();
		
		                $this.__clickedSwitchBtn = $(this);
		                $this.__state = $this.__clickedSwitchBtn.data('type');
		
		                $this.__switchPreset();
		            }
		        },
		        
		        
		        __switchResultTypeHandler: function(typeObj) {
		        	var $this = this;
		
		        	selectedType = $(typeObj).find('a').data('value');
		        	$($this.settings.searchTypeResult).val(selectedType);
		        	$($this.settings.searchTypeResultCurrent).html(selectedType);

		        	if (selectedType == 'Map') {
		            	$(typeObj).find('a').data('value', 'List');
		            	$(typeObj).find('a').text('List');
		        	} else {
		            	$(typeObj).find('a').data('value', 'Map');
		            	$(typeObj).find('a').text('Map');
		        	}
		        },
		        
		        
		        __switchGridHandler: function(gridObj) {
		        	var newGridVal = gridObj.attr('grid-id');
		        	var newGridName = gridObj.attr('grid-name');
		        	var oldGridVal = $(this.settings.searchGrid).attr('grid-id');
		        	var oldGridName = $(this.settings.searchGrid).attr('grid-name');

		        	$(this.settings.searchGrid).attr('grid-id', newGridVal);
		        	$(this.settings.searchGrid).attr('grid-name', newGridName);
		        	$(this.settings.searchGrid).html(newGridName);

		        	gridObj.attr('grid-id', oldGridVal);
		        	gridObj.attr('grid-name', oldGridName);
		        	gridObj.html('<span style="padding-left:15px;">' + oldGridName + '</span>');

		        	$(this.settings.searchGridInput).val(newGridVal);

		        	// switch search grid on filter panel
					var filterSwitchButton = this.settings.filterPanelSwitchGrid;
					$.when(this.__disableDatesByGrid()).then(function() {
						$(filterSwitchButton + '[data-grid="' + newGridVal + '"]').trigger('click');
					});
		        },
		        
		        
		        __disableDatesByGrid: function() {
		        	if ($(this.settings.searchGridInput).val() == 'venue') {
		        		$(this.settings.startDateMain).hide();
		        		$(this.settings.startDateReserve).show();
		        		
		        		$(this.settings.endDateMain).hide();
		        		$(this.settings.endDateReserve).show();
		        	} else {
		        		$(this.settings.startDateMain).show();
		        		$(this.settings.startDateReserve).hide();
		        		
		        		$(this.settings.endDateMain).show();
		        		$(this.settings.endDateReserve).hide();
		        												
		        	}
		        },
		        
		        
		        __switchPreset: function() {
		        	var $this = this;
		        	
		        	if ($this.__state == 'private') {
		                $this.__globalCategories.length = 0;
		                _.each($($this.settings.chooseCatBtn+'[data-active="1"]'), function(node) {
		                    $this.__globalCategories.push(node);
		                }); 
		
		                $this.__switchSearchTypeBtnState();
		                $this.__tryApplyPreset();
		            } else {
		                $this.__switchSearchTypeBtnState();
		                $this.__tryApplyGlobal();
		            }
		        },
		
		        __switchSearchTypeBtnState: function() {
		            var $this = this, btns = $($this.settings.switchStateBtnBlock+' button'), activeBtn = null, inactiveBtn = null;
		            _.each(btns, function(btn) {
		                btn = $(btn);
		                if (btn.hasClass('active')) {
		                    activeBtn = btn;
		                }else {
		                    inactiveBtn = btn;
		                }
		            });
		            btns.removeClass('active');
		            if (!_.isNull(inactiveBtn)) {
		                inactiveBtn.addClass('active');
		            }
		        },
		
		        __tryApplyPreset: function() {
		            var $this = this;
		            
		            $.when(utils.request('get', $this.settings.privatePresetUrl)).then(function(response){
		                if (response.errors) {
		                    var err_msg = 'Some errors occurred! Call to administrator!';
		                    if (_.isUndefined(response.error_msg) || _.isEmpty(response.error_msg) || _.isNull(response.error_msg) || _.isNull(response.error_msg)) {
		                        err_msg = 'Personalize search only for logged users. Please <a href="#" onclick="return false;" class="fb-login-popup">login via Facebook</a>';
		                    }else {
		                        err_msg = response.error_msg;
		                    }
		                    noty({text: err_msg, type: 'warning'});
		                    $this.__switchSearchTypeBtnState();
		                } else {
		
		                    $($this.settings.searchCategoriesTypeBlock + ' input').prop('checked', false);
		                    $($this.settings.searchCategoriesTypeBlock + ' input[value="private"]').prop('checked', true);
		
		                    _.each($this.__globalCategories, function(elem) {
		                        $(elem).trigger('click');
		                    });
		
		                    $this.__privateCategories.length = 0;
		                    _.each(response.member_categories, function(node) {
		                        var elem = $($this.settings.chooseCatBtn+'[data-id="'+node+'"]');
		
		                        $this.__privateCategories.push(elem[0]);
		                        elem.trigger('click');
		                    });
		                    
		                    $this.__setSearchLocationCity(response.member_location_city, 
		                    						  	  response.member_location_country);
		                    
		                    $this.__setSearchLocation(response.member_location_latitudeMin, 
		                    						  response.member_location_longitudeMin, 
		                    						  response.member_location_latitudeMax, 
		                    						  response.member_location_longitudeMax);
		                }
		            });
		        },
		
		        __tryApplyGlobal: function() {
		            var $this = this;
		
		            $($this.settings.searchCategoriesTypeBlock + ' input').prop('checked', false);
		            $($this.settings.searchCategoriesTypeBlock + ' input[value="global"]').prop('checked', true);
		            $($this.settings.searchLocation).val('');
		
		            _.each($($this.settings.chooseCatBtn), function(elem) {
		                if (_.include($this.__privateCategories, elem)) {
		                    $(elem).trigger('click');
		                }
		            });
		
		            _.each($this.__globalCategories, function(elem) {
		                $(elem).trigger('click');
		            });
		        },
		        

		        __setSearchLocation: function(formattedAddress) {
		        	var $this = this;
		            $($this.settings.searchLocationFormattedAddress).val(formattedAddress);
		            $($this.settings.searchLocation).attr('data-location-chosen', true);
		            
		            $this.__locationChosen = true;
		        },
		        
		        __setSearchLocationCity: function(city, country) {
		        	var $this = this;
		            $($this.settings.searchLocation).val(city + ', ' + country);
		        }
		    };

});