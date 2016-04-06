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

GMaps.maps    = {};
GMaps.cluster = [];
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

        GMaps.maps = new google.maps.Map(document.getElementById(elementId), myOptions);
//        if (GMaps.cluster.length > 0) {
//            google.maps.event.addListenerOnce(GMaps.maps, 'idle', function() {
//                alert('here');
//                GMaps.mc = new MarkerClusterer(GMaps.maps, GMaps.cluster);
//            });
//        }
    },

    /**
     * Adds a point in the map
     *
     * @param elementId         - The map element Id
     * @param lat               - Latitude
     * @param lng               - Longitude
     * @param contentString     - The HTML string
     * @param contentTitle      - The title
     * @param events            - The marker events
     */
    addMarker : function(elementId, lat, lng, contentString, contentTitle, events)
    {

        var latlng = new google.maps.LatLng(lat, lng);
        GMaps.marker = new google.maps.Marker({
            position: latlng,
            map: GMaps.maps,
            title:contentTitle,
            center: latlng,
            draggable: true
        });

        if(contentString){
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            google.maps.event.addListener(GMaps.marker, 'click', function() {
                infowindow.open(GMaps.maps, GMaps.marker);
            });
        }

        for (var i in events) {
            google.maps.event.addListener(GMaps.marker, i, events[i]);
        }

    },

    addClusteredMarker : function(elementId, lat, lng, contentString, contentTitle, events) {

        if (GMaps.cluster == undefined) GMaps.cluster = [];

        var latlng = new google.maps.LatLng(lat, lng);
        var marker = new google.maps.Marker({
            position: latlng,
            title:contentTitle,
            center: latlng,
            draggable: true
        });

        if(contentString){
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(GMaps.maps, marker);
            });
        }

        for (var i in events) {
            google.maps.event.addListener(marker, i, events[i]);
        }

        GMaps.cluster.push(marker);
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
            map: GMaps.maps,
            radius: radius,
            center: latlng
        });
    },

    addEvent : function(event, func) {

        GMaps.maps.event.addListener(GMaps.marker, event, func);
    }


}

/**
 * The GMaps object instance
 *
 * @type {GMaps}
 */
var GMaps = new GMaps();