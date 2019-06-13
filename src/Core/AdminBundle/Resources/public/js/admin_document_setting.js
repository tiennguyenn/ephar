var documentSetting = {
    documentName: $('#documentName').val(),
    contentSelector: $('#contentSelector').val(),
    currentEffectiveDate: $('#effectiveDate').val(),
    today: new Date(),
    init: function () {
        CKEDITOR.replace(this.contentSelector, {
            extraPlugins: 'filebrowser',
            filebrowserImageUploadUrl: $('#ajax-upload-image').val(),
            height: 700,
            allowedContent: true
        });

        var endDate = new Date(documentSetting.currentEffectiveDate);
        if (moment(documentSetting.today).format('YYYY-MM-DD') === documentSetting.currentEffectiveDate) {
            endDate = false;
        }

        if (documentSetting.today > endDate) {
            endDate = false;
        }

        $('#effectiveDate').datepicker({
            format: 'yyyy-mm-dd',
            startDate: documentSetting.today,
            endDate: endDate,
            forceParse: false
        });

        $('#document-setting-submit').click(function () {
            documentSetting.submitDocumentSetting();
        });

        $('#selectedSite').change(function () {
            documentSetting.handleSiteChange();
        });

        $('#view-document-log').click(function () {
            DataTableLog.init();
            DataTableLog.getData();
            $('#modal-document-log').modal('show');
        });
    },
    submitDocumentSetting: function () {
        var postData = {
            document_content: CKEDITOR.instances[documentSetting.contentSelector].getData(),
            effective_date: $('#effectiveDate').val(),
            site: $('#selectedSite').val()
        };

        if (postData.document_content == "") {
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
                    if (postData.effective_date > moment(documentSetting.today).format('YYYY-MM-DD')) {
                        var _endDate = new Date(postData.effective_date);
                        $('#effectiveDate').datepicker('setEndDate', _endDate);
                    } else {
                        $('#effectiveDate').datepicker('setEndDate', false);
                    }
                    $('#document-title').text('Success');
                    $('#document-information').text(documentSetting.documentName + ' successfully saved!');
                } else {
                    $('#document-title').text('Error');
                    $('#document-information').text(documentSetting.documentName + ' failed to save!');
                }
                $('#modal-document').modal('show');
            },
            error: function () {
                $('#document-title').text('Error');
                $('#document-information').text(documentSetting.documentName + ' failed to save!');
                $('#modal-document').modal('show');
            }
        });
    },
    handleSiteChange: function () {
        var postData = {
            site: $('#selectedSite').val()
        };
        $.ajax({
            type: "GET",
            url: $('#ajax-url-get').val(),
            data: postData,
            success: function (data) {
                var _responseData = data || {};
                if (Object.keys(_responseData).length > 0) {
                    $('#documentId').val(_responseData.documentId);
                    CKEDITOR.instances[documentSetting.contentSelector].setData(_responseData.contentAfter ? _responseData.contentAfter : "");
                    console.log(_responseData.effectiveDate.date.replace(/-/g,"/"));
                    var effectiveDate = new Date(_responseData.effectiveDate.date.replace(/-/g,"/"));
                    if (moment(effectiveDate).format('YYYY-MM-DD') > moment(documentSetting.today).format('YYYY-MM-DD')) {
                        $('#effectiveDate').datepicker('setEndDate', effectiveDate);
                    } else {
                        $('#effectiveDate').datepicker('setEndDate', false);
                    }
                    if (effectiveDate < documentSetting.today) {
                        $('#effectiveDate').val(moment(effectiveDate).format('YYYY-MM-DD'));
                    } else {
                        $('#effectiveDate').datepicker('setDate', effectiveDate);
                    }
                } else {
                    CKEDITOR.instances[documentSetting.contentSelector].setData('');
                    $('#effectiveDate').datepicker('setDate', new Date());
                }
            },
            error: function () {
                $('#document-title').text('Error');
                $('#document-information').text('Get '+ documentSetting.documentName +' failed!');
                $('#modal-document').modal('show');
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
    documentSetting.init();
});