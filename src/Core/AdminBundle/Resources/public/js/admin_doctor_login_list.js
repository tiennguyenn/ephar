var DataTable = $.extend(Base, {
    tableLength: 10,
    tableData: '',
    currentPage: 1,
    ajaxUrl: $("#ajax-url").val(),
    bodyTag: '#table-body-doctor',
    pagingTag: '#list-table-pagin',
    infoTag: "#list-tables-info",
    tableLenthTag: '#table-length',
    tableSearchTag: '#table-search-data',
    tableId: '#sample_1',
    doctorLoginStatus: 2,
    currentDeleteDoctorLogin: '',
    dataSort: {},
    dataPost: {},
    currentSearch: false,
    doctorId: $('#doctor-id').val(),
    maxPage : 0 ,

    init: function () {
        this.suggestion(DataTable.ajaxUrl, {id: DataTable.doctorId}, 'name');
        $(this.tableSearchTag).keyup(function (event) {
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
            DataTable.doctorLoginStatus = $(this).attr('data');
            DataTable.currentPage = 1;
            DataTable.getData();
            DataTable.suggestion(DataTable.ajaxUrl, {id: DataTable.doctorId}, 'name');
        });

        $(this.tableId + ' th').on('click', function () {            
            var attr = $(this).attr('data-colum-sort');
            if(typeof attr !== typeof undefined && attr !== false) {
                DataTable.changeSort($(this));
                DataTable.getData();
            }
        });
		
		$("#delete-yes").on('click', function() {
			DataTable.deleteLogin(this);
		});
		
		setTimeout(function(){
			$(".alert").remove();
		}, 5000);

        var isClick = false;
        $('#sample_1_wrapper').on('click', '.resendWE', function() {
            var dataId = $(this).data('id');
            var path = $("#resendwe-path").val();
            var curPath = path.replace('id', dataId);

            if(isClick == true) {
                return;
            }

            $.ajax({
                type: "POST",
                url: curPath,
                data: {'loginId': dataId},
                beforeSend: function() {
                    isClick = true;
                },
                success: function (data, textStatus, jqXHR) {
                    $('#modal-resend-email').modal('show');
                    isClick = false;
                }
            });
        });
    },
    
    suggestion: function(url, data, display_name) {
        var tag = $('.on-suggestion');
        if(tag.length > 0) {
            data = typeof data != 'undefined' ? data : {};
            data.status = this.doctorLoginStatus;
			data.role = 'doctor';
			data.action = 'get_suggestion';
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
        this.dataPost = {'search': this.tableData, 'length': this.tableLength, 'page': this.currentPage, 'status': this.doctorLoginStatus, 'sort': this.dataSort, 'id': this.doctorId, 'role': 'doctor', 'action': 'get_list'};
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
                var dataPost = {'id': this.value, 'action': 'change_status', 'role': 'doctor'};
                $.ajax({
                    type: "POST",
                    url: DataTable.ajaxUrl,
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

        $(".delete-doctor-login").on('click', function (e) {
            e.preventDefault();
			DataTable.currentDeleteDoctorLogin = $(this).attr('data-id');
			
			var email = $(this).attr('data-email');
			$("#modal-confirm-delete").find("p").html("Do you want to delete this doctor login (" + email + ")?")
			$("#modal-confirm-delete").modal("show");
        });
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
   
        for (var i = 0; i < arrayLength; i++)  {
            var curPath = path.replace('id', data[i].hashId);
            if (data[i].lastLogin != null) {
                var loginStatus = data[i].lastLogin;
            } else {
                if (data[i].passwordStatus == 'password_not_set') {
                    var loginStatus = 'Password not set up <a href="javascript:;" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-xs resendWE" data-id=' + data[i].id + '>Resend Welcome Email</a>';
                } else {
                    var loginStatus = 'Password set up completed';
                }
            }
            result += '<tr role="row" class="row-item rowItem">'
                    + '<td>' + data[i].email + '</td>'
                    + '<td>' + data[i].name + '</td>'
                    + '<td> ' + data[i].registerDate + '</td>'
                    + '<td> ' + loginStatus + '</td>'
                    + '<td>'
                    + '<div class="status-wrapper">';
            if (DataTable.doctorLoginStatus != -1) {
                if (data[i].status) {
                    result += '<input type="checkbox" value="' + data[i].id + '" class="make-switch" checked  data-on-color="success" data-off-color="danger" data-on-text="' + "<i class='fa fa-check'></i>" + '" data-off-text="' + "<i class='fa fa-times'></i>" + '"  data-size="mini" ' + (!data[i].doctorStatus ? 'disabled="" ' : '') + '>'
                            + '<div class="switch-text" >'
                            + '<span class="switch-text-on">Active</span>'
                            + '<span class="switch-text-off hide-item">Deactivated</span>';
                } else {
                    result += '<input type="checkbox" value="' + data[i].id + '" class="make-switch"  data-on-color="success" data-off-color="danger" data-on-text="' + "<i class='fa fa-check'></i>" + '" data-off-text="' + "<i class='fa fa-times'></i>" + '"  data-size="mini" ' + (!data[i].doctorStatus ? 'disabled="" ' : '') + '>'
                            + '<div class="switch-text">'
                            + '<span class="switch-text-on hide-item">Active</span>'
                            + '<span class="switch-text-off ">Deactivated</span>';
                }
            }
            result += '</div>'
                    + '</div>'
                    + '</td>'
                    + '<td>';
            if (DataTable.doctorLoginStatus != -1) {
                result += '<a href="' + curPath + '" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-icon-right btn-xs">Edit <i class="fa fa-edit"></i></a>';
                result += '<a  data-id="' + data[i].id + '" data-toggle="modal" class="btn btn-ahalf-circle text-uppercase red btn-icon-right btn-xs btn-delete delete-doctor-login" data-email="' + data[i].email + '" data-id="' + data[i].id + '">Delete</a>';
            }
            result += '</td>'
                    + '</tr>';

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
    },
	
	deleteLogin: function(btn) {
		$(btn).parent().find(".btn").attr("disabled", "disabled");
		$(btn).html("Deleting...");
		var dataPost = {"id": DataTable.currentDeleteDoctorLogin, "role": "doctor", "action": "delete"};
		$.ajax({
			type: "POST",
			url: DataTable.ajaxUrl,
			data: dataPost,

			success: function (data, textStatus, jqXHR) {
				$(btn).parent().find(".btn").removeAttr("disabled");
				$(btn).html("Yes");
				$("#modal-confirm-delete").modal("hide");
				$(".alert").remove();
				if (data.success == 1) {
					DataTable.getData();
					$("#sample_1_wrapper").before('<div class="alert alert-success">' + data.message + '</div>');
				} else {
					$("#sample_1_wrapper").before('<div class="alert alert-danger">' + data.message + '</div>');
				}
				setTimeout(function(){ $(".alert").remove() }, 5000);
			}
		});
	}
});

$(document).ready(function () {
    jQuery.validator.addMethod("empty", function (value, element) {
        return value != 'empty';
    }, "This field is required");
    DataTable.init();
    DataTable.getData();
});