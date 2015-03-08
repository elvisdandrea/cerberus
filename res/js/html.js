/* 
 * HTML PROCESSOR
 * 
 * Author: Elvis D'Andrea
 * E-mail: elvis@vistasoft.com.br
 * 
 */

function Html(){}

/**
 * The HTML Prototype
 *
 * @type {{Add: Function, Replace: Function, Show: Function, Hide: Function, SetLocation: Function, AsyncLoadList: Function, Post: Function}}
 */
Html.prototype = {

    /**
     * Append HTML Content to an Element
     *
     * @param           html        - The HTML Content
     * @param           block       - The element
     * @returns         {boolean}
     * @constructor
     */
    Add : function(html, block) {

        $(block).append(html);
        return false;
    },

    /**
     * Replaces HTML Content of an Element
     * @param           html        - The HTML Content
     * @param           block       - The element
     * @returns         {boolean}
     * @constructor
     */
    Replace : function(html, block) {

        $(block).html(html);
        return false;
    },

    /**
     * Displays a Hidden Element
     *
     * @param           block       - The element
     * @constructor
     */
    Show: function(block) {
        $(block).show();
    },

    /**
     * Hides an element
     *
     * @param           block       - The element
     * @constructor
     */
    Hide: function(block) {

        $(block).hide();
    },

    /**
     * Redirects to a Location
     *
     * @param       location        - Where to go
     * @returns     {boolean}
     * @constructor
     */
    SetLocation: function(location) {

        window.location.href = location;
        return false;
    },

    /**
     * Loads the options of a select input asynchronously
     *
     * The select must have an attribute href, so it will be
     * called. The request response must be the view of the
     * element with the found options
     *
     * @param       id      - The Select ID
     * @constructor
     */
    AsyncLoadList: function(id) {

        $('#'+id).ready(function(){
            $('#'+id + ' select[href]').each(function(){

                var params = '';
                if ($(this).attr('params') != undefined) {
                    params = 'params='+encodeURIComponent($(this).attr('params'));
                }
                var elemId = $(this).attr('id');

                Html.Post($(this).attr('href'), params, function(a){
                    $('#'+id + ' #'+elemId).html(a);
                    $('#'+id + ' #'+elemId).trigger('chosen:updated');
                    return false;
                });

            });
            return false;
        });
    },

    /**
     * Runs an ajax POST
     *
     * @param       url         - The URL string
     * @param       data        - The data json object
     * @param       callback    - The callback function
     * @constructor
     */
    Post: function(url,data,callback) {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: callback,
            async: true,
            error: function(xhr, textStatus, error){
                $('html').html(xhr.responseText);
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });
    },

    /**
     * Runs an ajax GET
     * @param           url         - The URL string
     * @param           callback    - The callback function
     * @constructor
     */
    Get: function(url, callback) {
        $.ajax({
            type: 'GET',
            url: url,
            success: callback,
            async: true,
            error: function(xhr, textStatus, error){
                $('html').html(xhr.responseText);
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });
    }

}

/**
 * The Html Object Instance
 *
 * @type {Html}
 */
var Html = new Html();