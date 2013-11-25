/**
 * Created by slav on 11/14/13.
 */

var topPanel = {

    settings: {
        searchCityBtn: '.locationCity',
        advancedSearchBtn: '.advancedSearchBtn',
        searchCityBlock: '.searchCityBlock',
        advancedSearchBlock: '.advancedSearchBlock',
        sendCoordsUrl: ''
    },

    __city: null,

    init: function(options) {
        var $this = this;

        //console.log('init top panel');

        // extends options
        $this.settings = $.extend($this.settings, options);

        $($this.settings.searchCityBtn).attr('data-state', 'close');

        // initialize clicks
        $this.__bindClicks();
    },

    __bindClicks: function() {
        var $this = this, htmlBody = $('body');
        htmlBody.on('click', $this.settings.searchCityBtn, function(e){
            e.preventDefault();

            $this.__changeVisibility('city');
            $($this.settings.searchCityBlock).find('input').focus();

            var list = addressAutoComplete('topSearchCity');

            app.__GoogleApi.maps.event.addListener(list, 'place_changed', function() {
                var lat = list.getPlace().geometry.location.ob;
                var lng = list.getPlace().geometry.location.pb;

                $this.__city = list.getPlace().vicinity;

                $($this.settings.searchCityBtn).find('span').text($this.__city);

                $this.__sendCoords(lat, lng);
            });
        });

        htmlBody.on('click', $this.settings.advancedSearchBtn, function(e) {
            e.preventDefault();

            $this.__changeVisibility('advanced');
        });
    },

    __changeVisibility: function(type) {
        var $this = this;

        $($this.settings.searchCityBtn).closest('div').removeClass('active-box');
        $($this.settings.advancedSearchBtn).closest('div').removeClass('active-box');

        if (type == 'city' && !$($this.settings.searchCityBlock).is(":visible")) {
            // hide advanced block
            $($this.settings.advancedSearchBlock).hide();

            // show city block
            $($this.settings.searchCityBtn).closest('div').addClass('active-box');
            $($this.settings.searchCityBlock).show();
        }else if (type == 'city' && $($this.settings.searchCityBlock).is(":visible")) {
            $($this.settings.searchCityBtn).closest('div').removeClass('active-box');
            $($this.settings.searchCityBlock).hide();
        }

        if (type == 'advanced' && !$($this.settings.advancedSearchBlock).is(":visible")) {
            // hide search block
            $($this.settings.searchCityBlock).hide();

            // show advanced block
            $($this.settings.advancedSearchBtn).closest('div').addClass('active-box');
            $($this.settings.advancedSearchBlock).show();
        }else if (type == 'advanced' && $($this.settings.advancedSearchBlock).is(":visible")) {
            $($this.settings.advancedSearchBtn).closest('div').removeClass('active-box');
            $($this.settings.advancedSearchBlock).hide();
        }
    },

    __sendCoords: function(lat, lng) {
        var $this = this;
        app.GmapEvents.getEvents(lat, lng, $this.__city);
        $this.__changeVisibility('city');
    }

};