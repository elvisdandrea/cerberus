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
GMaps.maps = {};
/**
 * GMaps Prototype
 *
 * @type {{FindAddresLatLNG: FindAddresLatLNG, init: init, add_point: add_point, add_circle: add_circle}}
 */
GMaps.prototype = {

    /**
     * Finds Lat and Lng of an specific address
     *
     * @param           address - Full address
     * @constructor
     */
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
    },


    /**
     * Initializes Google Map inside an element
     *
     * @param elementId     - The element Id
     * @param lat           - Start Latitude
     * @param lng           - Start Longitude
     * @param zoom          - Start Zoom
     */
    init : function(elementId, lat, lng, zoom) {
        var latlng = new google.maps.LatLng(lat, lng);
        var myOptions = {
            zoom: zoom,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        GMaps.maps[elementId] = new google.maps.Map(document.getElementById(elementId), myOptions);
    },

    /**
     * Adds a point in the map
     *
     * @param elementId         - The map element Id
     * @param lat               - Latitude
     * @param lng               - Longitude
     * @param contentString     - The HTML string
     * @param contentTitle      - The title
     */
    addMarker : function(elementId, lat, lng, contentString, contentTitle)
    {

        var latlng = new google.maps.LatLng(lat, lng);
        var marker = new google.maps.Marker({
            position: latlng,
            map: GMaps.maps[elementId],
            title:contentTitle
        });

        if(contentString){
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(GMaps.map,marker);
            });
        }


    },

    /**
     * Renders a circle around a Latitude and Longitude
     *
     * @param elementId - The map element Id
     * @param lat       - Latitude
     * @param lng       - Longitude
     * @param radius    - The circle radius
     */
    addCircle : function(elementId, lat, lng, radius){
        if (radius == undefined) radius = 800;
        var latlng = new google.maps.LatLng(lat, lng);
        var circle = new google.maps.Circle({
            map: GMaps.maps[elementId],
            radius: radius,
            center: latlng
        });
    }


}

/**
 * The GMaps object instance
 *
 * @type {GMaps}
 */
var GMaps = new GMaps();