/**
 * Created by Slava Basko on 12/20/13 <basko.slava@gmail.com>.
 */

define('frontSearchPanel', 
        ['jquery', 'noti', 'utils', 'bootstrapDatepicker', 'domReady', 'google!maps,3,other_params:sensor=false&key=AIzaSyBmhn9fnmPJSCXhztoLm9TR7Lln3bTpkcA&libraries=places'], 
        function($, noti, utils, bootstrapDatepicker) {

    return {

        /**
         * Settings
         */
        settings: {
            searchForm: '#topSearchForm',
            switchStateBtnBlock: '.switch-btn',
            searchCategoriesTypeBlock: '.searchCategoriesTypeBlock',
            searchSubmitOnList: '#searchSubmitOnList',
            searchSubmitOnMap: '#searchSubmitOnMap',
            chooseCatBtn: '.searchChooseCatBtn',
            categoriesBlock: '.hidden-categories',
            searchLocation: '#searchLocationField',
            searchLocationLatMin: '#searchLocationLatMin',
            searchLocationLngMin: '#searchLocationLngMin',
            searchLocationLatMax: '#searchLocationLatMax',
            searchLocationLngMax: '#searchLocationLngMax',
            startDatePicker: '.startDatePicker',
            endDatePicker: '.endDatePicker',
            privatePresetUrl: '/member/get-private-preset'
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

        /**
         * Initialize clicks. Constructor
         */
        init: function(options) {
            var $this = this;

            // Extend options
            $this.settings = _.extend($this.settings, options);

            // Get search type
            $this.__state = $($this.settings.searchForm).find($this.settings.switchStateBtnBlock).find('.active').data('type');

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

            body.on('click', $this.settings.searchSubmitOnList, $this.__submitBtnHandler());
            body.on('click', $this.settings.searchSubmitOnMap, $this.__submitBtnHandler());

            body.on('click', $this.settings.chooseCatBtn, $this.__categoryClickHandler());

            body.on('click', $this.settings.switchStateBtnBlock+' button', $this.__switchSearchTypeHandler());

            // add address autocomplete
            var list = utils.addressAutocomplete($($this.settings.searchLocation)[0]);

            google.maps.event.addListener(list, 'place_changed', function() {
                var latMax = list.getPlace().geometry.viewport.getNorthEast().lat();
                var lngMax = list.getPlace().geometry.viewport.getNorthEast().lng();

                var latMin = list.getPlace().geometry.viewport.getSouthWest().lat();
                var lngMin = list.getPlace().geometry.viewport.getSouthWest().lng();

                $($this.settings.searchLocationLatMin).val(latMin);
                $($this.settings.searchLocationLngMin).val(lngMin);

                $($this.settings.searchLocationLatMax).val(latMax);
                $($this.settings.searchLocationLngMax).val(lngMax);

                $this.__switchSearchBtnVisible(true);
            });

            // add date picker
            var nowTemp = new Date();
            var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

            var startDate = $($this.settings.startDatePicker).datepicker({
                format: 'yyyy-mm-dd',
                onRender: function() {
                    return now.valueOf();
                }
            }).on('changeDate', function() {
                    startDate.hide();
            }).data('datepicker');

            var endDate = $($this.settings.endDatePicker).datepicker({
                format: 'yyyy-mm-dd',
                onRender: function() {
                    return now.valueOf();
                }
            }).on('changeDate', function() {
                    endDate.hide();
            }).data('datepicker');
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
                var nativeForm = form[0];

                // Check if at least one category chosen
                if (form.find('input[type="checkbox"]:checked').length > 0) {
                    $this.__formFilled = true;
                }

                // Check if at least one input field filled
                var textInputs = form.find('input[type="text"]');
                _.each(textInputs, function(node){
                    if ($(node).val() != '') {
                        $this.__formFilled = true;
                        return false;
                    }
                });

                // If no option was chosen show notification or submit form
                if ($this.__formFilled === false) {
                    noti.createNotification('Please choose at least one option!', 'error');
                }else {
                    if ($(this).val() == 'in_map') {
                        nativeForm.searchType.value = "in_map";
                        nativeForm.action = nativeForm.action+'/map';
                    }else {
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

                if ($this.__state == 'private') {

                    $this.__globalCategories.length = 0;
                    _.each($($this.settings.chooseCatBtn+'[data-active="1"]'), function(node) {
                        $this.__globalCategories.push(node);
                    });

                    $this.__switchSearchTypeBtnState();
                    $this.__tryApplyPreset();
                }else {
                    $this.__switchSearchTypeBtnState();
                    $this.__tryApplyGlobal();
                }
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
                    noti.createNotification(err_msg, 'warning');
                    $this.__switchSearchTypeBtnState();
                }else {

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

                    $this.__switchSearchBtnVisible();
                }
            });
        },

        __tryApplyGlobal: function() {
            var $this = this;

            $($this.settings.searchCategoriesTypeBlock + ' input').prop('checked', false);
            $($this.settings.searchCategoriesTypeBlock + ' input[value="global"]').prop('checked', true);

            _.each($($this.settings.chooseCatBtn), function(elem) {
                if (_.include($this.__privateCategories, elem)) {
                    $(elem).trigger('click');
                }
            });

            _.each($this.__globalCategories, function(elem) {
                $(elem).trigger('click');
            });

            $this.__switchSearchBtnVisible();
        },

        __switchSearchBtnVisible: function(showMapBtn) {
            var $this = this, mapBtn = $($this.settings.searchSubmitOnMap);

            if (($this.__state == 'private' && !mapBtn.is(":visible")) || showMapBtn === true) {
                $($this.settings.searchSubmitOnList).attr('style', 'width: 49%');
                mapBtn.removeAttr('style');
            }else if ($this.__state == 'global' && mapBtn.is(":visible")) {
                $($this.settings.searchSubmitOnList).attr('style', 'width: 100%');
                mapBtn.attr('style', 'display: none;');
            }
        }

    };

});