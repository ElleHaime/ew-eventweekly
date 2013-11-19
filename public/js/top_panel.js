/**
 * Created by slav on 11/14/13.
 */

var topPanel = {

    settings: {
        searchCityBtn: '.location-city',
        searchCityBlock: '.location-search',
        sendCoordsUrl: ''
    },

    __city: null,

    init: function(options) {
        var $this = this;

        console.log('init top panel');

        // extends options
        $this.settings = $.extend($this.settings, options);

        $($this.settings.searchCityBtn).attr('data-state', 'close');

        // initialize clicks
        $this.__bindClicks();
    },

    __bindClicks: function() {
        var $this = this;
        $('body').on('click', $this.settings.searchCityBtn, function(e){
            e.preventDefault();
            $this.__changeVisibility($this.settings.searchCityBlock);
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
    },

    __changeVisibility: function(elem) {
        var element = $(elem), state = element.is(":visible");

        if (state) {
            element.hide();
        }else {
            element.show();
        }
    },

    __sendCoords: function(lat, lng) {
        var $this = this;
        app.GmapEvents.getEvents(lat, lng, $this.__city);
        $this.__changeVisibility($this.settings.searchCityBlock);
    }

};