define('frontMemberChangeLocation', ['jquery', 'utils', 'noty', 'domReady'], function($, utils, noty){

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
                var formattedAddress = {};
                
                $.each(place.address_components, function(index, val) {
                	formattedAddress[val.types[0]] = val.long_name;
                });
                
                formattedAddress['lat'] = place.geometry.location.lat();
                formattedAddress['lng'] = place.geometry.location.lng();
                formattedAddress['place_id'] = place.place_id;

                $.post('/member/update-location', formattedAddress, function(response){
                    if (response.status == true) {
                        // change city text in header
                        //$('.location-place_country span').text(city);

                        $('#mLocation').text(formattedAddress['locality'] + ',' + formattedAddress['country'] );
                        $('#uLocation').text(formattedAddress['locality'] + ',' + formattedAddress['country'] );
                        //$('#mLocation').text(city);
                        //$('#uLocation').val('');

                        $('#lConflict').remove();
                        
                        noty({text: 'Your default location was changed', type: 'success'});
                        // write last map positions in to cookie
                        //$.cookie('lastLat', lat, {expires: 1, path: '/'});
                        //$.cookie('lastLng', lng, {expires: 1, path: '/'});
                    }
                    //console.log(response);
                }, 'json');
            });
        }

    };

    return memberChangeLocation;

});