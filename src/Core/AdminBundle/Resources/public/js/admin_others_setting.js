var jsPSetting = {
    init: function() {
        jsPSetting.platformSetting();
        jsCommon.viewLogs('#view_logs_others_setting', '#admin_view_logs_url');
    },
    platformSetting: function(){
        jsCommon.digitPercent();
        $("#frm-others-setting").validate({
            rules : {
                "others_setting[scheduleDeclarationTime]": {
                    required : true,
                    number: true,
                    digits: true
                }
            },
            messages : {
                "others_setting[scheduleDeclarationTime]": {
                    required : msg.msgRequiredField.replace('%s', "Value")
                }
            },
            errorElement : 'span',
            errorPlacement : function(error, element) {
                error.insertAfter(element.parents('div.reminder-text'));
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