var DataTable = $.extend(Base, {
    tableLength: 10,
    tableData: '',
    currentPage: 1,
    ajaxUrl: $("#ajax-url").val(),
    bodyTag: '#table-body-mpa',
    pagingTag: '#list-table-pagin',
    infoTag: "#list-tables-info",
    tableLenthTag: '#table-length',
    tableSearchTag: '#table-search-data',
    tableId: '#list-mpa-admin',

    dataSort: {},
    dataPost: {},
    currentSearch: false,
    maxPage : 0 ,
    autoClass: '.on-suggestion',
    attrSort:'data-colum-sort',

    disable: false,
    deleteId: '',
    insertTextFilter : "",
    removeTextFilter : "",
    isUpdated: false,
    currentAjaxData: {},
    init: function () {

        $(this.tableSearchTag).keyup(function (event) {
//            setTimeout( function(){
//                if(!DataTable.currentSearch){
//                    $("#sample_1_filter .icon-magnifier").click();
//                    DataTable.currentSearch = true;
//                }
//            }, 200 );
            if (event.keyCode == 13) {
                $("#sample_1_filter .icon-magnifier").click();
            }
        });

        $("#sample_1_filter .icon-magnifier").on('click', function () {
            DataTable.changeSearchData($("#table-search-data").val());

        });

        $(this.tableId + ' th').on('click', function () {
            var attr = $(this).attr(DataTable.attrSort);
            if(typeof attr !== typeof undefined && attr !== false) {
                DataTable.changeSort($(this));
                DataTable.getData();
            }
        });


        $('#modal-delete').on('hidden.bs.modal', function () {
            DataTable.deleteId = '';
            DataTable.updateResetModal();
        });

        $("#delete-mpa-item").on('click', function(){
            DataTable.deleteMpa();
        });

        $("#btn-add-doctor-mpa").on("click", function (e) {
            e.preventDefault();
            DataTable.loadModalAddDoctor();
        });

        this.removeAlert();

    },
    loadModalAddDoctor(){

        $("#modal-add-doctor").find("#list-mpa-doctor").html("");
        DataTable.insertTextFilter = "";
        DataTable.removeTextFilter = "";
        $(".filter-input").val("");
        this.createViewListDoctor(0);
        
        $(".filter-input").keyup(function (event) {
            if($(this).data("type") == "insert"){
                DataTable.insertTextFilter = $(this).val();
            }
            if($(this).data("type") == "remove"){
                DataTable.removeTextFilter = $(this).val();
            }
            if (event.keyCode == 13) {
                DataTable.createViewListDoctor(1);
            }
        });
        $('#modal-add-doctor').on('hidden.bs.modal', function () {
            DataTable.updateResetModal();
            if(DataTable.isUpdated){
                DataTable.getData();
                DataTable.isUpdated = false;
            }
        });

        $("#update-asign-doctor-mpa").unbind("click");
        $("#update-asign-doctor-mpa").on("click", function () {
            DataTable.saveUpdateDoctorMpa();
        });

    },

    createViewListDoctor(filter){
        var dataPost = {'id': $("#current-mpa").val(), 'type': 6,'search-select': this.insertTextFilter,'select-remove': this.removeTextFilter,filter:filter };
        var ajaxUrl = DataTable.ajaxUrl;
        if(this.disable){
            return;
        }
        DataTable.disable = true;
        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: dataPost,
            success: function (data, textStatus, jqXHR) {

                DataTable.disable = false;
                if(data.success){
                    DataTable.updateViewModalAddDoctor(data.result);
                }

            },
            error: function(){
                DataTable.disable = false;
            }
        });




    },
    updateViewModalAddDoctor(data){

        var html = '';
        $.each(data, function () {
            if(this.select){
                html += "<option value='" + this.id + "' selected>"+ this.name+"</option>"
            } else {
                html += "<option value='" + this.id + "'>"+ this.name+"</option>"
            }

        });

        $("#list-mpa-doctor").html(html);
        $("#list-mpa-doctor").multiSelect("refresh");

        $("#list-mpa-doctor").unbind("change");
        $("#list-mpa-doctor").on("change", function () {
            DataTable.updateDoctorMpa($(this).val());
        });

    },

    updateDoctorMpa(val){
        var dataPost = {'id': $("#current-mpa").val(), 'type': 7,'data': val};
        var ajaxUrl = DataTable.ajaxUrl;
        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: dataPost,
            success: function (data, textStatus, jqXHR) {
                DataTable.isUpdated = true;
            },
            error: function(){

            }
        });
    },

    updateResetModal(){
        var dataPost = {'type': 12};
        var ajaxUrl = DataTable.ajaxUrl;
        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: dataPost,
            success: function (data, textStatus, jqXHR) {


            },
            error: function(){

            }
        });
    },
    saveUpdateDoctorMpa(){
        var val = $("#list-mpa-doctor").val();
        var dataPost = {'id': $("#current-mpa").val(), 'type': 11,'data': val};
        var ajaxUrl = DataTable.ajaxUrl;
        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: dataPost,
            success: function (data, textStatus, jqXHR) {
                if(data.success){
                    DataTable.showMessageUpdateSuccess(data.result)
                }
                DataTable.isUpdated = true;
                $('#modal-add-doctor').modal("toggle");
            },
            error: function(){

            }
        });
    },
    showMessageUpdateSuccess(result){
        var message = ''
        if(result.new.length > 0){
            message += result.new + ' <br/>';
        }
        if(result.remove.length > 0){
            message += result.remove ;
        }
        $("#master-body-content").find(".alert").remove();
        var successMessage = '<div class="alert alert-success">' + message + '</div>';
        $("#master-body-content").prepend(successMessage);
        DataTable.removeAlert();
    },
    deleteMpa:function(){

        if(this.deleteId == ''){
            $('#modal-delete-doctor').modal('toggle');
        }
        var dataPost = {'id': $("#current-mpa").val(),'doctor-id': this.deleteId, 'type': 9};
        var ajaxUrl = DataTable.ajaxUrl;
        if(this.disable){
            return;
        }
        DataTable.disable = true;
        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: dataPost,
            async: false,
            success: function (data, textStatus, jqXHR) {
                if(data.success){
                    DataTable.showMessageUpdateSuccess(data.result);
                }
                $('#modal-delete-doctor').modal('toggle');
                DataTable.disable = false;
                DataTable.getData();
            },
            error: function(){
                $('#modal-delete-doctor').modal('toggle');
                DataTable.disable = false;

            }
        });
    },

    getData: function () {
        if(this.disable){
            return;
        }
        this.dataPost = {
            'search': this.tableData,
            'length': this.tableLength,
            'page': this.currentPage,
            'sort': this.dataSort,
            'type': 5,
            'id': $("#current-mpa").val()
        };
        $.ajax({
            type: "POST",
            url: this.ajaxUrl,
            data: this.dataPost,
            beforeSend: function () {
                $(DataTable.bodyTag).html('<tr role="row"><td colspan="5" style="text-align: center;"> Loading...</td></tr>');
            },
            success: function (data, textStatus, jqXHR) {
                var result = data['result']['data'];
                var total = data['result']['total'];
                DataTable.currentAjaxData = result;
                DataTable.generateView(result);
                DataTable.generateInfo(total);
                DataTable.generatePagin(total);
                DataTable.initPaging();
                DataTable.initEventTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            }

        });
    },

    initEventTable: function () {

        $('.delete-doctor-link').on('click', function(){
            DataTable.deleteId =  $(this).data('id');
            $('#modal-delete-doctor').modal();
        });

        $(".btn-change-permission").on("click", function(){
            var id = $(this).data("id");
            DataTable.updateViewPermisson(id);
        });
        $(".btn-save-mpa-role").on("click", function(e){
            e.preventDefault();
            var id = $(this).data("id");
            DataTable.updateMpaPermisson(id);
        });

    },
    updateViewPermisson(id) {
        var data = [];
        $.each( DataTable.currentAjaxData , function () {
            if(this.id == id) {
                data = this.roles;
            }
        });

        $.each($("#line-" + id).find(".mpa-role"), function () {
            var role = $(this).data("role");
            var self = this;
            $.each(data, function(index, value) {
                if (role == value) {
                    $(self).attr("checked",true);
                    return false;
                }
            });
        });


    },

    updateMpaPermisson(id) {

        var data = [];
        $.each($("#line-" + id).find(".mpa-role"), function () {
            if($(this).is(":checked")){
                data.push($(this).data("role"));
            }
        });
        var dataPost = {'id': $("#current-mpa").val(),'doctor-id': id, roles: data, 'type': 8};
        var ajaxUrl = DataTable.ajaxUrl;

        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: dataPost,
            success: function (data, textStatus, jqXHR) {
                $("#master-body-content").find(".alert").remove();
                var successMessage = '<div class="alert alert-success">Permission settings updated successfully.</div>';
                $("#master-body-content").prepend(successMessage);
                DataTable.removeAlert();
            },
            error: function(){
                $("#master-body-content").find(".alert").remove();
                var successMessage = '<div class="alert alert-danger">Permission settings updated fail.</div>';
                $("#master-body-content").prepend(successMessage);
                DataTable.removeAlert();
            }
        });
    },

    generateView: function (data)
    {
        var result = '';
        $.each(data, function () {
            result += DataTable.renderLine(this);
        });
        if (result == '') {
            result = '<tr role="row"><td colspan="7">No Records</td> </tr>';
        }
        $(this.bodyTag).html(result);
    },
    renderLine:function(line){
        // var path = $("#edit-url").val();
        // var curPath = path.replace('id', line.hashId);
        // var detailPath = $("#detail-url").val();
        // var detailPath = detailPath.replace('id', line.hashId);

        var roles = line.roles;
        var str = '<tr role="row" class="row-item rowItem">' +
            ' <td>' + line.code +
            ' </td>' +
            ' <td> '+ line.doctorName +' </td>' +
            ' <td> '+ line.registerDate +'</td>' +
            '                        <td> <a href="javascript:;" data-id="'+ line.id +'" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-xs rowItemBtn btn-change-permission">' +
            '                   Manage Permissions </a> <a data-id="'+ line.id +'"  class="btn btn-ahalf-circle text-uppercase red btn-icon-right btn-xs delete-doctor-link">Remove From Manager List</a> </td>\n' +
            '                      </tr>' +
            '                      <tr class="row-item-expand hide-item rowItemExpand" id="line-'+ line.id +'">' +
            '                      <td colspan="4">' +
            '                            <div class="item-expand-wrap ml-20 form">' +
            '                                <h4 class="block mt-10 mb-0 pt-0 pb-0">Manage Permissions</h4>' +
            '                                <div class="mt-30"><strong>My Patients</strong></div>' +
            '                                <div class="icheck-list mt-10 mb-10 ml-20">' +
            '<label><input type="checkbox" class="icheck mpa-role"  data-role="patient_index" id="checkPatients1" name="checkPatients"> List Patients</label>' +
            '<label><input type="checkbox" class="icheck mpa-role"  data-role="patient_new" id="checkPatients2" name="checkPatients"> Register New Patient</label>' +
            '                                </div>' +
            '                                <div class="mt-30"><strong>My Prescriptions</strong></div>' +
            '                                <div class="icheck-list mt-10 mb-10 ml-20">' +
            '                                  <label>\n' +
            '                                    <input type="checkbox"  data-role="create_rx" class="icheck  mpa-role" id="checkPrescriptions1" name="checkPrescriptions">Create New RX</label>' +
            '                                    <label>' +
            '                                    <input type="checkbox" data-role="send_to_patient" class="icheck  mpa-role" id="checkPrescriptions2" name="checkPrescriptions"> Send RX Order</label>' +
            '                                    <label>\n' +
            '                                    <input type="checkbox" data-role="list_rx"  class="icheck  mpa-role" id="checkPrescriptions3" name="checkPrescriptions"> All Rx</label>' +
            '                                    <label>\n' +
            '                                    <input type="checkbox" data-role="list_draft_rx"  class="icheck  mpa-role" id="checkPrescriptions5" name="checkPrescriptions"> Draft Rx</label>' +
            '                                    <label>\n' +
            '                                    <input type="checkbox" data-role="list_scheduled_rx"  class="icheck  mpa-role" id="checkPrescriptions11" name="checkPrescriptions"> Scheduled Rx Orders</label>' +
            '                                    <label>\n' +
            '                                    <input type="checkbox" data-role="list_pending_rx" class="icheck  mpa-role" id="checkPrescriptions6" name="checkPrescriptions"> Pending RX Orders</label>' +
            '                                    <label>\n' +
            '                                    <input type="checkbox" data-role="list_confirmed_rx"  class="icheck  mpa-role" id="checkPrescriptions7" name="checkPrescriptions"> Paid Rx Orders</label>' +
            '                                    <label>\n' +
            '                                    <input type="checkbox" data-role="list_recalled_rx"  class="icheck  mpa-role" id="checkPrescriptions8" name="checkPrescriptions"> Recalled RX Orders</label>' +
            '                                    <label>\n' +
            '                                    <input type="checkbox" data-role="list_failed_rx"  class="icheck  mpa-role" id="checkPrescriptions9" name="checkPrescriptions"> Failed RX Orders</label>' +
            '                                    <label>\n' +
            '                                    <input type="checkbox" data-role="list_reported_rx"  class="icheck  mpa-role" id="checkPrescriptions10" name="checkPrescriptions"> Rx Orders with reported issues</label>' +
            '                                </div>' +
            '                                <div class="mt-30"><strong>My Reports</strong></div>' +
            '                                <div class="icheck-list mt-10 mb-10 ml-20">' +
            '                                  <label>' +
            '                                    <input type="checkbox" class="icheck  mpa-role" data-role="doctor_report_transaction_history"  id="checkPrescriptions11" name="checkPrescriptions1">RX Transaction History</label>' +
            '                                    <label>' +
            '                                    <input type="checkbox" class="icheck  mpa-role" data-role="doctor_report_monthly_statement" id="checkPrescriptions12" name="checkPrescriptions1"> Monthly Statements</label>' +
            '                                </div>' +
            '                                <div class="mt-30"><strong>My Resources</strong></div>' +
            '                                <div class="icheck-list mt-10 mb-10 ml-20">' +
            '                                  <label>' +
            '                                    <input type="checkbox" class="icheck mpa-role" data-role="doctor_custom_selling_prices" id="checkResources1" name="checkResources1" '+(line.isCustomizeMedicineEnabled ? '' : 'disabled="disabled"')+'>Custom Medicine Selling Prices</label>' +
            '                                </div>' +
            '                                <div class="form-actions mt-30">' +
            '                                      <a href="" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-sm button-submit btn-save-mpa-role" data-id="'+ line.id +'">Save</a>' +
            '                                </div>' +
            '                            </div>' +
            '                        </td>' +
            '                      </tr>';

        return str;
    },



    changeSort: function (e) {
        var sort = e.attr('data-colum-sort');

        $.each($(this.tableId).find('th'), function () {
            if ($(this).attr('data-colum-sort') != sort) {
                $(this).removeClass('sorting_asc');
                $(this).removeClass('sorting_desc');
            }
        });

        DataTable.dataSort = {};

        if (typeof sort != 'undefined') {
            if (e.hasClass('sorting_asc')) {
                e.removeClass('sorting_asc');
                e.addClass('sorting_desc');
                DataTable.dataSort[sort] = 'DESC';
            } else if (e.hasClass('sorting_desc')) {
                e.removeClass('sorting_desc');
                e.addClass('sorting_asc');
                DataTable.dataSort[sort] = 'ASC';
            } else {
                e.addClass('sorting_asc');
                DataTable.dataSort[sort] = 'ASC';

            }
        }


    },

    initPaging: function () {

        $(this.pagingTag + " li").on('click', function (e) {
            e.preventDefault();
            if ($(this).is(':first-child'))
            {
                page = 'des'

            } else if ($(this).is(':last-child')) {

                page = 'inc'
            } else {
                page = $(this).find('a').html();
            }
            DataTable.changePageData(page);

        });
    },

    changeTableLenth: function (length) {
        this.currentPage = 1;
        this.tableLength = length;
        DataTable.getData();
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
            DataTable.getData();
        }

    },

    changeSearchData: function (dataSearch) {
        this.tableData = dataSearch;
        this.currentPage = 1;
        DataTable.getData();
    },

    generateInfo: function (sum) {
        var total = sum | 0;
        if(total == 0) {
            $(this.infoTag).html("");
            return;
        }
        var start = (this.currentPage - 1) * this.tableLength + 1;
        var end = this.currentPage * this.tableLength;

        if (end > total) {
            end = total;
        }
        if (total == 0) {
            start = 0;
        }
        $(this.infoTag).html("Showing " + start + " to " + end + " of " + total + " entries")
    },

});
function number_format(number, decimals, dec_point, thousands_sep) {
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
$(document).ready(function () {

    DataTable.init();
    DataTable.getData();
});
