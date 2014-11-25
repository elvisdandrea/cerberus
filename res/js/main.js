/**
 * Main functions JS
 *
 * @author      Elvis D'Andrea
 * @email       elvis@gravi.com.br
 */

$(document).on('click','a[href]', function(a){

    if ( $(this).attr('href').indexOf('#') === 0 ) { return; }

    a.preventDefault();
    Html.Post($(this).attr('href'),'',function(r){
        eval(r);
        return false;
    });
});