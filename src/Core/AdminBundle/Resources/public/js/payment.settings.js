var jsPSetting = {
    init: function() {
        jsPSetting.platformSetting();
        jsPSetting.psPercentage();
        jsCommon.viewLogs('#view_logs_anchor', '#admin_view_logs_url');
    },
    platformSetting: function(){
        jsCommon.digitPercent();
        $("#frm-product-margin").validate({
            rules : {
                "platform_setting[local]": {
                    required : true,
                    min: 0,
                    max: 100
                },
                "platform_setting[overseas]": {
                    required : true,
                    min: 0,
                    max: 100
                }
            },
            messages : {
                "platform_setting[local]": {
                    required : msg.msgRequiredField.replace('%s', "Local Patient")
                },
                "platform_setting[overseas]": {
                    required : msg.msgRequiredField.replace('%s', "Overseas Patient")
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
        $("#frm-gst-rate").validate({
            rules : {
                "platform_setting[newGstRate]": {
                    required : true,
                    min: 0,
                    max: 100
                },
                "platform_setting[gstRateAffectDate]": {
                    required : true
                }
            },
            messages : {
                "platform_setting[newGstRate]": {
                    required : msg.msgRequiredField.replace('%s', "Gst Rate")
                },
                "platform_setting[gstRateAffectDate]": {
                    required : msg.msgRequiredField.replace('%s', "Date")
                },
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

        $("#gmeds_gst_setting_frm").validate({
            rules : {
                "platform_setting[gstNo]": {
                    required : true
                },
                "platform_setting[gstAffectDate]": {
                    required : true
                }
            },
            messages : {
                "platform_setting[gstNo]": {
                    required : msg.msgRequiredField.replace('%s', "Gst No")
                },
                "platform_setting[gstAffectDate]": {
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
        $("#frm-schedule").validate({
            rules : {
                "platform_setting[doctorStatementDate]": {
                    required : true,
                    min: 1,
                    max: 29,
                    number: true
                },
                "platform_setting[agentStatementDate]": {
                    required : true,
                    min: 1,
                    max: 29,
                    number: true
                }
            },
            messages : {
                "platform_setting[doctorStatementDate]": {
                    required : msg.msgRequiredField.replace('%s', "Doctor Statement")
                },
                "platform_setting[agentStatementDate]": {
                    required : msg.msgRequiredField.replace('%s', "Agent Statement")
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
        $("#itForm").validate({
            rules : {
                "it[freightCostDate]": {
                    required : true
                },
                "it[insuranceVariableDate]": {
                    required : true
                },
                "it[bmImportDutyDate]": {
                    required : true
                },
                "it[ppnVatDate]": {
                    required : true
                },
                "it[pphWithTaxIdDate]": {
                    required : true
                },
                "it[pphWithoutTaxIdDate]": {
                    required : true
                }
            },
            messages : {
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
    },
    psPercentage: function(){
        // Platform gross margin share
        if ($("#platform_share_fee").val() === 0 ) {
            var urlLocation = $("#url_payment_gross_margin_share").val();
            var urlAPI = $("#url_payment_gms_update_active").val();
        }
        // Platform global margin share and fee
        else {
            var urlLocation = $("#url_payment_global_margin_share_fee").val();
            var urlAPI = $("#url_payment_gms_new_update_active").val();
        }

        $('input.icheck').on('ifChecked', function(event){
            successCallback = function (res) {
                window.location.href = urlLocation
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {};
            jsDataService.callAPI(urlAPI, {area_type:$(this).val(),is_active:1}, "POST", successCallback, errorCallback, null, 'json');
        }).on('ifUnchecked', function(event){
            successCallback = function (res) {
                window.location.href = urlLocation
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {};
            jsDataService.callAPI(urlAPI, {area_type:$(this).val(),is_active:0}, "POST", successCallback, errorCallback, null, 'json');
        });
        jsPSetting.gmsValidation("#frm-medicine1");
        jsPSetting.gmsValidation("#frm-medicine2");
        jsPSetting.gmsValidation("#frm-service1");
        jsPSetting.gmsValidation("#frm-service2");
        // jsPSetting.gmsValidation("#frm-custom-caf1");
        // jsPSetting.gmsValidation("#frm-custom-caf2");
        jsPSetting.gmsValidation("#frm-live-consult1");
        jsPSetting.gmsValidation("#frm-live-consult2");
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
        jsPSetting.calTotal(element);
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
    }
};
$(document).ready(function() {
    jsPSetting.init();
});

var Form = {
    data: {},
    available: true,
    initStatus: function () {

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

        this.updateFixTotalValue();
    },
    updateFixTotalValue(){


        $("#form-1 #rd_fee_agent_local").val(jsCommon.currencyFormat(100 - $('#form-1 #admin_agent_fee_value').val()));
        $("#form-2 #rd_fee_agent_oversea").val(jsCommon.currencyFormat(100 - $('#form-2 #admin_agent_fee_value').val()));
        $('#form-1 #admin_agent_fee_value').on('change', function () {
            if($(this).val().length ==  0 || $(this).val() > 100 ) {
                $(this).val(jsCommon.currencyFormat('0'));
            }
            $("#form-1 #rd_fee_agent_local").val(jsCommon.currencyFormat(100 - $(this).val()));
        });
        $('#form-1 #rd_fee_agent_local').on('change', function () {
            if($(this).val().length ==  0 || $(this).val() > 100 ) {
                $(this).val(jsCommon.currencyFormat('0'));
            }
            $("#form-1 #admin_agent_fee_value").val(jsCommon.currencyFormat(100 - $(this).val()));
        });
        $('#form-2 #admin_agent_fee_value').on('change', function () {
            if($(this).val().length ==  0 || $(this).val() > 100 ) {
                $(this).val(jsCommon.currencyFormat('0'));
            }
            $("#form-2 #rd_fee_agent_oversea").val(jsCommon.currencyFormat(100 - $(this).val()));
        });
        $('#form-2 #rd_fee_agent_oversea').on('change', function () {
            if($(this).val().length ==  0 || $(this).val() > 100 ) {
                $(this).val(jsCommon.currencyFormat('0'));
            }
            $("#form-2 #admin_agent_fee_value").val(jsCommon.currencyFormat(100 - $(this).val()));
        });
    },
    validateInfor: function (id) {

        var    fee = {decimal: true};

        $("#"+id).validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_agent_fee[value]':
                        var tag = $e.parents(".input-medium-wrap").first().find('.current-data').first();
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_agent_fee[date]':
                        var tag = $e.parents(".date-picker").first();
                        error.insertAfter(tag);
                        break;


                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                'admin_fee[admin_agent_fee]': fee

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
                    if(data.value > 0){
                        tag.html('<span id="" class="">Current value is '+data.value+'</span>');
                    }

                    Form.message("Update success", true);
                } else {
                    Form.message("Update fail");
                    var tag = ($(form).find('.form-group').first().find('.current-data').first());

                    tag.html('');
                    tag.html('<span id="" class="error">Update fail:  '+ data.message +'</span>');
                }
                document.body.style.cursor = 'default';
                Form.available = true;
                // $("html, body").animate({ scrollTop: 0 }, "slow");
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

var minForm = {
    data: {},
    available: true,
    init: function () {
        this.validateData($('#frm-primary-min-margin'));
        this.validateData($('#frm-secondary-min-margin'));
    },
    validateData: function (form) {
        var fee = {decimal: true};

        form.validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            rules: {
                'others_fee[feeLocal]'        : fee,
                'others_fee[feeEastMalay]'    : fee,
                'others_fee[feeWestMalay]'    : fee,
                'others_fee[feeIndo]'         : fee,
                'others_fee[feeInternational]': fee
            },
            submitHandler: function (form) {
                minForm.ajaxSubmit(form);
                return false;
            }
        });
    },
    ajaxSubmit: function(form) {
        if(Form.available == true)
        {
            document.body.style.cursor = 'wait';
            Form.available = false;
        } else {
            this.message("Can't execute this action");
            return;
        }
        var url = $(form).attr('action');
        var form_data = new FormData();
        $.each($(form).serializeArray(), function (index, value) {
            form_data.append(value.name, value.value);
        });

        $.ajax({
            type: "POST",
            url: url,
            data: form_data,
            contentType: false,
            processData: false,
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                if(data.success) {
                    if (data.message) {
                        Form.message(data.message, true);
                    } else {
                    Form.message("Update success", true);
                    }
                } else {
                    Form.message("Update fail");
                }
                document.body.style.cursor = 'default';
                Form.available = true;
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
    minForm.init();
});
