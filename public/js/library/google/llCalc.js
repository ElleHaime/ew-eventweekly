define('llCalc', ['jquery', 'underscore', 'domReady'], function($, _){

    return {

        distance: function(LatLng1, LatLng2) {
            var lat1 = LatLng1.lat();
            var lat2 = LatLng2.lat();
            var lon1 = LatLng1.lng();
            var lon2 = LatLng2.lng();

            var R = 6378.137; // earth radius - KM
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            var d = R * c;
            return d.toFixed(3); // km (make d * 1000 its will be in meters)
        }

    };

});