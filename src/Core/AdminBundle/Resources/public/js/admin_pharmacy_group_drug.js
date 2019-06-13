var jsGD = {
    init: function() {
        jsCommon.pagingAjax();
        jsCommon.digitPercent();
        this.formGroupDrug();
        this.search();
        jsCommon.viewLogs('#view_logs_anchor', '#admin_view_logs_url');
    },
    search: function(){
        var _frmFilter = $("#frm-group-filter");
        var _container = $("div.content-table.gd-container");
        _frmFilter.on('submit',function(e){
            e.preventDefault() 
        }).on('keyup','#p_filter_term', function(e) {
            e.preventDefault();
            if (e.keyCode == 13) {
                jsGD.getData();
            }
        }).on('change','#p_filter_per_page', function(e){
            e.preventDefault();
            jsGD.getData();
        });
        _container.on('click', 'th.sorting', function(e){
            e.preventDefault();
            var colName = $(this).data('colname');
            if($(this).hasClass('sorting_asc')){
                $(this).removeClass('sorting_asc');
                $(this).addClass('sorting_desc');
                _frmFilter.find("input[name='p_filter[sorting]']").val(colName+'_desc');
            } else {
                $(this).removeClass('sorting_desc');
                $(this).addClass('sorting_asc');
                _frmFilter.find("input[name='p_filter[sorting]']").val(colName+'_asc');
            }
            jsGD.getData();
        });
    },
    formGroupDrug: function(){
        var m_ui = $("#modal-gd-form");
        $(document).on('click', ".btn-modal-gd-add, .btn-modal-gd-edit", function(e){
            e.preventDefault();
            var _id = ($(this).data('gd') == 1)? $(this).closest('tr').data('id'): "";
            successCallback = function (res) {
                m_ui.find("div.content-views").html(res);
                m_ui.find(".msg-container").html("");
                m_ui.modal();
                m_ui.find('#select_drugs').multiSelect();
                $('#frm-group-drug').validate({
                    rules : {
                        "name": {
                            required : true
                        },
                        "description": {
                            required : true
                        },
                        "localPricePercentage": {
                            required : true,
                            greaterMin: 0
                        },
                        "overseasPricePercentage": {
                            required : true,
                            greaterMin: 0
                        }
                    },
                    messages : {
                        "name": {
                            required : msg.msgRequiredField.replace('%s', "Name")
                        },
                        "description": {
                            required : msg.msgRequiredField.replace('%s', "Description")
                        },
                        "localPricePercentage": {
                            required : msg.msgRequiredField.replace('%s', "GMEDS Selling Price for Local Patients"),
                            greaterMin : msg.msgGreaterThan.replace('%s', "GMEDS Selling Price for Local Patients").replace('%s', "0")
                        },
                        "overseasPricePercentage": {
                            required : msg.msgRequiredField.replace('%s', "GMEDS Selling Price for Overseas Patients"),
                            greaterMin : msg.msgGreaterThan.replace('%s', "GMEDS Selling Price for Local Patients").replace('%s', "0")
                        }
                    },
                    errorElement : 'span',
                    errorPlacement : function(error, element) {
                        error.insertAfter(element);
                    },
                    submitHandler : function(form) {
                        jsGD.updateGroupDrug($(form), m_ui);
                        return false;
                    },
                    errorClass : "error"
                });
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_admin_pharmacy_group_drug_form").val(), {'id': _id, 'f': 0}, "POST", successCallback, errorCallback, null, 'html');
        });
        //load delete form
        var m_ui_delete = $("#modal-gd-delete");
        $(document).on('click', ".btn-modal-gd-delete", function(e){
            e.preventDefault();
            successCallback = function (res) {
                m_ui_delete.find("div.content-views").html(res);
                m_ui_delete.find(".msg-container").html("");
                m_ui_delete.modal();
                //remove per item
                m_ui_delete.find('.drug-gd-delete').on('click', function(e) {
                    e.preventDefault();
                    var _this = $(this);
                    successCallback = function (res) {
                        if(res.status == true)
                            _this.closest('div.each-medicines').remove();
                    };
                    errorCallback = function (xhr, ajaxOptions, thrownError) {};
                    jsDataService.callAPI($("#url_admin_pharmacy_group_drug_delete").val(), {'id': _this.data('id'), 'f': 0} , "POST", successCallback, errorCallback, null, 'json');
                });
                //remove group drug
                m_ui_delete.find('.btn-gd-delete').on('click', function(e) {
                    e.preventDefault();
                    var _this = $(this);
                    successCallback = function (res) {
                        if(res.status == true) {
                            jsGD.getData();
                            m_ui_delete.modal('hide');
                        } else {
                            m_ui_delete.find(".msg-container").html('<div class="alert alert-danger"><button class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button> '+ res.msg +'</div>');
                        }
                    };
                    errorCallback = function (xhr, ajaxOptions, thrownError) {};
                    jsDataService.callAPI($("#url_admin_pharmacy_group_drug_delete").val(), {'id': _this.closest('form').data('id'), 'f': 1} , "POST", successCallback, errorCallback, null, 'json');
                });
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_admin_pharmacy_group_drug_form").val(), {'id': $(this).closest('tr').data('id'), 'f': 1}, "POST", successCallback, errorCallback, null, 'html');
        });
        //move group
        var m_ui_mg = $("#modal-move-group");
        $(document).on('click', ".btn-modal-move-group", function(e){
            e.preventDefault();
            var drugId = [];
            $('#productList').find('input.drugCheckbox:checked').each(function() {
                drugId.push($(this).data('drugid'));
            });
            var params = {
                'id': $(this).data('id'), 
                'f': 2,
                'drugid': drugId
            };
            if(drugId.length > 0) {
                successCallback = function (res) {
                    m_ui_mg.find("div.content-views").html(res);
                    m_ui_mg.find(".msg-container").html("");
                    m_ui_mg.modal();
                    //remove per item
                    m_ui_mg.find('.btn-move-group').on('click', function(e) {
                        e.preventDefault();
                        var _this = $(this);

                        if(m_ui_mg.find('#group').val() != "") {
                            successCallback = function (res) {
                                if(res) {
                                    $(document).find('#filter_all').click();
                                    m_ui_mg.modal('hide');
                                }
                            };
                            errorCallback = function (xhr, ajaxOptions, thrownError) {};
                            jsDataService.callAPI($("#url_admin_pharmacy_group_drug_move").val(), _this.closest('form').serialize() , "POST", successCallback, errorCallback, null, 'json');
                        }
                    });
                };
                errorCallback = function (xhr, ajaxOptions, thrownError) {
                };
                jsDataService.callAPI($("#url_admin_pharmacy_group_drug_form").val(), params, "POST", successCallback, errorCallback, null, 'html');
            }
        });
        $('#productList').on('click', '.checkedAll', function() {
            var checked = $(this).prop('checked');
            $('#productList').find('input.checkboxes').not(":disabled").prop('checked', checked);
        });
    },
    updateGroupDrug: function(form, modal_ui) {
        successCallback = function (res) {
            var msgText = "";
            if(res == 'existed') {
                msgText = msg.msgItemExist.replace('%s', 'Group Name');
            } else if(res) {
                modal_ui.modal('hide');
                $(document).find(".msg-page").html('<div class="alert alert-success"><button class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button> '+ msg.msgUpdatedSuccess.replace('%s', 'Group') +'</div>');
                jsGD.getData();
            } else {
                msgText = msg.msgCannotEdited.replace('%s', 'Group');                
            }

            if(msgText != "") {            
                form.find(".msg-container").html('<div class="alert alert-danger"><button class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button> '+ msgText +'</div>');
            }
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };
        jsDataService.callAPI($("#url_admin_pharmacy_group_drug_update").val(), form.serialize() , "POST", successCallback, errorCallback, null, 'json');
    },
    getData: function(){
        successCallback = function (res) {
            $("div.content-table.gd-container" ).html(res);
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };
        jsDataService.callAPI($("#url_admin_pharmacy_group_drug_filter").val(), $('#frm-group-filter').serialize(), "POST", successCallback, errorCallback, null, 'html');
    }
}

$(document).ready(function() {
    $.validator.addMethod('greaterMin', function (value, el, param) {
        return value > param;
    });
    jsGD.init();
});