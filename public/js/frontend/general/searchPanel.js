define('frontSearchPanel', 
        ['jquery', 'noty', 'utils', 'normalDatePicker', 'underscore', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'],
        function($, noty, utils, normalDatePicker) {

    return {

        /**
         * Settings
         */
        settings: {
            searchFormId: 'topSearchForm',
            searchForm: '#topSearchForm',
            searchSubmit: '#searchSubmit',
            searchLocation: '#searchLocationField',
            searchLocationLatMin: '#searchLocationLatMin',
            searchLocationLngMin: '#searchLocationLngMin',
            searchLocationLatMax: '#searchLocationLatMax',
            searchLocationLngMax: '#searchLocationLngMax',
            searchTypeResult: '#searchTypeResult',
            searchTypeResultMenu: '#searchTypeResultMenu',
            searchTypeResultCurrent: '#searchTypeResultCurrent',
            startDatePicker: '#js-selectDateTimeStart',
            startDateField: '#searchPanel-startDate',
            startDateInput: '#searchStartDate',
            endDatePicker: '#js-selectDateTimeEnd',
            endDateField: '#searchPanel-endDate',
            endDateInput: '#searchEndDate',
            isLoggedUser: '#isLogged'
        },

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

            body.on('submit', $this.settings.searchForm, $this.__submitHandler());
            body.on('click', $this.settings.searchSubmit, $this.__submitBtnHandler());
         
            // add address autocomplete
            var list = utils.addressAutocomplete($($this.settings.searchLocation)[0]);

            google.maps.event.addListener(list, 'place_changed', function() {
                var latMax = list.getPlace().geometry.viewport.getNorthEast().lat();
                var lngMax = list.getPlace().geometry.viewport.getNorthEast().lng();

                var latMin = list.getPlace().geometry.viewport.getSouthWest().lat();
                var lngMin = list.getPlace().geometry.viewport.getSouthWest().lng();
                
                $this.__setSearchLocation(latMin, lngMin, latMax, lngMax);
            });

            // add date picker
            var nowTemp = new Date();
            var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

            var startDate = $($this.settings.startDatePicker).datetimepicker({
                format: 'yyyy-mm-dd',
                pickDate: false,
                autoclose: true,
                minView: 2
            });

            var endDate = $($this.settings.endDatePicker).datetimepicker({
                format: 'yyyy-mm-dd',
                pickDate: false,
                autoclose: true,
                minView: 2
            }); 
            
            startDate.on('changeDate', function(e) {
            	selMonth = (0+((e.date.getMonth()+1)).toString()).slice(-2);
            	selDay = (0+(e.date.getDate()).toString()).slice(-2);
            	
            	selectedDate = e.date.getFullYear() + '-' + selMonth + '-'+ selDay;  
            	$($this.settings.startDateField).html(selectedDate);
            	$($this.settings.startDateInput).val(selectedDate);
            	
            });

            endDate.on('changeDate', function(e) {
                selMonth = (0+((e.date.getMonth()+1)).toString()).slice(-2);
                selDay = (0+(e.date.getDate()).toString()).slice(-2);
                
                selectedDate = e.date.getFullYear() + '-' + selMonth + '-'+ selDay;  
                $($this.settings.endDateField).html(selectedDate);
                $($this.settings.endDateInput).val(selectedDate);
                
            }); 
            
            $($this.settings.searchTypeResultMenu + ' li').click(function(e) {
            	$this.__switchResultTypeHandler(this);
            });
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

                if (_.isEmpty($($this.settings.searchLocation).val()) && $this.__locationChosen == false) {
                    noty({text: 'You must chose location from list!', type: 'error'});
                    return false;
                }

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

                /* sent data from form2(filters) */
                $.each ($('#form2 input').serializeArray(), function ( i, obj ) {
                  $('<input type="hidden">').prop(obj).appendTo(nativeForm);
                } );

                // If no option was chosen show notification or submit form
                if ($this.__formFilled === false) {
                    noty({text: 'Please choose at least one option!', type: 'error'});
                } else {
                    if ($($this.settings.searchTypeResult).val().toLowerCase() == 'map') {
                        //nativeForm.searchType.value = "in_map";
                        nativeForm.action = nativeForm.action+'/map';
                    } else {
                        nativeForm.action = nativeForm.action+'/list';
                    }

                    nativeForm.submit();
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
        
        __setSearchLocation: function(latMin, lngMin, latMax, lngMax) {
        	var $this = this;
        	
            $($this.settings.searchLocationLatMin).val(latMin);
            $($this.settings.searchLocationLngMin).val(lngMin);

            $($this.settings.searchLocationLatMax).val(latMax);
            $($this.settings.searchLocationLngMax).val(lngMax);

            $($this.settings.searchLocation).attr('data-location-chosen', true);
            $this.__locationChosen = true;
        },
        
        __setSearchLocationCity: function(city, country) {
        	var $this = this;
            $($this.settings.searchLocation).val(city + ', ' + country);
        }


    };

});