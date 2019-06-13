var Form = {
    baseUrl: $("#asset-url").val(),
    data: {},
    available: true,
    initStatus: function () {
        jsCommon.digitPercent();
        $(".update").on('click', function (e) {
            e.preventDefault();
            $(this).parents('form').first().submit();
        });
        this.validateInfor();
        jsCommon.viewLogs('#view_logs_anchor', '#admin_view_logs_url');

        Form.psPercentage();
    },

    psPercentage: function(){
        $('input.icheck').on('ifChecked', function(event){
            successCallback = function (res) {
                window.location.href = $("#url_admin_custom_clearance_fee").val()
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {};
            jsDataService.callAPI($("#url_payment_gms_new_update_active").val(), {area_type:$(this).val(),is_active:1, serve_page:'CAF'}, "POST", successCallback, errorCallback, null, 'json');
        }).on('ifUnchecked', function(event){
            successCallback = function (res) {
                window.location.href = $("#url_admin_custom_clearance_fee").val()
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {};
            jsDataService.callAPI($("#url_payment_gms_new_update_active").val(), {area_type:$(this).val(),is_active:0, serve_page:'CAF'}, "POST", successCallback, errorCallback, null, 'json');
        });
        Form.gmsValidation("#frm-custom-caf1");
        Form.gmsValidation("#frm-custom-caf2");
    },

    gmsValidation: function(element){
        $(element).validate({
            rules : {
                "ps_percentage[platformPercentage]": {
                    required : true,
                    min: 0,
                    max: 100
                },
                "ps_percentage[agentPercentage]": {
                    required : true,
                    min: 0,
                    max: 100
                },
                "ps_percentage[doctorPercentage]": {
                    required : true,
                    min: 0,
                    max: 100
                },
                "ps_percentage[totalPercentage]": {
                    min: 100,
                    max: 100
                },
                "ps_percentage[takeEffectOn]": {
                    required : true
                }
            },
            messages : {
                "ps_percentage[platformPercentage]": {
                    required : msg.msgRequiredField.replace('%s', "GMEDES Sdn Bhd")
                },
                "ps_percentage[agentPercentage]": {
                    required : msg.msgRequiredField.replace('%s', "Agent")
                },
                "ps_percentage[doctorPercentage]": {
                    required : msg.msgRequiredField.replace('%s', "Doctor")
                },
                "ps_percentage[takeEffectOn]": {
                    required : msg.msgRequiredField.replace('%s', "Date")
                }
            },
            errorElement : 'span',
            errorPlacement : function(error, element) {
                error.insertAfter(element.parents('div.input-group'));
            },
            submitHandler : function(form) {
                form.submit();
            },
            errorClass : "error"
        });
        Form.calTotal(element);
    },

    calTotal: function(element){
        var platformPercentage = "input[name='ps_percentage[platformPercentage]']";
        var agentPercentage = "input[name='ps_percentage[agentPercentage]']";
        var doctorPercentage = "input[name='ps_percentage[doctorPercentage]']";
        var totalPercentage = "input[name='ps_percentage[totalPercentage]']";
        $(element)
            .on('blur', platformPercentage, function(){
                var total = 0;
                var self_form = $(this).closest('form');
                total += parseFloat($(this).val());
                total += parseFloat(self_form.find(agentPercentage).val());
                total += parseFloat(self_form.find(doctorPercentage).val());
                self_form.find(totalPercentage).val(jsCommon.currencyFormat(total));
            })
            .on('blur', agentPercentage, function(){
                var total = 0;
                var self_form = $(this).closest('form');
                total += parseFloat($(this).val());
                total += parseFloat(self_form.find(platformPercentage).val());
                total += parseFloat(self_form.find(doctorPercentage).val());
                self_form.find(totalPercentage).val(jsCommon.currencyFormat(total));
            })
            .on('blur', doctorPercentage, function(){
                var total = 0;
                var self_form = $(this).closest('form');
                total += parseFloat($(this).val());
                total += parseFloat(self_form.find(agentPercentage).val());
                total += parseFloat(self_form.find(platformPercentage).val());
                self_form.find(totalPercentage).val(jsCommon.currencyFormat(total));
            });
    },

    validateInfor: function () {
        var validobj = $("#admin-fee-1").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_fee[fee1]':
                        var tag = $e.parents(".input-medium-wrap").first();
                        tag.append(error);
                        break;
                    case 'admin_fee[date1]':
                        var tag = $e.parents(".date-picker").first();
                        error.insertAfter(tag);
                        break;


                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                'admin_fee[fee1]': {
                    number: true
                }

            },
            submitHandler: function (form) {
                Form.submitData(form, 1);
            }
        });

        $("#admin_fee_date1").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });

        var validobj2 = $("#admin-fee-2").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_fee[fee2]':
                        var tag = $e.parents(".input-medium-wrap").first();
                        tag.append(error);
                        break;
                    case 'admin_fee[date2]':
                        var tag = $e.parents(".date-picker").first();
                        error.insertAfter(tag);
                        break;


                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                'admin_fee[fee1]': {
                    decimal: true
                },
                'admin_fee[fee2]': {
                    decimal: true
                }

            },
            submitHandler: function (form) {
                Form.submitData(form, 2);
            }
        });

        $("#admin_fee_date2").on('change', function () {
            if (!$.isEmptyObject(validobj2.submitted)) {
                validobj2.form();
            }
        });
    },
    submitData: function (form, type) {
        if(Form.available == true) {   
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
