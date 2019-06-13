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
    currentDeleteDelivery: '',
    dataSort: {},
    dataPost: {},
    currentSearch: false,
    maxPage : 0 ,
    autoClass: '.on-suggestion',
    attrSort:'data-colum-sort',
    currentType: '',
    currentRateId: '',
    currentRateData: '',
    updateRateData: '',
    disable: false,
    deleteId: '',
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

        });

        $("#delete-mpa-item").on('click', function(){
            DataTable.deleteMpa();
        });

    },
    deleteMpa:function(){
        if(this.deleteId == ''){
            $('#modal-delete').modal('toggle');
        }
        var dataPost = {'id': this.deleteId, 'type': 3};
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
                var successMessage = '<div class="alert alert-success">MPA is deleted successfully.</div>';
                $("#master-body-content").prepend(successMessage);
                DataTable.disable = false;
                $('#modal-delete').modal('toggle');
                DataTable.getData();
            },
            error: function(){
                DataTable.disable = false;
                var successMessage = '<div class="alert alert-danger"><strong>Alert!</strong> Delete MPA status fail.</div>';
                $("#master-body-content").prepend(successMessage);
                $('#modal-delete').modal('toggle');
            }
        });
    },

    getData: function () {
        if(this.disable){
            return;
        }
        this.dataPost = {'search': this.tableData,
            'length': this.tableLength,
            'page': this.currentPage,
            'sort': this.dataSort,
            'type':1
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
        $("[class='make-switch']").bootstrapSwitch();
        $('.make-switch').on('switchChange.bootstrapSwitch', function () {
            var _this = $(this);
            var _parent = _this.closest('.bootstrap-switch');
            var _labelText = _parent.next('.switch-text');

            if (_this.prop('checked', true)) {
                var dataPost = {'id': this.value, 'type': 2};
                var ajaxUrl = DataTable.ajaxUrl;
                if(DataTable.disable){
                    return;
                }
                DataTable.disable = true;
                $.ajax({
                    type: "POST",
                    url: ajaxUrl,
                    data: dataPost,
                    success: function (data, textStatus, jqXHR) {
                        $("#master-body-content").find(".alert").remove();
                        var successMessage = '<div class="alert alert-success"> MPA is updated successfully.</div>';
                        $("#master-body-content").prepend(successMessage);
                        DataTable.disable = false;
                    },
                    error: function(){
                        DataTable.disable = false;
                        $("#master-body-content").find(".alert").remove();
                        var error = '<div class="alert alert-danger"><strong>Alert!</strong> Update MPA status fail.</div>';
                        $("#master-body-content").prepend(error);
                    }
                });
            }

            if (_parent.hasClass('bootstrap-switch-off')) {
                _labelText.find('.switch-text-on').hide();
                _labelText.find('.switch-text-off').show();
            } else {
                _labelText.find('.switch-text-off').hide();
                _labelText.find('.switch-text-on').show();
            }
        });


        $('.delete-mpa').on('click', function(){
            DataTable.deleteId =  $(this).data('id');
        });

        $('.resend-btn-mpa').on('click', function(){
            var id =  $(this).data('id');
            var dataPost = {'id': id, 'type': 4};
            var ajaxUrl = DataTable.ajaxUrl;
            if(DataTable.disable){
                return;
            }
            DataTable.disable = true;
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: dataPost,
                success: function (data, textStatus, jqXHR) {
                    $("#modal-resend-success").modal();
                    DataTable.disable = false;
                },
                error: function(){
                    DataTable.disable = false;
                    $("#master-body-content").find(".alert").remove();
                    var error = '<div class="alert alert-danger"><strong>Alert!</strong> Resend email to MPA status fail.</div>';
                    $("#master-body-content").prepend(error);
                }
            });
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
        var path = $("#edit-url").val();
        var curPath = path.replace('id', line.hashId);
        var detailPath = $("#detail-url").val();
        var detailPath = detailPath.replace('id', line.hashId);
        var str = '<tr role="row" class="row-item rowItem">\n' +
            '\n' +
            '                                                <td>\n' +
            '                                                    <a class="btn btn-circle green-seagreen btn-xs row-item-btn rowItemBtn" href="javascript:;"><i class="fa fa-plus"></i></a> '+ line.code+'\n' +
            '                                                </td>\n' +
            '                                                <td> ' + line.name + ' </td>\n' +
            '                                                <td> ' + line.registerDate + '</td>\n' +
            '                                                <td>\n' +
            '<div class="status-wrapper">\n';

            if (line.status) {
                str += '<input type="checkbox" value="' + line.id + '" class="make-switch" ';
                if(!line.accountStatus) {
                    str += 'disabled ';
                }
                str +='checked  data-on-color="success" data-off-color="danger" data-on-text="' + "<i class='fa fa-check'></i>" + '" data-off-text="' + "<i class='fa fa-times'></i>" + '"  data-size="mini">'
                    + '<div class="switch-text" >'
                    + '<span class="switch-text-on">Active</span>'
                    + '<span class="switch-text-off hide-item">Deactivated</span>';
            } else {
                str += '<input type="checkbox" value="' + line.id + '" class="make-switch" ';
                if(!line.accountStatus) {
                    str += 'disabled ';
                }
                str += ' data-on-color="success" data-off-color="danger" data-on-text="' + "<i class='fa fa-check'></i>" + '" data-off-text="' + "<i class='fa fa-times'></i>" + '"  data-size="mini">'
                    + '<div class="switch-text">'
                    + '<span class="switch-text-on hide-item">Active</span>'
                    + '<span class="switch-text-off ">Deactivated</span>';
            }



            str += '</div>\n' +
            '                                                </td>\n' +
            '                                                <td>  <a href="' + curPath + '" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-xs">\n' +
            '                                                        Edit Profile <i class="fa fa-edit"></i> </a> ' +
                '<a href="'+detailPath+'" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-xs">\n' +
            '                                                        View/Edit Doctor List <i class="fa fa-edit"></i> </a>' +
                ' <a href="#modal-delete" data-toggle="modal" class="btn btn-ahalf-circle text-uppercase red btn-icon-right btn-xs delete-mpa" data-id="' + line.id + '">Delete</a> </td>\n' +
            '                                            </tr>\n' +
            '                                            <tr class="row-item-expand hide-item rowItemExpand">\n' +
            '                                                <td colspan="5">\n' +
            '                                                    <div class="row">\n' +
            '                                                        <div class="col-md-5">\n' +
            '                                                        </div>\n' +
            '                                                        <div class="col-md-7">\n' +
            '                                                            <dl class="list-item list-item-account">\n';

            if(!line.accountStatus) {
                str += '<dt class="mt-5">Account Status:</dt><dd>Password not set up<a href="javascript:;" class="btn text-uppercase green-seagreen btn-sm ml-10 resend-btn-mpa" data-id="' + line.id + '">Resend welcome email</a> </dd>\n';
            } else {
                str += '<dt >Account Status:</dt><dd>Password set up completed</dd>\n';
            }
            str += '</dl>\n' +
            '                                                            <dl class="list-item list-item-account pt-0">\n' +
            '                                                                <dt>Last login:</dt>\n' +
            '                                                                <dd>'+ line.lastLogin+'</dd>\n' +
            '                                                            </dl>\n' +
            '                                                        </div>\n' +
            '                                                    </div>\n' +
            '                                                </td>\n' +
            '                                            </tr>';
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
