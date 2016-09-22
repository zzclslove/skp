$('.sendemail').click(function(){
    var self = $(this);
    var email = $(this).attr('rel');
    $.post('send_email.php?email=' + $(this).attr('rel'),function(res){
        if(res){
            copy(email);
            self.parents('tr').remove();
        }else{
            alert('请不要重复给该人发送邮件。');
        }
    });
});