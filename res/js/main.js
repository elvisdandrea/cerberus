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
            Html.Post(action,'',function(r){
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

            Html.Post($(this).attr('action'), data.join('&'), function(r) {
                eval(r);
                return false;
            });

        });

    },

    /**
     * Apply quick links
     * Usable in inline action on elements
     *
     * @param action
     */
    quickLink : function(action) {

        Html.Post(action,'',function(r){
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

