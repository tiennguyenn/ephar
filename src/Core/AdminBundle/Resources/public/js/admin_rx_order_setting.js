var rxOrderNotificationSetting = {
    init: function () {

        $('#rx-notification-setting-submit').click(function () {
            rxOrderNotificationSetting.submit();
        });

        $("#logs_notification_setting").bind("click", rxOrderNotificationSetting.viewLogsNotification);
    },
    viewLogsNotification: function () {
        var url = $("#logs_notification_url").val();
        $("#title").text('Rx Order Notification Settings Log');
        rxOrderNotificationSetting.viewLogs(url);
    },
    viewLogs: function (url) {
        $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
        $.post(url, {}, function (response) {
            $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
            $("#list-logs").html(response);
            $('#modal-view-logs').modal();
        });
    },
    submit: function () {
        var postData = {
            delay_time: $('#delay_time').val(),
            delay_time_unit: $('#delay_time_unit').val(),
            email_subject: $('#email_subject').val(),
            email_message: $('#email_message').val(),
            sms_message: $('#sms_message').val(),
        };

        if (postData.email_subject == "") {
            $('#email-subject-error').show();
        } else {
            $('#email-subject-error').hide();
        }

        if (postData.email_message == "") {
            $('#email-message-error').show();
        } else {
            $('#email-message-error').hide();
        }

        if (postData.sms_message == "") {
            $('#sms-message-error').show();
        } else {
            $('#sms-message-error').hide();
        }

        if (postData.sms_message != "" && postData.email_message != "" && postData.email_subject != "") {
            $.ajax({
                type: "POST",
                url: $('#ajax-url-save').val(),
                data: postData,
                success: function (data) {
                    if (data.success) {
                        $('#rx-notification-setting-title').text('Success');
                        $('#rx-notification-setting-information').text('New Rx Order Notification Setting successfully saved!');
                    } else {
                        $('#rx-notification-setting-title').text('Error');
                        $('#rx-notification-setting-information').text('New Rx Order Notification Setting failed to save!');
                    }
                    $('#modal-rx-notification-setting').modal('show');
                },
                error: function () {
                    $('#rx-notification-setting-title').text('Error');
                    $('#rx-notification-setting-information').text('New Rx Order Notification Setting failed to save!');
                    $('#modal-rx-notification-setting').modal('show');
                }
            });
        }
    },
};

$(document).ready(function () {
    rxOrderNotificationSetting.init();
});