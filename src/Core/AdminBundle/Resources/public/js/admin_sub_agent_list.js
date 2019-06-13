var DataTable = $.extend(Base, {
    tableLength: 10,
    tableData: '',
    currentPage: 1,
    ajaxUrl: $("#ajax-url").val(),
    bodyTag: '#table-body-agent',
    pagingTag: '#list-table-pagin',
    infoTag: "#list-tables-info",
    tableLenthTag: '#table-length',
    tableSearchTag: '#table-search-data',
    tableId: '#sample_1',
    agentStatus: 2,
    currentDeleteAgent: '',
    dataSort: {},
    dataPost: {},
    currentSearch: false,
    agentId: $('#main-agent-id').val(),
    maxPage : 0 ,

    init: function () {
        this.resendWelcomeMail();
        this.suggestion($("#auto-complete-url").val(), {id:$("#main-agent-id").val()}, 'name');
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
        $(this.tableLenthTag).on('change', function () {

            DataTable.changeTableLenth(this.value);
        });
        this.initPaging();

        $('.filter-changing a').on('click', function () {
            $(this).parent().first().find('a').removeClass('active');
            $(this).addClass('active');
            DataTable.agentStatus = $(this).attr('data');
            DataTable.currentPage = 1;
            DataTable.getData();
            DataTable.suggestion($("#auto-complete-url").val(), {id:$("#main-agent-id").val()}, 'name');
        });
        $("#deleteAgent").on('click', function () {
            var dataPost = {'id': DataTable.currentDeleteAgent, 'type': 2};
            var ajaxUrl = $("#ajaxUrlUpdateStatus").val();
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: dataPost,
                beforeSend: function () {
                },
                success: function (data, textStatus, jqXHR) {
                    DataTable.getData();
                    DataTable.currentDeleteAgent = '';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                },
                complete: function (jqXHR, textStatus) {
                },

            });

        });
        $("#cancelDeleteAgent").on('click', function () {
            DataTable.currentDeleteAgent = '';
        });
        $(this.tableId + ' th').on('click', function () {            
            var attr = $(this).attr('data-colum-sort');
            if(typeof attr !== typeof undefined && attr !== false) {
                DataTable.changeSort($(this));
                DataTable.getData();
            }
        });
        
        $("#admin_doctor_signature").removeAttr('required');
            
        $("#admin_doctor_signature").on('change',function() {
            $("#admin_doctor_signature").attr('required','required');
        });

        this.removeAlert();
    },
    
    resendWelcomeMail: function(){
        var url = $('#ajaxResendWelcomeAgent').val();
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
                    beforeSend: function(){
                        isClick = true;
                    },
                    success: function (res) {
                        if(res == false){
                            $('#modal-resend-email').find('.update-profile-info').html('<i class="fa fa-check txt-green-new"></i> Account set up completed.');
                        } else {
                            $('#modal-resend-email').find('.update-profile-info').html('<i class="fa fa-check txt-green-new"></i> Welcome Email has been resent to the Sub Agent successfully.');
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
            data.status = this.agentStatus;
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
        $("[class='make-switch']").bootstrapSwitch();
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

    getData: function () {
        this.dataPost = {'search': this.tableData, 'length': this.tableLength, 'page': this.currentPage, 'status': this.agentStatus, 'sort': this.dataSort,'id': this.agentId};
        $.ajax({
            type: "POST",
            url: this.ajaxUrl,
            data: this.dataPost,
            beforeSend: function () {
                $(DataTable.bodyTag).html('<tr role="row"><td colspan="6" style="text-align: center;">Loading...</td></tr>');
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
            },

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
            DataTable.currentDeleteAgent = $(this).attr('data-id');
        });
        $(".view-doctor").on('click', function (e) {
            e.preventDefault();
            var agent = $(this).attr('data-id');
            ajaxUrl = $("#view-doctor-url").val();
            this.dataPost = {'id': agent, 'type': 1};
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: this.dataPost,
                beforeSend: function () {
                },
                success: function (data, textStatus, jqXHR) {
                    console.log(data);
                    DataTable.generateViewDoctor(data);
                    $("#modal-view-doctors").modal('show');

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                },
                complete: function (jqXHR, textStatus) {
                    DataTable.currentSearch = false;
                },

            });

        });
        $(".delete-agent").on('click', function (e) {

            e.preventDefault();
            var agent = $(this).attr('data-id');
            ajaxUrl = $("#view-doctor-url").val();
            this.dataPost = {'id': agent, 'type': 2};
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: this.dataPost,
                beforeSend: function () {
                    
                  
                },
                success: function (data, textStatus, jqXHR) {                    
                    DataTable.generateViewDeleteAgent(data);
                    $("#modal-delete-agent").modal('show');

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                },
                complete: function (jqXHR, textStatus) {
                    DataTable.currentSearch = false;
                },

            });

        });
    },

    generateViewDeleteAgent: function (data) {
        var result = '';
        var agent = data.data['agent'];
        var doctors = data.data['doctors'];
        var id = 'form-delete-agent-'+agent.code;
        result += '<form id="' + id+ '"><ul class="update-info-detail mb-0">'
            + '<li><span class="text">Code:</span> ' + agent.code + '</li>'
            + '<li><span class="text">Agent Name:</span> ' + agent.name + '</li>'
            + '<li><span class="text">Register Date:</span>  ' + agent.registerDate + '</li>'
            + '</ul><input type="hidden" id="delete-agent-id" value="'+agent.id+'">';

        var agentselect = data.agentSelectBox;

        
        result += '<div class="form-group">'
            + '<textarea class="form-control" id="reason-delete" rows="3" required="required"  placeholder="Enter reasons why Agent is blocked"></textarea>'
            + '</div> <div class="update-info-form-wrap">';
        if(agentselect) {    
            result += '<div class="update-info-form">'
                + '<div class="mb-20"><strong>REASIGNING DOCTORS TO ANOTHER AGENTS</strong></div>';
            $.each(doctors, function(index, value){
                result += '<input type="hidden" class="delete-doctor" value="'+value.id+'">';
                result += '<div class="form-group mb-0">'
                    + '<label for="inputName">Assign <strong>Dr '+value.name+'</strong> to:</label>';
                var agentinfo = agentselect.replace('id="admin_select_agent_agentId"','id="admin_select_agent_'+value.id+'"');
                agentinfo = agentinfo.replace('name="admin_select_agent[agentId]"','name="admin_select_agent[agentId]['+value.id+']"');
                result += agentinfo;
                result += '</div>';
            });     
            result += '</div>';
        } else {
             result += '<div class="update-info-form"><div class="mb-20">All doctors will be re-assigned to the main agent</div></div>'
        }
          
    
        result += '<a href="javascript:void(0);"  data-dismiss="modal" class="btn btn-ahalf-circle text-uppercase grey-dark btn-sm">Cancel</a>'
            + '<a href="javascript:void(0);" class="btn btn-ahalf-circle text-uppercase red btn-sm ml-10 btn-delete" >Delete</a>'
            + '</div></form>'
        $('#modal-delete-agent').find('.modal-body').html(result);
        this.initeventDeletForm(id,doctors,agentselect);
        
    },

    initeventDeletForm: function(id,doctors, agentselect){
        $("#"+id).validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                $("select[id^=admin_select_agent_]").each(function (i, e) {
                    var tag = $e.parent();
                    tag.append(error);
                    
                });
               
                if($e.attr('id') == 'reason-delete'){
                    var tag = $e.parent();
                    tag.append(error);
                }
        
            },
            submitHandler: function (form) {
                DataTable.executeDeleteAgent(form);
            }
        });
        if(doctors.length > 0 && agentselect.length > 0 ) {
            $.each(doctors,function(index,value){
                $("#admin_select_agent_" + value.id ).select2({                   
                    width: '100%'
                });
//                console.log(value.id);
                $("#admin_select_agent_" + value.id ).rules( "add", {
                    empty: true
                });
                
            });
        }
        $("#"+id).find(".btn-delete").first().on('click', function(){
            $("#"+id).submit();
        });
                
        
    },
    
    executeDeleteAgent: function(form){
        var data = {};
        data.id =  $(form).find('#delete-agent-id').val();
        data.note =  $(form).find('#reason-delete').val();
        data.doctor = {};
        $.each($(form).find('.delete-doctor'), function(index,element){
            var vl = element.value;
            data.doctor[vl] = $("#admin_select_agent_"+element.value).val();
           
        });
     
        ajaxUrl = $("#view-doctor-url").val();
        dataPost = {'data': data, 'type': 4};
        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: dataPost,
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                var succ = data.success;
                console.log(succ);
                if(succ)
                {
                    $('#modal-delete-agent').find('.modal-body').html('');
                    $("#modal-delete-agent").modal('hide');
                    DataTable.getData();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus) {
                DataTable.currentSearch = false;
            },

        });
    },
    
    generateViewDoctor: function (result) {
        var data = result['data'];
        var type = result['type'];
        var agent = data['agent'];
        var doctors = data['doctors'];
        var result = '';
        result += '<ul class="update-info-detail mb-0">'
                + '    <li><span class="text">Code:</span> ' + agent.code + '</li>'
                + '    <li><span class="text">Agent Name:</span> ' + agent.name + '</li>'
                + '    <li><span class="text">Register Date:</span> ' + agent.date + '</li>'
                + '</ul>';
        if (type == 1) {
            result += '<div class="scroller pl-0" style="height:280px" data-always-visible="1" data-rail-visible="1" data-rail-color="white" data-handle-color="green">'
                    + '  <div class="table-scrollable table-scrollable-borderless mt-0">'
                    + '    <table class="table table-hover">'
                    + '      <thead>'
                    + '        <tr>'
                    + '          <th>Doctor Code</th>'
                    + '          <th>Doctor Name</th>'
                    + '          <th>Registration Date</th>'
                    + '        </tr>'
                    + '      </thead>'
                    + '      <tbody>';
            if (doctors.length > 0) {
                $.each(doctors, function (index, value) {
                    result += '<tr>'
                            + '<td>' + value.code + '</td>'
                            + '<td>' + value.name + '</td>'
                            + '<td>' + value.registerDate + '</td>'
                            + '</tr>';
                });
            } else  {
                result += '<tr>'
                        + '<td colspan="3">Do not have any record</td>'

                        + '</tr>';
            }

            result += '</tbody>'
                    + '    </table>'
                    + '  </div>'
                    + '</div>';
        } else   {
            result += '<div class="scroller pl-0" style="height:280px" data-always-visible="1" data-rail-visible="1" data-rail-color="white" data-handle-color="green">'
                    + '  <div class="table-scrollable table-scrollable-borderless mt-0">'
                    + '    <table class="table table-hover">'
                    + '      <thead>'
                    + '        <tr>'
                    + '          <th>Agent Code</th>'
                    + '          <th>Agent Name</th>'
                    + '          <th>Doctor Code</th>'
                    + '          <th>Doctor Name</th>'
                    + '          <th>Registration Date</th>'
                    + '        </tr>'
                    + '      </thead>'
                    + '      <tbody >';
            if (doctors.length > 0)  {
                $.each(doctors, function (index, value) {
                    result += '<tr>'
                            + '<td>' + value.agentCode + '</td>'
                            + '<td>' + value.agentName + '</td>'
                            + '<td>' + value.code + '</td>'
                            + '<td>' + value.name + '</td>'
                            + '<td>' + value.registerDate + '</td>'
                            + '</tr>';
                });
            } else {
                result += '<tr>'
                        + '<td colspan="5">Do not have any record</td>'

                        + '</tr>';
            }

            result += '</tbody>'
                    + '    </table>'
                    + '  </div>'
                    + '</div>';
        }


        $('#modal-view-doctors').find('.modal-body').html(result);
        this.autoscroll($('#modal-view-doctors .scroller '));




    },

    autoscroll: function (input)  {
        var height;
        var isRTL = false;

        if (input.attr("data-height")) {
            height = input.attr("data-height");
        } else {
            height = input.css('height');
        }

        input.slimScroll({
            allowPageScroll: false, // allow page scroll when the element scroll is ended
            size: '7px',
            color: (input.attr("data-handle-color") ? input.attr("data-handle-color") : '#bbb'),
            wrapperClass: (input.attr("data-wrapper-class") ? input.attr("data-wrapper-class") : 'slimScrollDiv'),
            railColor: (input.attr("data-rail-color") ? input.attr("data-rail-color") : '#eaeaea'),
            position: isRTL ? 'left' : 'right',
            height: height,
            alwaysVisible: (input.attr("data-always-visible") == "1" ? true : false),
            railVisible: (input.attr("data-rail-visible") == "1" ? true : false),
            disableFadeOut: true
        });
    },

    generateView: function (data)
    {

        var arrayLength = data.length;
        var result = '';
        var path = $("#edit-path").val();
		var loginPath = $("#login-agent-path").val();
   
        for (var i = 0; i < arrayLength; i++)  {
            var curPath = path.replace('id', data[i].hashId);
			var curLoginPath = loginPath.replace('id', data[i].hashId);
     
            result += '<tr role="row" class="row-item rowItem">'
                    + '<td><a class="btn btn-circle green-seagreen btn-xs row-item-btn rowItemBtn" href="javascript:;"><i class="fa fa-plus"></i></a>' + data[i].code + '</td>'
                    + '<td>' + data[i].name + '</td>'
                    + '<td> ' + data[i].registerDate + '</td>'
                    + '<td> ' + data[i].email + '</td>'
                    + '<td>'
                    + '<div class="status-wrapper">';
            if (DataTable.agentStatus != -1) {
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
            if (DataTable.agentStatus != -1) {
                result += '<a href="' + curPath + '" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-xs">Edit <i class="fa fa-edit"></i></a>';
                result += '<a data-toggle="modal" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-xs view-doctor" data-id="' + data[i].id + '">View Doctors <i class="fa fa-user"></i></a>';
				result += '<a href="' + curLoginPath + '" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-xs">Login <i class="fa fa-user"></i></a>';
                if (data[i].child == 0) {
                    result += '<a  data-id="' + data[i].id + '" data-toggle="modal" class="btn btn-ahalf-circle text-uppercase red btn-icon-right btn-xs btn-delete delete-agent" data-id="' + data[i].id + '">Delete</a>';
                }
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
                    + '</tr>';
            result += '<tr class="row-item-expand hide-item rowItemExpand"><td colspan="6"><div class="row">';
            result += '<div class="col-md-5"><div class="item-expand-wrap text-left">'
                    + '</div></div>';
            result += setupPart;
            result += '</div></td></tr>';

        }

        if (result == '') {
            result = '<tr role="row"><td colspan="6">Have no record in result  </td> </tr>';
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
        $(this.infoTag).html("Showing " + start + " to " + end + " of " + total + " entries")
    }
});

$(document).ready(function () {
    jQuery.validator.addMethod("empty", function (value, element) {
        return value != 'empty';
    }, "This field is required");
    DataTable.init();
    DataTable.getData();
});