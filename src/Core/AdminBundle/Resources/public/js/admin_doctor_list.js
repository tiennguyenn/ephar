var DataTable =$.extend(Base, {
    tableLength: 10,
    tableData: '',
    currentPage: 1,
    ajaxUrl: $("#ajax-url").val(),
    bodyTag: '#table-body-doctor',
    pagingTag: '#list-table-pagin',
    infoTag: "#list-tables-info",
    tableLenthTag: '#table-length',
    tableSearchTag: '#table-search-data',
    tableId:'#sample_1',
    doctorStatus: 2,
    currentDeleteDoctor: '',
    dataSort:{},
    dataPost: {},
    currentSearch: false,
    maxPage : 0 ,
    
    init: function () {
        this.resendWelcomeMail();
        this.suggestion($("#auto-complete-url").val(), {}, 'name');
        $(this.tableSearchTag).keyup(function (event) {
           // setTimeout( function(){
           //     if(!DataTable.currentSearch) {
           //         $("#sample_1_filter .icon-magnifier").click();
           //         DataTable.currentSearch = true;
           //     }
           // }, 200 );
            if (event.keyCode == 13) {
                $("#sample_1_filter .icon-magnifier").click();
            }
        });

        $("#sample_1_filter .icon-magnifier").on('click', function () {
            $.each($(this.tableId).find('th'), function () {               
                $(this).removeClass('sorting_asc');
                $(this).removeClass('sorting_desc');               
            });
            DataTable.dataSort = {};
            DataTable.changeSearchData($("#table-search-data").val());
        });
        
        $(this.tableLenthTag).on('change', function () {
            DataTable.changeTableLenth(this.value);
        });
        
        this.initPaging();

        $('.filter-changing a').on('click', function () {
            $(this).parent().first().find('a').removeClass('active');
            $(this).addClass('active');
            DataTable.doctorStatus = $(this).attr('data');
            DataTable.currentPage = 1;
            DataTable.getData();
            DataTable.suggestion($("#auto-complete-url").val(), {}, 'name');
        });
        
        $("#deleteDoctor").on('click', function () {
            var dataPost = {'id': DataTable.currentDeleteDoctor, 'type': 2};
            var ajaxUrl = $("#ajaxUrlUpdateStatus").val();
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: dataPost,
                beforeSend: function () {
                },
                success: function (data, textStatus, jqXHR) {
                    DataTable.getData();
                    DataTable.currentDeleteDoctor = '';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                },
                complete: function (jqXHR, textStatus) {
                }

            });

        });
        
        $("#cancelDeleteDoctor").on('click', function () {
            DataTable.currentDeleteDoctor = '';
        });
        
        $(this.tableId + ' th').on('click', function (){
            var attr = $(this).attr('data-colum-sort');
            if(typeof attr !== typeof undefined && attr !== false) {
                DataTable.changeSort($(this));
                DataTable.getData();
            }
        });
    }, 
    resendWelcomeMail: function(){
        var url = $('#ajaxResendWelcomeDoctor').val();
        var isClick = false;
        $('#sample_1_wrapper').on('click','.resend-email', function(e){
            e.preventDefault();
            if(isClick == false){
                $.ajax({
                    url: url,
                    data: {
                        id : $(this).data('id')
                    },
                    type: "POST",
                    beforeSend: function() {
                        isClick = true;
                    },
                    success: function (res) {
                        if(res == false){
                            $('#modal-resend-email').find('.update-profile-info').html('<i class="fa fa-check txt-green-new"></i> Account set up completed.');
                        } else {
                            $('#modal-resend-email').find('.update-profile-info').html('<i class="fa fa-check txt-green-new"></i> Welcome Email has been resent to the Doctor successfully.');
                        }
                        $('#modal-resend-email').modal('show');
                        isClick = false;
                    }
                });
            }
        });
    },
    suggestion: function(url, data, display_name) {
        
        var tag = $('.on-suggestion');
        if(tag.length > 0) {
            data = typeof data != 'undefined' ? data : {};
            data.status = this.doctorStatus;
            display_name = typeof display_name != 'undefined' ? display_name : 'name';
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
            tag.typeahead('destroy');
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
    
    changeSort:function(e) {
        var sort = e.attr('data-colum-sort');

        $.each($(this.tableId).find('th'), function () {
            if ($(this).attr('data-colum-sort') != sort) {
                $(this).removeClass('sorting_asc');
                $(this).removeClass('sorting_desc');
            }
        });

        DataTable.dataSort = {};
        if(typeof sort != 'undefined') {
            if(e.hasClass('sorting_asc')) {
                e.removeClass('sorting_asc');
                e.addClass('sorting_desc');
                DataTable.dataSort[sort] = 'DESC';                
            } else if(e.hasClass('sorting_desc')) {
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
        $("[class='make-switch']").bootstrapSwitch();
        $('.rowItemBtn-doctor').click(function () {
            var _this = $(this);
            var _parent = _this.closest('.rowItem');
    
            if (_parent.hasClass('open')) {
                _parent.next('.rowItemExpand').slideUp(0);
                _parent.removeClass('open');
            } else {
                _parent.addClass('open');
                _parent.next('.rowItemExpand').slideDown(0);
            }
        });
        
        $(this.pagingTag + " li").on('click', function (e) {
            e.preventDefault();
            if ($(this).is(':first-child')){
                page = 'des';
            } else if ($(this).is(':last-child')) {
                page = 'inc';
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
    
    getData: function () {        
        this.dataPost = {'search': this.tableData, 'length': this.tableLength, 'page': this.currentPage, 'status': this.doctorStatus,'sort': this.dataSort};
        
        $.ajax({
            type: "POST",
            url: this.ajaxUrl,
            data: this.dataPost,
            beforeSend: function () {
                $(DataTable.bodyTag).html('<tr role="row"><td colspan="6" style="text-align: center;"> Loading...</td></tr>') ;
            },
            success: function (data, textStatus, jqXHR) {
                var result = data['data'];
                var total = data['total'];
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
            },
            complete: function (jqXHR, textStatus) {
                DataTable.currentSearch = false;
            }
        });
    },
    
    initEventTable: function () {
        $('.make-switch').on('switchChange.bootstrapSwitch', function () {
            var _this = $(this);
            var _parent = _this.closest('.bootstrap-switch');
            var _labelText = _parent.next('.switch-text');

            if (_this.prop('checked', true)) {
                var dataPost = {'id': this.value, 'type': 1};
                var ajaxUrl = $("#ajaxUrlUpdateStatus").val();
                $.ajax({
                    type: "POST",
                    url: ajaxUrl,
                    data: dataPost,

                    success: function (data, textStatus, jqXHR) {

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
        
        $(".btn-delete").on('click', function () {
            DataTable.currentDeleteDoctor = $(this).attr('data-id');
        });
    },
    
    generateView: function (data) {        
        var arrayLength = data.length;
        var result = '';
        var reactivatePath = $('#reactivate-path').val();
        var path = $("#edit-path").val();
		var loginPath = $("#login-doctor-path").val();

        for (var i = 0; i < arrayLength; i++) {
            var curReactivatePath = reactivatePath.replace('id', data[i].hashId);
            var curPath = path.replace('id', data[i].hashId);
			var curLoginPath = loginPath.replace('id', data[i].hashId);
            var curReactivatePath = reactivatePath.replace('id', data[i].hashId);
            result += '<tr role="row" class="row-item rowItem">'
                    + '<td>'
                    + '<a class="btn btn-circle green-seagreen btn-xs row-item-btn rowItemBtn-doctor" href="javascript:;"><i class="fa fa-plus"></i></a> ' + data[i].code + ''
                    + '</td>'
                    + '<td>' + data[i].name + '</td>'
                    + '<td>' + data[i].registerDate + '</td>'
                    + '<td>' + data[i].email + '</td>'
                    + '<td>'
                    + '<div class="status-wrapper">';
            if (DataTable.doctorStatus != -1) {
                if (data[i].status) {
                    result += '<input type="checkbox" value="' + data[i].id + '" class="make-switch" checked  data-on-color="success" data-off-color="danger" data-on-text="' + "<i class='fa fa-check'></i>" + '" data-off-text="' + "<i class='fa fa-times'></i>" + '"  data-size="mini">'
                            + '<div class="switch-text" >'
                            + '<span class="switch-text-on">Active</span>'
                            + '<span class="switch-text-off hide-item">Deactivated</span>';
                } else {
                    result += '<input type="checkbox" value="' + data[i].id + '" class="make-switch"  data-on-color="success" data-off-color="danger" data-on-text="' + "<i class='fa fa-check'></i>" + '" data-off-text="' + "<i class='fa fa-times'></i>" + '"  data-size="mini">'
                            + '<div class="switch-text">'
                            + '<span class="switch-text-on hide-item">Active</span>'
                            + '<span class="switch-text-off ">Deactivated</span>';
                }
            }
            result += '</div>'
                    + '</div>'
                    + '</td>'
                    + '<td>';

            if (DataTable.doctorStatus != -1) {
                result += '<a href="' + curPath + '" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-xs">Edit <i class="fa fa-edit"></i> </a>'
						+ '<a href="' + curLoginPath + '" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-xs">Login <i class="fa fa-user"></i></a>'
                        + '<a href="#modal-delete" data-id="' + data[i].id + '" data-toggle="modal"  class="btn btn-ahalf-circle text-uppercase red btn-icon-right btn-xs btn-delete">Delete</a>';
            } else {
                result += '<a href="javascript:void(0)" data-doctor-id="' + data[i].id + '" data-href="' + curReactivatePath + '" class="btn btn-ahalf-circle reactivate-doctor text-uppercase green-seagreen btn-icon-right btn-xs">Re-activate</a>';
            }

            var txtLL = 'N/A';
            if(data[i].lastLogin != null){
                txtLL = data[i].lastLogin;
            }
            if(data[i].confirmed == true){
                var setupPart = '<div class="col-md-7">'
                    + '<dl class="list-item list-item-account">'
                    + '<dt class="mt-5">Account Status:</dt>'
                    + '<dd>Password set up completed</dd></dl>'
                    + '<dl class="list-item list-item-account pt-0"><dt>Last login:</dt><dd>'+ txtLL +'</dd></dl>'
                    + '</div>';
            } else {
                var setupPart = '<div class="col-md-7">'
                    + '<dl class="list-item list-item-account">'
                    + '<dt class="mt-5">Account Status:</dt>'
                    + '<dd>Password not set up <a data-id="'+ data[i].id +'" class="btn text-uppercase green-seagreen btn-sm ml-10 resend-email">Resend welcome email</a> </dd></dl>'
                    + '<dl class="list-item list-item-account pt-0"><dt>Last login:</dt><dd>'+ txtLL +'</dd></dl>'
                    + '</div>';
            }

            result += '</td>'
                    + '</tr>'
                    + '<tr class="row-item-expand hide-item rowItemExpand">'
                    + '<td colspan="6">'
                    + '<div class="row">'
                    + '<div class="col-md-5">'
                    + '<div class="item-expand-wrap text-left">'
                    + '<dl class="list-item"><dt>Agent</dt><dd>' + data[i].agent + '</dd></dl>'
                    + '<dl class="list-item"><dt>Country</dt><dd>' + data[i].country + '</dd></dl>'
                    + '<dl class="list-item"><dt>City</dt><dd>' + data[i].city + '</dd></dl>'
                    + '</div>'
                    + '</div>'
                    + setupPart
                    + '</div>'
                    + '</td>'
                    + '</tr>';
        }
        if (result == '') {
            result = '<tr role="row"><td colspan="6">Have no record in result  </td> </tr>';
        }
        $(this.bodyTag).html(result);
        this.reactivateDeletedDoctor();
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
    },

    reactivateDeletedDoctor: function(){

        $('.reactivate-doctor').click(function () {
            var id = $(this).data('doctor-id');
            var ajaxUrl = $(this).data('href');
            $('#reactivateDoctor').attr('data-doctor-id', id);
            $('#modal-reactivate').modal('show');

            $('#reactivateDoctor').click(function () {
                $.ajax({
                    type: "POST",
                    url: ajaxUrl,
                    beforeSend: function() {
                        $('#modal-reactivate').modal('hide');
                    },
                    success: function (data) {
                        var succ = data.success;
                        if(succ)
                        {
                            DataTable.getData();
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        if (typeof errorCallback == 'function')
                            return errorCallback(jqXHR, textStatus, errorThrown);
                        return false;
                    },
                    complete: function () {
                        DataTable.currentSearch = false;
                    },
                });
            });
        });
    }
});

$(document).ready(function () {
    DataTable.init();
    DataTable.getData();
});