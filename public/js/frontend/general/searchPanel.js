/**
 * Created by Slava Basko on 12/20/13 <basko.slava@gmail.com>.
 */

define('frontSearchPanel', ['jquery', 'noti', 'domReady'], function($, noti) {

    var searchPanel = {

        settings: {
            searchForm: '#topSearchForm',
            chooseCatBtn: '.searchChooseCatBtn',
            categoriesBlock: '.hidden-categories'
        },

        __formFilled: false,

        /**
         * Initialize clicks. Constructor
         */
        init: function(options){
            var $this = this;

            $this.settings = _.extend($this.settings, options);

            _.once($this.__bindClicks());
        },

        /**
         * Click binding
         *
         * @private
         */
        __bindClicks: function() {
            var $this = this, body = $('body');

            body.on('submit', $this.settings.searchForm, $this.__submitHandler());

            body.on('click', $this.settings.chooseCatBtn, $this.__categoryClickHandler());
        },

        __submitHandler: function() {
            var $this = this;
            return function(event) {
                if ($(this).find('input[type="checkbox"]:checked').length > 0) {
                    $this.__formFilled = true;
                }

                var textInputs = $(this).find('input[type="text"]');
                _.each(textInputs, function(node, index){
                    console.log($(node).val());
                    if ($(node).val() != '') {
                        $this.__formFilled = true;
                        return false;
                    }
                });

                if ($this.__formFilled === false) {
                    event.preventDefault();
                    noti.createNotification('Please choose at least one option!', 'error');
                }
            }
        },

        __categoryClickHandler: function() {
            var $this = this;
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
        }

    };

    return searchPanel;

});