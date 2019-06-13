var RXList = {
    init: function() {
        jsCommon.pagingAjax();
        RXList.onClickActivitiesLog();
        RXList.onSubmitForm();
        RXList.onClickDeleteButton();
        RXList.onClickRecallButton();
        RXList.onClickSorting();
        RXList.onClickResendButton();
        RXList.onClickEditButton();
        RXList.onClickFilterStatus();
        RXList.bootstrap();
    },
    bootstrap: function() {
        $('.icheck').each(function() {
            var checkboxClass = $(this).attr('data-checkbox') ? $(this).attr('data-checkbox') : 'icheckbox_minimal-grey';
            var radioClass = $(this).attr('data-radio') ? $(this).attr('data-radio') : 'iradio_minimal-grey';

            if (checkboxClass.indexOf('_line') > -1 || radioClass.indexOf('_line') > -1) {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass,
                    insert: '<div class="icheck_line-icon"></div>' + $(this).attr("data-label")
                });
            } else {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass
                });
            }
        });
    },
    onClickActivitiesLog: function() {
        $('#sample_1_wrapper').on('click', '.activitieLink', function() {
            var text     = 'ORDER ID: ' + $(this).data('ordernumber'),
                url      = $(this).data('url'),
                urlPrint = $(this).data('print');

            $('#orderIdText').text(text);
            $('#modal-view-logs').find('#printBtn').attr('href', urlPrint);

            $.ajax({
                url: url,
                data: {},
                success: function(response) {
                    $('#activitiesContainer').find('table').remove();
                    $('#activitiesContainer').append(response);

                    $('#modal-view-logs').modal('show');
                }
            });
        });

        $('#printBtn').click(function() {

        });
    },
    onSubmitForm: function() {
        $('#searchRXForm').submit(function(event) {
            event.preventDefault();

            var url = $('#ajaxListRXUrl').val();
            var data = $('#searchRXForm').serialize();
            var dataType = 'html';
            var method = 'GET';

            var successCallback = function(data) {
                $("#tableContainer" ).html(data);
            };
            var errorCallback = function() {};
            var loadingContainer = $("#tableContainer");
            jsDataService.callAPI(url, data, method, successCallback, errorCallback, loadingContainer, dataType);
        });

        $('#perPage').change(function() {
            $('#searchRXForm').submit();
        });

        $('#issueDate').keyup(function(event) {
            if (event.which === 13) {
                $('#searchRXForm').submit();
            }
        })

        $(document).on("mouseover", ".icon-magnifier", function () {
            $(this).css("cursor", "pointer");
        });

        $(document).on('click', '.icon-magnifier', function () {
            $('#searchRXForm').submit();
        });
    },
    onClickDeleteButton: function() {
        $('#tableContainer').on('click', '.deleteBtn', function() {
            var href = $(this).data('url') || '';
            var orderNumber = $(this).data('ordernumber');
            var isScheduled = $(this).data('scheduled') || 0;
            var scheduledDate = $(this).data('scheduled-date') || '';
            if (isScheduled) {
                $('#delete-dialog').html('<p>Are you sure you want to delete the scheduled RX Order? <br /><br />' +
                    '          <strong>Order ID:</strong> '+ orderNumber +' <br />' +
                    '          <strong>Send Date:</strong> '+ scheduledDate +
                    '        </p>');
            }
            $('#confirmBtn').attr('href', href);
        });
    },
    onClickRecallButton: function() {
        $('#tableContainer').on('click', '.recallBtn', function() {
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
                    $('#reasonTextArea').parent().find('.error').remove();
                }
            });
        });

        $('#reasonTextArea').keyup(function() {
            var remain = 2000 - $('#reasonTextArea').val().length;
            $('#lengthSpan').text(remain);
        });

        $('#recallUrl, #recallCancelUrl, #recallEditUrl').click(function() {
            if ($(this).attr('id') === 'recallCancelUrl') {
                $('#recallAction').val('cancel');
            } else if ($(this).attr('id') === 'recallEditUrl') {
                $('#recallAction').val('edit');
            }
            var length = $('#reasonTextArea').val().trim().length;
            var remain = 2000 - length;
            if (length < 10 || remain < 0 || 2000 == remain) {
                $('#reasonTextArea').focus();
                $('#reasonTextArea').parent().find('.error').remove();
                $('#reasonTextArea').parent().append('<span class="error">Please input at least 10 characters</span>');
                return false;
            }

            $('#recallForm').submit();
        });
    },
    onClickResendButton: function() {
         $('#tableContainer').on('click', '.resendBtn', function() {
            $(this).addClass('disabled');

            var url  = $(this).data('url');
            $.ajax({
                url: url,
                data: {},
                success: function(response) {
                }
            });
        });
    },
    onClickFilterStatus: function() {
        $('.rxStatus').click(function() {
            // http://handlebarsjs.com/
            var template = Handlebars.compile($('#statusTemplate').html());
            var status = $(this).data('status') || 0;

            if ($(this).hasClass('active')) {
                return false;
            }

            $('.rxStatus').removeClass('active');
            $(this).addClass('active');

            $('#searchRXForm').find('input[name="rxStatus[]"]').remove();
            $('.rxStatus').each(function() {
                var value = $(this).data('status');
                if (value && $(this).hasClass('active')) {
                    $('#searchRXForm').append(template({'value': value}));
                }
            });

            $('input[name="page"]').val(0);
            $('#searchRXForm').submit();
        });
    },
    onClickSorting: function() {
        $('#sample_1_wrapper').on('click', '.sorting', function() {
            var list = [];
            list.push($(this).data('sort'));
            if ($(this).hasClass('sorting_asc')) {
                list.push('desc');
            } else {
                list.push('asc');
            }
            $('#sorting').val(list.join('-'));

            $('#searchRXForm').submit();
        });
    },
    onClickEditButton: function() {
        $('#tableContainer').on('click', '.editBtn', function() {
            $(this).addClass('disabled');

            var url = $(this).data('url');
            $.ajax({
                url: url,
                data: {},
                success: function(response) {
                    window.location.href = response.url;
                }
            });
        });
    }
}

$(document).ready(function() {
    RXList.init();
});