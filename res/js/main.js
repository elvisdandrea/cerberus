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

            if ($(this).attr('href').indexOf('#') === 0) return false;

            if (
                $(this).attr('href').indexOf('http://')  === 0 ||
                    $(this).attr('href').indexOf('https://') === 0 ) {

                window.open($(this).attr('href'), '_blank');
                return false;
            }

            var action    = $(this).attr('href');
            var changeUrl = $(this).attr('changeurl') != undefined;
            e.preventDefault();
            $('#loading').show();
            Html.Get(action, function(r){
                eval(r);
                if (changeUrl) window.history.pushState(undefined, '', action);
                return false;
            });
        });

        $(document).on('click','[data-type="selitem"]', function(e){
            e.preventDefault();
            var target = $(this).attr('data-target');
            if (target == undefined) return false;
            $('#'+target).attr('data-id', $(this).attr('data-id')).val($(this).attr('data-value'));
            return false;
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
            $(this).find('input[type="hidden"][name],input[type][name]:not("[type=password]"):not("[type=radio]"),select[name],textarea[name]').each(function(e){
                var value = $(this).val();
                if ($(this).attr('data-id') != undefined) value = $(this).attr('data-id');
                if ($(this).attr('format') == 'currency') value = $(this).maskMoney('unmasked')[0];
                data.push($(this).attr('name')+'='+encodeURIComponent(value));
            });
            $(this).find('input[type="password"]').each(function(){
                data.push($(this).attr('name')+'='+md5($(this).val()));
            });

            $(this).find('input[type="radio"]:checked').each(function(){
                data.push($(this).attr('name')+'='+$(this).val());
            });

            $(this).find('img[type="upload"]').each(function(){
                data.push($(this).attr('name')+'='+encodeURIComponent($(this).attr('src')));
            });

            var method = 'post';
            if ($(this).attr('method') != undefined) {
                method = $(this).attr('method');
            }

            var changeUrl = $(this).attr('changeurl') != undefined;
            var url = $(this).attr('action');
            if (method == 'post') {
                Html.Post($(this).attr('action'), data.join('&'), function(r) {
                    eval(r);
                    if (changeUrl) window.history.pushState(undefined, '', url);
                    return false;
                });
            } else if (method == 'get') {
                url = url + '?' + data.join('&');
                Html.Get(url, function(r){
                    eval(r);
                    if (changeUrl) window.history.pushState(undefined, '', url);
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

                //Resize Image

                tempImg.onload = function() {

                    var MAX_WIDTH = 640;
                    var MAX_HEIGHT = 480;
                    var tempW = tempImg.width;
                    var tempH = tempImg.height;
                    if (tempW > tempH) {
                        if (tempW > MAX_WIDTH) {
                            tempH *= MAX_WIDTH / tempW;
                            tempW = MAX_WIDTH;
                        }
                        if (tempH > MAX_HEIGHT) {
                            tempW *= MAX_HEIGHT / tempH;
                            tempH = MAX_HEIGHT;
                        }
                    } else {
                        if (tempH > MAX_HEIGHT) {
                            tempW *= MAX_HEIGHT / tempH;
                            tempH = MAX_HEIGHT;
                        }
                        if (tempW > MAX_WIDTH) {
                            tempH *= MAX_WIDTH / tempW;
                            tempW = MAX_WIDTH;
                        }
                    }
                    var canvas = document.createElement('canvas');
                    canvas.width = tempW;
                    canvas.height = tempH;
                    var ctx = canvas.getContext("2d");
                    ctx.drawImage(this, 0, 0, tempW, tempH);
                    var dataURL = canvas.toDataURL("image/jpeg");

                    $('#' + imgId).attr('src', dataURL);
                    $('#loading').hide();

                }


            }

            r.readAsDataURL(f);

        }
    },

    /**
     * Element interactions
     *
     * Useful for loading widgets or handling
     * data of the elements
     *
     */
    interactions : function() {

        var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );

        if (!iOS)
            window.onpopstate = function(e) {
                Html.Get(e.target.window.location.href, function(r) {
                    eval(r);
                    return false;
                });
            }

    },

    /**
     * Apply quick links
     * Usable in inline action on elements
     *
     * @param action
     */
    quickLink : function(action, changeUrl,event, avoidElement) {

        if (event != undefined && avoidElement != undefined) {
            var target = event.target;
            if ($(target).is(avoidElement)) return false;
        }

        Html.Get(action, function(r){
            eval(r);
            if (changeUrl) window.history.pushState(undefined, '', action);
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
Main.interactions();