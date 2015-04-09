/**
 * Main functions JS
 *
 * Those actions that run overall
 *
 *
 * @author      Elvis D'Andrea
 * @email       elvis.vista@gmail.com
 */

/**
 * The Main Handler Constructor
 *
 * @constructor
 */
function Main() {}

/**
 * The Main Handler Prototype
 *
 * @type {{linkActions: Function, formActions: Function, quickLink: Function}}
 */
Main.prototype = {

    /**
     * Actions that handle simple links
     *
     * This action applies overall on the document
     * and will be applied on Dynamic HTML as well
     */
    linkActions : function() {

        $(document).on('click','a[href]', function(e){

            if ( $(this).attr('href').indexOf('#') === 0 || $(this).attr('href').indexOf('http://') === 0 ) return false;

            var action = $(this).attr('href');
            e.preventDefault();
            Html.Get(action, function(r){
                eval(r);
                window.history.replaceState(undefined, '', action);
                return false;
            });
        });
    },

    /**
     * A generic ajax form handler
     *
     * - The framework controller can easily handle forms
     * - Any password field will be md5 hashed
     */
    formActions : function() {

        $(document).on('submit','form[action]', function(e) {

            e.preventDefault();
            if ($(this).attr('action') == undefined) return false;

            data = [];
            $(this).find('input[type="hidden"][name],input[type][name]:not("[type=password]"),select[name],textarea[name]').each(function(){
                data.push($(this).attr('name')+'='+encodeURIComponent($(this).val()));
            });
            $(this).find('input[type="password"]').each(function(){
                data.push($(this).attr('name')+'='+md5($(this).val()));
            });

            $(this).find('img[type="upload"]').each(function(){
                data.push($(this).attr('name')+'='+encodeURIComponent($(this).attr('src')));
            });

            var method = 'post';
            if ($(this).attr('method') != undefined) {
                method = $(this).attr('method');
            }

            if (method == 'post') {
                Html.Post($(this).attr('action'), data.join('&'), function(r) {
                    eval(r);
                    return false;
                });
            } else if (method == 'get') {
                var url = $(this).attr('action') + '?' + data.join('&');
                $('#loading').show();
                Html.Get(url, function(r){
                    eval(r);
                    $('#loading').hide();
                    return false;
                });
            }

        });

    },

    /**
     * Creates a bas64 loader function for an input type="file"
     *
     * @param   inputId     - The input type="file" Id
     * @param   imgId       - The Id of an element img to set the src as base64
     */
    imageAction : function(inputId, imgId) {

        $('#loading').show();
        document.getElementById(inputId).addEventListener('change', readImage, false);

        function readImage(evt){

            var f = evt.target.files[0];
            if (!f) return false;

            var r = new FileReader();

            r.onloadend = function(e) {
                var tempImg = new Image();
                tempImg.src = e.target.result;
                $('#' + imgId).attr('src', tempImg.src);
                $('#loading').hide();
            }

            r.readAsDataURL(f);

        }
    },

    /**
     * Apply quick links
     * Usable in inline action on elements
     *
     * @param action
     */
    quickLink : function(action) {

        Html.Get(action, function(r){
            eval(r);
            window.history.replaceState(undefined, '', action);
            return false;
        });
    }

}

/**
 * The Main Handler instance object
 * @type {Main}
 */
var Main = new Main();

/**
 * Default Actions call
 */
Main.linkActions();
Main.formActions();
//Main.imageAction();   // Uncoment for activating base64 image loader

