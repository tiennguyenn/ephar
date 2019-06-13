var RXView = {
    init: function() {
        RXView.onClickReportIssueButton();
        RXView.onClickReportAdverseButton();
    },
    onClickReportIssueButton: function() {
        $('.recallBtn').click(function() {
            var url = $(this).data('url');
            $.ajax({
                url: url,
                data: {},
                success: function(response) {
                    $('#orderId').text(response.orderNumber);
                    $('#patientName').text(response.patientName);
                    $('#recallForm').attr('action', response.recallUrl);
                    $('#reasonTextArea').val('');
                    $('#modalRecall').modal('show');
                    $('#recallUrl').removeClass('disabled');
                    $('#reasonTextArea').parent().find('.error').remove();
                }
            });
        });

        $('#reasonTextArea').keyup(function() {
            var remain = 2000 - $('#reasonTextArea').val().length;
            $('#lengthSpan').text(remain);
        });

        $('#recallUrl').click(function() {
            var length = $('#reasonTextArea').val().trim().length;
            var remain = 2000 - length;
            if (length < 10 || remain < 0 || 2000 == remain) {
                $('#reasonTextArea').focus();
                $('#reasonTextArea').parent().find('.error').remove();
                $('#reasonTextArea').parent().append('<span class="error">Please input at least 10 characters</span>');
                return false;
            }

            var data = {};
            data.redirect = 0;
            data.reasonForRecall = $('#reasonTextArea').val();
            data.isConfirmed = $('#recallForm').find("input[name=isConfirmed]").val();
            $(this).addClass('disabled');
            $.post($('#recallForm').attr("action"), data, function(response){
                if (response.success == 1) {
                    var tr = '<tr role="row">';
                    tr += '<td>' + response.date + '</td>';
                    tr += '<td>' + response.reporter + '</td>';
                    tr += '<td>' + response.status + '</td>';
                    tr += '<td>' + response.note + '</td>';
                    tr += '</tr>';

                    $("#issues").append(tr).parent().parent().show();
                }

                $('#modalRecall').modal('hide');
                $('#recallUrl').addClass('disabled');
            }, 'json');
            //$('#recallForm').submit();
        })
    },
    onClickReportAdverseButton: function() {
        $('.adverseBtn').click(function() {
            $('#modalAdverse').modal('show');
        });

        $('#adverseUrl').click(function() {
            var redirectUrl = $(this).attr('data-redirectUrl');
            var data = {};
            data.rxId = $(this).attr('data-rxId');
            $.post($('#ajaxAdverseUrl').val(), data, function(response) {
                if (response.success) {
                    window.open(redirectUrl, '_blank');
                    $('#modalAdverse').modal('hide');
                } else {
                    return false;
                }
            }, 'json');
        });
    }
}

$(document).ready(function() {
    RXView.init();
});