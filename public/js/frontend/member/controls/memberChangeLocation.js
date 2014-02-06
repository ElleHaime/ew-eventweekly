/**
 * Created by Slava Basko on 12/19/13 <basko.slava@gmail.com>.
 */

define('frontMemberChangeLocation', ['jquery', 'utils', 'domReady'], function($, utils){

    var memberChangeLocation = {

        settings: {
            fieldId: '#uLocation',
            form: '#mLocationForm'
        },

        init: function(options) {
            var $this = this;

            $this.settings = $.extend($this.settings, options);

            $('body').on('submit', $this.settings.form, function(e){
                e.preventDefault();
            });

            $this.__initializeAutoComplete();
        },

        __initializeAutoComplete: function() {
            var $this = this;
            var addr = utils.addressAutocomplete($($this.settings.fieldId)[0]);
            $this.__initializeListener(addr);
        },

        __initializeListener: function(addr) {
            google.maps.event.addListener(addr, 'place_changed', function() {
                var place = addr.getPlace();
                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();
                var city = place.vicinity;
                var country = $('.country-name', '<div>'+place.adr_address+'</div>').text();

                var data = {
                    lat: lat,
                    lng: lng,
                    city: city,
                    country: country
                };

                $.post('/member/update-location', data, function(response){
                    if (response.status == true) {
                        console.log('all is OK');

                        // change city text in header
                        $('.location-place_country span').text(city);

                        $('#mLocation').text(city);
                        $('#uLocation').val('');

                        $('#lConflict').remove();
                        // write last map positions in to cookie
                        $.cookie('lastLat', lat, {expires: 1, path: '/'});
                        $.cookie('lastLng', lng, {expires: 1, path: '/'});
                    }
                    console.log(response);
                }, 'json');
            });
        }

    };

    return memberChangeLocation;

});