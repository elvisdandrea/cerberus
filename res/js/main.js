$(document).on('click','a[href]',function(a){
    a.preventDefault();
    Html.Post($(this).attr('href'),'',function(r){
        eval(r);
        return false;
    });
});