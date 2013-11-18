/**
 * Created by slav on 11/14/13.
 */

/**
 * Create autocomplete for input by ID
 * <code>
 *     addressAutoComplete('address');
 * </code>
 *
 * Create autocomplete for input by ID with coordinates where should search address first
 * <code>
 *     addressAutoComplete('address', 46.4845, 30.7326);
 * </code>
 *
 * @param selector
 * @param lat
 * @param lng
 * @returns {null}
 */
function addressAutoComplete (selector, lat, lng) {

    if (selector == '' && selector == undefined) {
        return null;
    }

    var input = document.getElementById(selector), options = {};

    if (lat != '' && lat != undefined && lng != '' && lng != undefined) {
        console.log('init options');
        options = {
            bounds: new google.maps.LatLngBounds(new google.maps.LatLng(lat, lng), new google.maps.LatLng(lat, lng))
        };
    }

    var autocomplete = new google.maps.places.Autocomplete(input,options);

    return autocomplete;
}