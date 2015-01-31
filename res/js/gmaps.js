/**
 * GMaps Javascript
 *
 * Pre-defined functions to easily
 * manipulate GMaps API
 *
 * Unfinished - Just Started!
 *
 * @param address
 */

/**
 * GMaps Constructor
 *
 * @constructor
 */
function GMaps() {}

/**
 * GMaps Prototype
 *
 * @type {{FindAddresLatLNG: Function}}
 */
GMaps.prototype = {

    FindAddresLatLNG : function(address) {
        geocoder.geocode( { 'address': address}, function(results, status) {

            if (status != google.maps.GeocoderStatus.OK) return false;

            map.setCenter(results[0].geometry.location);
            marker.setPosition(results[0].geometry.location);

            return {
                lat : results[0].geometry.location.lat(),
                lng : results[0].geometry.location.lng()
            };
        });
    }


    //TODO: Functions to render a map on an element with options


}

/**
 * The GMaps object instance
 *
 * @type {GMaps}
 */
var GMaps = new GMaps();