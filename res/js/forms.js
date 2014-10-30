function Forms(){}

Forms.prototype = {
    
    Actions: function(id){

        $('form:not(.boundAction)').unbind('submit').submit(function(l){
            $(this).addClass('boundAction');
            l.preventDefault();

            if ($(this).attr('action') != undefined) {

                //var form = $(this).serialize();
                
                Forms.Data = [];
                //Forms.Data.push('formId='+$(this).attr('id'));
                $(this).find('input[type="hidden"][name],input[type="text"][name],select[name],textarea[name]').each(function(e){
                    Forms.Data.push($(this).attr('name')+'='+encodeURIComponent($(this).val()));
                });
                $(this).find('input[type="password"]').each(function(e){
                    Forms.Data.push($(this).attr('name')+'='+md5($(this).val()));
                });
                
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: Forms.Data.join('&'),
                    async: true,
                    success: function(a){
                        eval(a);
                        return false;
                    },
                    error: function(xhr, textStatus, error){
                        console.log(xhr.statusText);
                        console.log(textStatus);
                        console.log(error);
                    }
                });
            }
            //alert($(this).serialize());
            //SetFunctions(id);
            return false;
        });

        $('button[type="form-submit"]:not(.boundAction)').unbind('click').click(function(l){
            $(this).addClass('boundAction');
            l.preventDefault();
            if ($(this).attr('form') != undefined) {
                $('#' + $(this).attr('form')).submit();
            }
            SetFunctions(id);
            return false;
        });
         
    }
}

var Forms = new Forms();


