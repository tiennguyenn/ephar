// Title log
var titleLogCycleOne = 'CYCLE 1 REMINDERS POLICY';
var titleLogCycleTwo = 'CYCLE 2 - EXTENSION OF PRESCRIPTION ORDER (GRACE PERIOD SETTINGS)';
var RxReminderSetting = {
    init: function () {
        RxReminderSetting.vaildateForm();
        RxReminderSetting.handleWysihtml5();
        RxReminderSetting.logs();
        RxReminderSetting.resetForm();
    },
    handleWysihtml5: function () {
        if (!jQuery().wysihtml5) {
            return;
        }

        if ($('.wysihtml5').size() > 0) {
            $('.wysihtml5').wysihtml5({
                "stylesheets": ["/bundles/admin/assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
            });
        }
    },
    vaildateForm: function () {
        $('#cycleOneForm').validate({
            rules: {
                'form[reminder1][durationTime]': {
                    required: true,
                    digits: true
                },
                'form[reminder2][durationTime]': {
                    required: true,
                    digits: true
                },
                'form[reminder3][durationTime]': {
                    required: true,
                    digits: true
                },
                'form[reminder3][expiredTime]': {
                    required: true,
                    digits: true
                }
            },
            submitHandler: function (form) {
                form.submit();
            },
            errorPlacement: function (error, element) {
                return false;
            }
        });
    },
    logs: function () {
        $("a#logs_cycle_one").bind("click", RxReminderSetting.viewLogsCycleOne);
        $("a#logs_cycle_two").bind("click", RxReminderSetting.viewLogsCycleTwo);
    },
    viewLogsCycleOne: function () {
        $("#printLogCycleOne").css({"display": "block"});
        $("#printLogCycleTwo").css({"display": "none"});
        var url = $("#logs_cycle_one_url").val();
        $("#title").text(titleLogCycleOne);
        RxReminderSetting.viewLogs(url);
    },
    viewLogsCycleTwo: function () {
        $("#printLogCycleOne").css({"display": "none"});
        $("#printLogCycleTwo").css({"display": "block"});
        var url = $("#logs_cycle_two_url").val();
        $("#title").text(titleLogCycleTwo);
        RxReminderSetting.viewLogs(url);
    },
    viewLogs: function (url) {
        $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
        $.post(url, {}, function (response) {
            $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
            $("#list-logs").html(response);
            $('#modal-view-logs').modal();
        });
    },
    resetForm: function() {
        $('button[type="reset"]').on('click', function() {
            var form = $(this).parents('form:first');
            $(form).find('.select2 option').prop('selected', function() {
                return this.defaultSelected;
            });
            $('.select2').trigger('change.select2');
        });
    }
}

$(document).ready(function () {
    RxReminderSetting.init();
});