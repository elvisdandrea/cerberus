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
     * Removes an element
     *
     * @param           block       - The element
     * @constructor
     */
    Remove : function(block) {
        $(block).remove();
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
     * Sets an element value
     *
     * @param           block       - The element
     * @param           value       - The value
     * @constructor
     */
    SetValue : function(block, value) {

        $(block).val(value);
    },

    /**
     * Removes an Element Class
     *
     * @param           block       - The element
     * @param           className   - The class name
     * @constructor
     */
    RemoveClass : function (block, className) {

        $(block).removeClass(className);
    },

    /**
     * Adds a Class to an Element
     *
     * @param           block       - The element
     * @param           className   - The class name
     * @constructor
     */
    AddClass : function (block, className) {

        $(block).addClass(className);
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
     * Ajax Redirection
     *
     * @param   url             - The destination URL
     * @param   changeUrl       - Change the URL on browser
     * @constructor
     */
    Redirect: function(url, changeUrl) {

        Main.quickLink(url, changeUrl);
    },

    /**
     * Scrolls page to an element
     *
     * @param       element     - The element
     * @param       speed       - The speed to reach the element
     * @constructor
     */
    ScrollToElement : function (element, speed) {
        $('html, body').animate(
            {
                scrollTop: $(element).offset().top
            }, speed);
    },

    /**
     * Appends a row to a Table
     *
     * @param           html        - The tr html
     * @param           block       - The table Id/attribute
     * @constructor
     */
    AppendToTable : function (html, block) {

        $(block + ' tbody').append(html);
    },

    /**
     * Loads the options of a select input asynchronously
     *
     * The select must have an attribute href, so it will be
     * called. The request response must be the view of the
     * element with the found options
     *
     * @param       id          - The Select ID
     * @param       selected    - The selected option value
     * @constructor
     */
    AsyncLoadList: function(id, selected) {

        $('select#' + id + '[href]').each(function(){

            var action = $(this).attr('href');

            if (selected != undefined)
                action += '?selected='+selected;
            Html.Get(action, function(a){
                $('#'+id).html(a);
                return false;
            });

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