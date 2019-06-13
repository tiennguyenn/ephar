/**
 * Common javascript function
  */
var jsDataService = {
    callAPI: function (url, data, method, successCallback, errorCallback, loadingContainer, dataType) {
        dataType = typeof dataType !== 'undefined' ? dataType : 'json';
        //if (this.xhr) {
        //    this.xhr.abort();
        //}

        this.xhr = $.ajax({
            type: method,
            url: url,
            data: data,
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                if (typeof successCallback == 'function')
                    return successCallback(data, textStatus, jqXHR);
                return false;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus) {
            },
            dataType: dataType
        });
    },
    callAPISync: function (url, data, method, successCallback, errorCallback, loadingContainer, dataType) {
        dataType = typeof dataType !== 'undefined' ? dataType : 'json';
        //if (this.xhr) {
        //    this.xhr.abort();
        //}

        this.xhr = $.ajax({
            type: method,
            url: url,
            data: data,
            async: false,
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                if (typeof successCallback == 'function')
                    return successCallback(data, textStatus, jqXHR);
                return false;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus) {
            },
            dataType: dataType
        });
    }
};

var jsCommon = {
    checkSingleSession: function (){
        var checkID = setInterval(function () {
            $.ajax({
                type: "POST",
                url: singleSessionUrl,
                success: function(resp){
                    if(resp.status == 1){
                        clearInterval(checkID);
                        $('#modal-single-session').modal('show');
                    } else if(resp.status == 2){
                        clearInterval(checkID);
                    }
                },
            });
        },60000);
    },
    writeLogs: function () {
        $(document).on('click', '.write-log', function () {
            $_this = $(this);
            module = $_this.data('module');
            newValue = $_this.data('newvalue');
            entityId = $_this.data('entityid');
            action = $_this.data('action');
            $.ajax({
                type: "POST",
                url: $('#admin_write_logs_url').val(),
                data: {
                    module: module, 
                    newValue: newValue, 
                    entityId : entityId,
                    action: action
                },
                beforeSend: function( ) {
                    // $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
                },
                success: function(response){
                    console.log(response)
                },
                complete: function(data){
                }
            });
        });
    },
    viewLogs: function (viewLogBtnId, viewLogUrlId, modalId, containerId ) {
        $modal = $('#admin-modal-view-logs');
        
        if (modalId != undefined) {
            $modal = $(modalId);
        }
        conId = '#admin-list-logs';
        if (containerId != undefined) {
            conId = containerId;
        }
        $(viewLogBtnId).on('click', function () {
            module           = $(this).data('module');
            viewLogTemplate  = $(this).data('template-view');
            printLogTemplate = $(this).data('template-print');
            fileName         = $(this).data('fileName');
            breadcrumbTitle  = $(this).data('breadcrumb-title');
            entityId         = $(this).data('entity-id');
            modalTitle       = $(this).data('modal-title');
            breadcrumbUrl    = $(this).data('breadcrumb-url');
			breadcrumbHome	 = $(this).data('breadcrumb-home');
			breadcrumbHome	 = breadcrumbHome == '' ? 'Home' : breadcrumbHome;

            $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
			$modal.find('.breadcrumb span.active').text(modalTitle);
			$modal.find('.breadcrumb a.bc-home').attr("href", breadcrumbUrl).text(breadcrumbHome);
			$modal.find('#title').text(modalTitle);
			$modal.modal();
			$(conId).html('<div style="text-align: center;">Loading...</div>');
            $.ajax({
                type: "POST",
                url: $(viewLogUrlId).val(),
                data: {module: module, 
                    viewLogTemplate: viewLogTemplate, 
                    printLogTemplate: printLogTemplate, 
                    fileName : fileName,
                    breadcrumbTitle: breadcrumbTitle,
                    entityId: entityId,
                    breadcrumbUrl: breadcrumbUrl
                },
                beforeSend: function( ) {
                    $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
                },
                success: function(response){
                    
                    html = "<div>" + response + "</div>";
                    printLogsUrl = $(html).find('#print_log_url').val() + '&title=' + modalTitle;
                    breadcrumbTitle = $(html).find('#breadcrumb_title').val();
                    $modal.find('#print_log_anchor').attr('href', printLogsUrl );
                    $modal.find('.breadcrumb span.active').text(breadcrumbTitle);
                    $modal.find('#title').text(modalTitle);
                    if(breadcrumbUrl != '') {
                        $modal.find('.breadcrumb a.bc-home').attr('href', breadcrumbUrl);
                    }

                    $(conId).html(response);
                    //$modal.modal();
                },
                complete: function(data){
                }
            });
        });
    },
    downloadCSV: function(url, data, filename){
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(result){

                var element = document.createElement('a');
                element.setAttribute('href', 'data:application/csv;charset=UTF-8,' + encodeURIComponent(result));
                element.setAttribute('download', filename);

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            beforeSend: function () {
            },
        });
    },

    pagingAjax: function(){
        $(document).on("click","div.ajax_paging  a.p_next, div.ajax_paging  a.btnPage, div.ajax_paging  a.p_pre", function(e){
            e.preventDefault();
            url = $(this).attr('href');
            data = '';
            method = 'GET';
            loadingContainer = $("div.content-table");
            successCallback = function(data){
                $( e.target ).closest( "div.content-table" ).html(data);
            };
            errorCallback =  function (xhr, ajaxOptions, thrownError){
                alert(xhr.status + ": " + thrownError);
            };
            dataType = 'html';

            jsDataService.callAPI(url, data, method, successCallback, errorCallback, loadingContainer, dataType);
        });
    },
    suggestion: function(url, data, display_name, $el) {
        var tag = $el || $('.on-suggestion');
        if(tag.length > 0) {
            data = typeof data !== 'undefined' ? data : {};
            display_name = typeof display_name !== 'undefined' ? display_name : 'name';
            var sources = function (term, sync) {
                data['term'] = term;
                $.ajax({
                    url: url,
                    data: data,
                    dataType: "json",
                    async: false,
                    type: "POST",
                    success: function (res) {
                        return sync(res);
                    }
                });
            };
            tag.typeahead({
                highlight: false,
                hint: true,
                minLength: 3
            }, {
                display: display_name,
                source: sources
            }).on('keyup', this, function (e) {
                if (e.keyCode == 13) {
                    tag.typeahead('close');
                }
            });

        }
    },
    nl2br: function (text) {
        if(text != '' && text != null) {
            res = text.replace(/\n/g, "<br />");
        } else {
            res = '';
        }
        return res;
    },
    getFileExtension: function (fileNameStr) {
        return fileNameStr.substr((fileNameStr.lastIndexOf('.') + 1))
    },
    currencyFormat: function(n) {
        return Number(n).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    },
    currencyFormatExtra: function(n) {
        return Number(n).toFixed(3).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    },
    digitPercent: function(){
        $(document).on('focus', 'input.on-digit-percent', function(){
            var self = $(this);
            self.val(self.val().replace(',',''));
        }).on('blur', 'input.on-digit-percent', function(){
            var self = $(this);
            if(self.val() != ""){
                self.val(jsCommon.currencyFormat(self.val().replace(/[^0-9\.]/g)));
            }
        }).on('input', 'input.on-digit-percent', function(e) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((e.which != 46 || self.val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)){
                e.preventDefault();
            }
        });
        $(document).on('focus', 'input.on-digit-extra', function(){
            var self = $(this);
            self.val(self.val().replace(',',''));
        }).on('blur', 'input.on-digit-extra', function(){
            var self = $(this);
            if(self.val() != ""){
                self.val(jsCommon.currencyFormatExtra(self.val().replace(/[^0-9\.]/g)));
            }
        }).on('input', 'input.on-digit-extra', function(e) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((e.which != 46 || self.val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)){
                e.preventDefault();
            }
        });
        $(document).on('input', 'input.on-number-only', function(e) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((e.which != 46 || self.val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)){
                e.preventDefault();
            }
        });
    }
};
