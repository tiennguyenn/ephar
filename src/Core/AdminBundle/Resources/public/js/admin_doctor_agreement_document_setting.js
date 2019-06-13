var doctorAgreementDocumentSetting = {
    today: new Date(),
    currentEffectiveDate: $('#effectiveDate').val(),
    init: function () {
        CKEDITOR.replace('doctor_agreement', {
            extraPlugins: 'filebrowser',
            filebrowserImageUploadUrl: $('#ajax-upload-image').val(),
            height: 700,
            allowedContent: true
        });
        CKEDITOR.replace('notification_email', {
            extraPlugins: 'filebrowser',
            filebrowserImageUploadUrl: $('#ajax-upload-image').val(),
            allowedContent: true
        });

        var endDate = new Date(doctorAgreementDocumentSetting.currentEffectiveDate);
        if (moment(doctorAgreementDocumentSetting.today).format('YYYY-MM-DD') === doctorAgreementDocumentSetting.currentEffectiveDate) {
            endDate = false;
        }

        if (doctorAgreementDocumentSetting.today > endDate) {
            endDate = false;
        }

        $('#effectiveDate').datepicker({
            format: 'yyyy-mm-dd',
            startDate: doctorAgreementDocumentSetting.today,
            endDate: endDate,
            forceParse: false
        });

        $('#doctor-agreement-setting-submit').click(function () {
            doctorAgreementDocumentSetting.submit();
        });
        $('#selectedSite').change(function () {
            doctorAgreementDocumentSetting.handleSelectSite();
        });
        $('#doctor-agreement-save-notification').click(function () {
            doctorAgreementDocumentSetting.saveNotification();
        });
        $('#doctor-agreement-send-notification').click(function () {
            doctorAgreementDocumentSetting.sendNotification();
        });

        $('#view-document-log').click(function () {
            DataTableLog.init();
            DataTableLog.getData();
            $('#modal-document-log').modal('show');
        });
    },
    submit: function () {
        var postData = {
            doctor_agreement: CKEDITOR.instances.doctor_agreement.getData(),
            effective_date: $('#effectiveDate').val(),
            site: $('#selectedSite').val()
        };

        if (postData.doctor_agreement == "") {
            $('#document-setting-error').show();
            return;
        } else {
            $('#document-setting-error').hide();
        }

        $.ajax({
            type: "POST",
            url: $('#ajax-url-save').val(),
            data: postData,
            success: function (data) {
                if (data.success) {
                    $('#documentId').val(data.file_document_id);
                    if (postData.effective_date > moment(doctorAgreementDocumentSetting.today).format('YYYY-MM-DD')) {
                        var _endDate = new Date(postData.effective_date);
                        $('#effectiveDate').datepicker('setEndDate', _endDate);
                    } else {
                        $('#effectiveDate').datepicker('setEndDate', false);
                    }
                    $('#doctor-agreement-title').text('Success');
                    $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement successfully saved!');
                } else {
                    $('#doctor-agreement-title').text('Error');
                    $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement failed to save!');
                }
                $('#modal-doctor-agreement').modal('show');
            },
            error: function () {
                $('#doctor-agreement-title').text('Error');
                $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement failed to save!');
                $('#modal-doctor-agreement').modal('show');
            }
        });
    },
    handleSelectSite: function () {
        var postData = {
            site: $('#selectedSite').val()
        };
        $.ajax({
            type: "GET",
            url: $('#ajax-url-get').val(),
            data: postData,
            success: function (data) {
                var doctor_agreement = data.doctor_agreement || {};
                var agreement_notification = data.doctor_agreement_notification || {};
                if (Object.keys(doctor_agreement).length > 0) {
                    $('#documentId').val(doctor_agreement.doctor_agreement_id);
                    CKEDITOR.instances.doctor_agreement.setData(doctor_agreement.contentAfter);
                    var effectiveDate = new Date(doctor_agreement.effectiveDate.date.replace(/-/g,"/"));
                    if (effectiveDate < doctorAgreementDocumentSetting.today) {
                        $('#effectiveDate').val(moment(effectiveDate).format('YYYY-MM-DD'));
                    } else {
                        $('#effectiveDate').datepicker('setDate', effectiveDate);
                    }
                    if (moment(effectiveDate).format('YYYY-MM-DD') > moment(doctorAgreementDocumentSetting.today).format('YYYY-MM-DD')) {
                        $('#effectiveDate').datepicker('setEndDate', effectiveDate);
                    } else {
                        $('#effectiveDate').datepicker('setEndDate', false);
                    }
                } else {
                    CKEDITOR.instances.doctor_agreement.setData('');
                    $('#effectiveDate').datepicker('setDate', new Date());
                }

                if (Object.keys(agreement_notification).length > 0) {
                    $('#documentNotificationId').val(agreement_notification.id);
                    $('#notification_subject').val(agreement_notification.subject);
                    CKEDITOR.instances.notification_email.setData(agreement_notification.content);
                    $('#last-send-date').text(agreement_notification.send_date);
                } else {
                    $('#documentNotificationId').val('');
                    $('#notification_subject').val('');
                    CKEDITOR.instances.notification_email.setData('');
                    $('#last-send-date').text('');
                }
            },
            error: function () {
                $('#doctor-agreement-title').text('Error');
                $('#doctor-agreement-information').text('Get Doctor\'s Subscriber Agreement failed!');
                $('#modal-doctor-agreement').modal('show');
            }
        });
    },
    saveNotification: function () {
        var postData = {
            id: $('#documentNotificationId').val(),
            subject: $('#notification_subject').val(),
            content: CKEDITOR.instances.notification_email.getData(),
            site: $('#selectedSite').val()
        };

        if (postData.subject == "") {
            $('#notification-subject-error').show();
        } else {
            $('#notification-subject-error').hide();
        }

        if (postData.content == "") {
            $('#notification-content-error').show();
        } else {
            $('#notification-content-error').hide();
        }

        if (postData.subject != "" && postData.content != "") {
            $.ajax({
                type: "POST",
                url: $('#ajax-url-save-notification').val(),
                data: postData,
                success: function (data) {
                    if (data.success) {
                        $('#doctor-agreement-title').text('Success');
                        $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement Notification successfully saved!');
                        $('#doctor-agreement-send-notification').removeClass('disabled');
                    } else {
                        $('#doctor-agreement-title').text('Error');
                        $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement Notification failed to save!');
                    }
                    $('#modal-doctor-agreement').modal('show');
                },
                error: function () {
                    $('#doctor-agreement-title').text('Error');
                    $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement Notification failed to save!');
                    $('#modal-doctor-agreement').modal('show');
                }
            });
        }
    },
    sendNotification: function () {
        $.ajax({
            type: "POST",
            url: $('#ajax-url-send-notification').val(),
            data: { 'site_id': $('#selectedSite').val() },
            success: function (data) {
                if (data.success) {
                    $('#doctor-agreement-title').text('Success');
                    $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement Notification successfully sent!');
                    $('#last-send-date').text(data.send_date);
                } else {
                    $('#doctor-agreement-title').text('Error');
                    $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement Notification failed to sent!');
                }
                $('#modal-doctor-agreement').modal('show');
            },
            error: function () {
                $('#doctor-agreement-title').text('Error');
                $('#doctor-agreement-information').text('Doctor\'s Subscriber Agreement Notification failed to sent!');
                $('#modal-doctor-agreement').modal('show');
            }
        });
    }
};

var DataTableLog = $.extend(Base, {
    tableLength: 10,
    tableData: '',
    currentPage: 1,
    ajaxUrl: $("#ajax-get-log-url").val(),
    bodyTag: '#table-body-document-log',
    pagingTag: '#list-table-pagin',
    infoTag: "#list-tables-info",
    tableLenthTag: '#table-length',
    tableId:'#sample_1',
    dataPost: {},
    maxPage : 0 ,

    init: function () {

        $(this.tableLenthTag).on('change', function () {
            DataTableLog.changeTableLength(this.value);
        });

        this.initPaging();
    },

    initPaging: function () {
        $("[class='make-switch']").bootstrapSwitch();

        $(this.pagingTag + " li").on('click', function (e) {
            e.preventDefault();
            if ($(this).is(':first-child')){
                page = 'des';
            } else if ($(this).is(':last-child')) {
                page = 'inc';
            } else {
                page = $(this).find('a').html();
            }
            DataTableLog.changePageData(page);
        });
    },

    changeTableLength: function (length) {
        this.currentPage = 1;
        this.tableLength = length;
        DataTableLog.getData();
    },

    changePageData: function (page) {
        var valid = true;
        if (page == 'inc') {
            if(this.currentPage == this.maxPage) {
                valid = false;
            } else {
                this.currentPage++;
            }

        } else if (page == 'des') {
            if(this.currentPage == 1) {
                valid = false;
            } else {
                this.currentPage--;
            }

        } else {
            this.currentPage = page;

        }
        if(valid) {
            DataTableLog.getData();
        }
    },

    changeSearchData: function (dataSearch) {
        this.tableData = dataSearch;
        this.currentPage = 1;
        DataTableLog.getData();
    },

    getData: function () {
        this.dataPost = {'limit': this.tableLength, 'page': this.currentPage, 'document_id': $('#documentId').val()};

        $.ajax({
            type: "POST",
            url: this.ajaxUrl,
            data: this.dataPost,
            beforeSend: function () {
                $(DataTableLog.bodyTag).html('<tr role="row"><td colspan="4" style="text-align: center;"> Loading...</td></tr>') ;
            },
            success: function (data, textStatus, jqXHR) {
                var result = data['data'];
                var total = data['total'];
                DataTableLog.generateView(result);
                DataTableLog.generateInfo(total);
                DataTableLog.generatePagin(total);
                DataTableLog.initPaging();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            }
        });
    },

    generateView: function (data) {
        var arrayLength = data.length;
        var contentBeforeUrl = $('#ajax-view-document-log-before').val();
        var contentAfterUrl = $('#ajax-view-document-log-after').val();
        var result = '';

        for (var i = 0; i < arrayLength; i++) {
            currContentBeforeUrl = contentBeforeUrl.replace('_documentId', data[i].logId);
            currContentAfterUrl = contentAfterUrl.replace('_documentId', data[i].logId);
            result += '<tr role="row" class="row-item rowItem">'
                + '<td><a href="'+ currContentBeforeUrl +'" target="_blank" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-sm" id="view-document-log-before">View Document</a></td>'
                + '<td><a href="'+ currContentAfterUrl +'" target="_blank" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-sm" id="view-document-log-before">View Document</a></td>'
                + '<td>' + data[i].effectiveDate + '</td>'
                + '<td>' + data[i].createdAt + '</td>'
                + '</tr>';
        }
        if (result == '') {
            result = '<tr role="row"><td colspan="4">Have no record in result  </td> </tr>';
        }
        $(this.bodyTag).html(result);
    },

    generateInfo: function (sum) {
        var total = sum | 0;
        var start = (this.currentPage - 1) * this.tableLength + 1;
        var end = this.currentPage * this.tableLength;

        if (end > total) {
            end = total;
        }
        if (total == 0) {
            start = 0;
        }
        $(this.infoTag).html("Showing " + start + " to " + end + " of " + total + " entries");
    }
});

$(document).ready(function () {
    doctorAgreementDocumentSetting.init();
});