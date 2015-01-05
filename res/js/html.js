/* 
 * HTML PROCESSOR
 * 
 * Author: Elvis D'Andrea
 * E-mail: elvis@vistasoft.com.br
 * 
 */

function Html(){}

Html.prototype = {
    
    Add : function(html, block) {
        $(block).append(html);
        return false;
    },
    
    Replace : function(html, block) {
        $(block).html(html);
        return false;
    },

    Show: function(block) {
        $(block).show();
    },

    Hide: function(block) {
        $(block).hide();
    },

    SetLocation: function(location) {
        window.location.href = location;
        return false;
    },

    Actions: function(id){
        if (id == undefined) id = 'html';
        $(id + ' a[href]:not(.boundAction), ' + id + ' button[href]:not(.boundAction)').click(function(a){
            $(this).addClass('boundAction');
            if ($(this).attr('href') == '') {
                return false;
            }
            a.preventDefault();
            var params = '';
            if ($(this).attr('params') != undefined) {
                params = 'params='+encodeURIComponent($(this).attr('params'));
            }
            Html.Post($(this).attr('href'), params, function(e){
                eval(e);
                return false;
            });
        });
    },

    loadSelects: function(id) {
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

    Post: function(url,data,callback) {
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: callback,
            async: true,
            error: function(xhr, textStatus, error){
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });
    }

}

var Html = new Html();