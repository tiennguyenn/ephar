/**
 * Rx Refill Reminder Settings
 * Author Luyen Nguyen
 * Date: 08/28/2017
 */

// Validation message
var mssgError = 'Reminder days is required';
// Title log
var titleLog30 = 'REMINDERS FOR SUPPLY LENGTH OF 30 DAYS';
var titleLog60 = 'REMINDERS FOR SUPPLY LENGTH OF 60 DAYS AND ABOVE';
// Init function
var jsReminder = {
    init: function () {
        jsReminder.reminderValidate();
        jsReminder.logs();
    },
    reminderValidate: function () {
        $("#frm-reminder-thirty").validate({
            rules: {
                "rx_refill_reminder_setting[reminderthirtydays]": {
                    required: true,
                    min: 0,
                    max: 30,
                    number: true
                }
            },
            messages: {
                "rx_refill_reminder_setting[reminderthirtydays]": {
                    required: mssgError
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                element.parents('div.reminder-text').addClass('error');
                error.insertAfter(element.parents('div.reminder-text'));
            },
            submitHandler: function (form) {
                form.submit();
            },
            errorClass: "error"
        });
        $("#frm-reminder-sixty").validate({
            rules: {
                "rx_refill_reminder_setting[remindersixtydays]": {
                    required: true,
                    min: 0,
                    max: 180,
                    number: true
                }
            },
            messages: {
                "rx_refill_reminder_setting[remindersixtydays]": {
                    required: mssgError
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                element.parents('div.reminder-text').addClass('error');
                error.insertAfter(element.parents('div.reminder-text'));
            },
            submitHandler: function (form) {
                form.submit();
            },
            errorClass: "error"
        });
        $("#frm-reminder-emails").validate({
            rules: {
                "rx_refill_reminder_setting[first_patient_days]": {
                    required: true,
                    min: 0,
                    max: 180,
                    digits: true
                },
                "rx_refill_reminder_setting[first_doctor_days]": {
                    required: true,
                    min: 0,
                    max: 180,
                    digits: true
                },
                "rx_refill_reminder_setting[second_patient_days]": {
                    required: true,
                    min: 0,
                    max: 180,
                    digits: true
                },
                "rx_refill_reminder_setting[second_doctor_days]": {
                    required: true,
                    min: 0,
                    max: 180,
                    digits: true
                }
            },
            messages: {
                "rx_refill_reminder_setting[first_patient_days]": {
                    required: mssgError
                },
               "rx_refill_reminder_setting[first_doctor_days]": {
                    required: mssgError
                },
                "rx_refill_reminder_setting[second_patient_days]": {
                    required: mssgError
                },
               "rx_refill_reminder_setting[second_doctor_days]": {
                    required: mssgError
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                element.parents('p.reminder-text').addClass('error');
                error.insertAfter(element.parents('p.reminder-text'));
            },
            submitHandler: function (form) {
                form.submit();
            },
            errorClass: "error"
        });
    },
    logs: function () {
        $("a#logs_30").bind("click", jsReminder.viewLogs30);
        $("a#logs_60").bind("click", jsReminder.viewLogs60);
    },
    viewLogs30: function () {
        $("#printLog30").css({"display": "block"});
        $("#printLog60").css({"display": "none"});
        var url = $("#logs_30_url").val();
        $("#title").text(titleLog30);
        jsReminder.viewLogs(url);
    },
    viewLogs60: function () {
        $("#printLog30").css({"display": "none"});
        $("#printLog60").css({"display": "block"});
        var url = $("#logs_60_url").val();
        $("#title").text(titleLog60);
        jsReminder.viewLogs(url);
    },
    viewLogs: function (url) {
        $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
        $.post(url, {}, function (response) {
            $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
            $("#list-logs").html(response);
            $('#modal-view-logs').modal();
        });
    }
};

$(document).ready(function () {
    jsReminder.init();
});