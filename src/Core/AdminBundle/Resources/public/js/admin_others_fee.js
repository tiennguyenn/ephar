var jsPSetting = {
    init: function() {
        jsPSetting.platformSetting();
        jsCommon.viewLogs('#view_logs_others_fee', '#admin_view_logs_url');
    },
    platformSetting: function(){
        jsCommon.digitPercent();
        $("#frm-others-fee").validate({
            rules : {
                "others_fee[feeLocal]": {
                    required : true,
                    number: true
                },
                "others_fee[feeIndo]": {
                    required : true,
                    number: true
                },
                "others_fee[feeEastMalay]": {
                    required : true,
                    number: true
                },
                "others_fee[feeWestMalay]": {
                    required : true,
                    number: true
                },
                "others_fee[feeInternational]": {
                    required : true,
                    number: true
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

        $("#frm-others-fee-2").validate({
            rules : {
                "others_fee[feeLocal]": {
                    required : true,
                    number: true
                },
                "others_fee[feeIndo]": {
                    required : true,
                    number: true
                },
                "others_fee[feeEastMalay]": {
                    required : true,
                    number: true
                },
                "others_fee[feeWestMalay]": {
                    required : true,
                    number: true
                },
                "others_fee[feeInternational]": {
                    required : true,
                    number: true
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
        $("#frm-others-fee-3").validate({
            rules : {
                "others_fee[feeLocal]": {
                    required : true,
                    number: true
                },
                "others_fee[feeIndo]": {
                    required : true,
                    number: true
                },
                "others_fee[feeEastMalay]": {
                    required : true,
                    number: true
                },
                "others_fee[feeWestMalay]": {
                    required : true,
                    number: true
                },
                "others_fee[feeInternational]": {
                    required : true,
                    number: true
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
};

$(document).ready(function() {
    jsPSetting.init();
});