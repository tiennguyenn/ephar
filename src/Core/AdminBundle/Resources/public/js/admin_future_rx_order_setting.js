var futureRxOrderNotificationSetting = {
    init: function () {

        $('#rx-notification-setting-email-submit').click(function () {
            futureRxOrderNotificationSetting.submit(1);
        });

        $('#rx-notification-setting-sms-submit').click(function () {
            futureRxOrderNotificationSetting.submit(2);
        });
    },
    submit: function (type) {
        var postData = {};

        // type 1 for email, type 2 for sms
        if (type === 1) {
            postData = {
                email_subject: $('#email_subject').val(),
                email_message: $('#email_message').val(),
                type: 'email'
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

            if (postData.email_message == "" || postData.email_subject == "") {
                return false;
            }
        } else if (type === 2) {
            postData = {
                sms_message: $('#sms_message').val(),
                type: 'sms'
            };

            if (postData.sms_message == "") {
                $('#sms-message-error').show();
            } else {
                $('#sms-message-error').hide();
            }

            if (postData.sms_message == "") {
                return false;
            }
        }

        $.ajax({
            type: "POST",
            url: $('#ajax-url-save').val(),
            data: postData,
            success: function (data) {
                if (data.success) {
                    $('#rx-notification-setting-title').text('Success');
                    $('#rx-notification-setting-information').text('Future Rx Order Notification Setting successfully saved!');
                } else {
                    $('#rx-notification-setting-title').text('Error');
                    $('#rx-notification-setting-information').text('Future Rx Order Notification Setting failed to save!');
                }
                $('#modal-rx-notification-setting').modal('show');
            },
            error: function () {
                $('#rx-notification-setting-title').text('Error');
                $('#rx-notification-setting-information').text('Future Rx Order Notification Setting failed to save!');
                $('#modal-rx-notification-setting').modal('show');
            }
        });
    },
};

$(document).ready(function () {
    futureRxOrderNotificationSetting.init();
});