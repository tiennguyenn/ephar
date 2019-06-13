var Form = {
    baseUrl: $("#asset-url").val(),
    data: {},
    available: true,
    initStatus: function () {
        jsCommon.digitPercent();
        $(".btn-sm").on('click', function (e) {
            if ($(this).attr('id') != 'print_log_anchor'){
                e.preventDefault();
            }
            $(this).parents('form').first().submit();
        });
        this.validateInfor('form-1');
        this.validateInfor('form-2');
        this.validateInfor('form-3');
        this.validateInfor('form-4');
        this.validateInfor('form-5');
        this.validateInfor('form-6');
        this.validateInfor('form-7');
        this.validateInfor('form-8');
        this.validateInfor('form-9');
        this.validateInfor('form-10');
        this.validateInfor('form-11');
        jsCommon.viewLogs('#view_logs_anchor', '#admin_view_logs_url');
        
    },    
    validateInfor: function (id) {
        
        var    fee = {decimal: true};
      
        $("#"+id).validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_fee[fee]':
                        var tag = $e.parents(".input-medium-wrap").first().find('.current-data').first();                   
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_fee[date]':
                        var tag = $e.parents(".date-picker").first();                       
                        error.insertAfter(tag);
                        break;
                    

                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                'admin_fee[fee]': fee
               
            },
            submitHandler: function (form) {
                Form.submitData(form, 3);
            }
        });
        
      
    },
    submitData: function(form,type) {
        if(Form.available == true)
        {   
            document.body.style.cursor = 'wait';
            Form.available = false;
        } else {
            this.message("Can't execute this action");
            return;
        }
        var dependUrl = $("#ajax-url").val();
        var form_data = new FormData();
        
        form_data.append('type', type);
        $.each($(form).serializeArray(), function (index, value) {
            form_data.append(value.name, value.value);
        });
      
        $.ajax({
            type: "POST",
            url: dependUrl,
            data: form_data,
            contentType: false,
            processData: false,
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                if(data.success) {                    
                    var tag = ($(form).find('.form-group').first().find('.current-data').first());
             
                    tag.html('');
                    tag.html('<span id="" class="">Current value is '+data.value+'</span>');
                    Form.message("Update success", true);
                } else {
                    Form.message("Update fail");
                }
                document.body.style.cursor = 'default';
                Form.available = true;
                $("html, body").animate({ scrollTop: 0 }, "slow");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                document.body.style.cursor = 'default';
                Form.available = true;
                Form.message("Update fail");
                return false;
            }
        });
        
    },
    
    message(msg,type){
        var cl = 'alert-danger';
        if(type === true) {
            cl = 'alert-success';
        }        
        var html = '<div class="alert '+ cl +'"><button class="close" data-dismiss="alert"></button>'+ msg+'</div>';
        $("#notification").html(html);
    }
};


$(document).ready(function () {
  
    $.validator.addMethod('decimal', function(value, element) {
        return this.optional(element) || /^(\d+(?:[\.]\d{1,2})?)$/.test(value);
    }, "Please enter a correct number, format xxxx.xx");
    Form.initStatus();
});
