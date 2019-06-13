var DataTable = $.extend(Base, {
    tableLength: 10,
    tableData: '',
    currentPage: 1,
    ajaxUrl: $("#ajax-url").val(),
    bodyTag: '#table-body-delivery',
    pagingTag: '#list-table-pagin',
    infoTag: "#list-tables-info",
    tableLenthTag: '#table-length',
    tableSearchTag: '#table-search-data',
    tableId: '#sample_1',
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

    init: function () {
        this.suggestion($("#auto-complete-url").val(), {}, 'name');
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
        $(this.tableId + ' th').on('click', function () {
            var attr = $(this).attr(DataTable.attrSort);
            if(typeof attr !== typeof undefined && attr !== false) {
                DataTable.changeSort($(this));
                DataTable.getData();
            }
        });
        this.initEditableForm();

        $('.date-picker').datepicker('remove');
        $('.date-picker').datepicker({
            startDate: "+1d",
            rtl: App.isRTL(),
            orientation: "left",
            autoclose: true,
            format: 'd M yy'
        });
        $('#modal-update-patient-change').on('hidden.bs.modal', function () {

            if( DataTable.currentParentRate != '' ) {
                DataTable.currentParentRate.removeClass('editable-unsaved');
              //  DataTable.currentParentRate.editable('setValue',"changed value");
            }
            DataTable.resetEditTableFormData();
        });
        $('#modal-delete').on('hidden.bs.modal', function () {
            DataTable.currentDeleteDelivery = '';

        });
        $("#delete-delivery").on('click', function () {
            var dataPost = {'id': DataTable.currentDeleteDelivery, 'type': 5};
            var ajaxUrl = $("#update-delivery-url").val();
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: dataPost,
                success: function (data, textStatus, jqXHR) {
                   $('#modal-delete').modal('hide');
                   DataTable.getData();
                }
            });

        });
        this.viewLogs();
    },

    getData: function () {
        this.dataPost = {'search': this.tableData, 'length': this.tableLength, 'page': this.currentPage, 'sort': this.dataSort};
        $.ajax({
            type: "POST",
            url: this.ajaxUrl,
            data: this.dataPost,
            beforeSend: function () {
                $(DataTable.bodyTag).html('<tr role="row"><td colspan="7" style="text-align: center;"> Loading...</td></tr>');
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
            }

        });
    },

    initEventTable: function () {
        $('.editableInline').editable({
            type: 'text',
            mode: 'inline',
            name: 'data',

            success: function(response, newValue) {
                return DataTable.confirmdata(this, newValue);
            }
        });
        $('.editablePopup').editable({
            type: 'text',
            value: function(){

               var text = $(this).parents('th').first().find('a').first().html();
               return text.replace('%', '');
            },
            success: function(response, newValue) {
                 return DataTable.confirmdata(this, newValue);
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
        $('.btn-delete-courier').on('click', function(){
           DataTable.currentDeleteDelivery = $(this).attr('cus-data-id')
        });
    },

    generateView: function (data)
    {

        var arrayLength = data.length;
        var result = '';
        var path = $("#edit-path").val();
        for (var i = 0; i < arrayLength; i++)  {
            var curPath = path.replace('id', data[i].hashId);
            result += '<tr role="row" class="row-item rowItem">'
            + '<td><a href="javascript:void(0);" class="rowItemBtn btn btn-circle green-seagreen btn-xs row-item-btn"><i class="fa fa-plus"></i></a> '+data[i].name+'</td>'
            + '<td> '+data[i].registerDate+'</td>'
            + '<td>'+data[i].email+'</td>'
            + '<td>'+data[i].phone+'</td>'
            + '<td>'+data[i].country+'</td>'
            + '<td>'+data[i].state+'</td>'
            + '<td>'
            + '<a href="'+curPath+'" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-xs btn-icon-right">Edit <i class="fa fa-edit"></i></a>'
            + '<a href="#modal-delete" data-toggle="modal" cus-data-id="'+data[i].id+'"  class="btn btn-ahalf-circle text-uppercase red btn-icon-right btn-xs btn-delete-courier">Delete</a>'
            + '</td>'
            + '</tr>'
            + '<tr class="hide-item rowItemExpand sub-table">'
            + '<td colspan="7" class="align-left">'
            + '<h5 class="sub-table-title mt-0 mb-0">Normal Deliveries</h5>'
            + '<table class="table table-hover table-head-bordered mb-10">'
            + '<thead>'
            + '<tr>'
            + '<th class="align-left pl-40 span20p">Locations</th>'
            + '<th class="align-right span20p">Westmead Rate <br> (SGD)</th>'
            + '<th class="align-right span30p">\n\
                <a href="javascript:;" class="editablePopup mr-10" data-original-title="Change Percentage" cus-data-type="4" \n\
                cus-data-title = "Shipping Rate Charged To Patient Change" \n\
                cus-data-message = "This will recalculate Shipping Rate Charged to Patient for all locations" \n\
                cus-data-id="'+data[i].id+'" \n\
                cus-data-label = "Percentage change <br/> will  take effect on:"\n\
                cus-data-value="'+ data[i].margin +'">' + number_format(data[i].margin, 2, '.', ',') + '%</a>';

            if(data[i].effectDate.length > 0) {
                result += this.renderNote(number_format(data[i].newMargin, 2, '.', ',')+ '%', data[i].effectDate);
            }

            result += ' Shipping Rate Charged <br> to Patient (SGD)</th>'
            + '<th>Collection and Destruction Rates (SGD)</th>'
            + '<th class="align-right span20p editable-right">IG Permit Fee <br> (SGD)</th>'
            + '<th class="align-right span10p">Estimated Delivery</th>'
            + '</tr>'
            + '</thead>'
            + '<tbody>';
            var rates = data[i]['rate_data'];
            if(rates.length > 0) {
                for (var j = 0; j < rates.length; j++) {
                    result += '<tr>'
                        + '<td class="span20p align-left pl-40">'+rates[j].name+'</td>'
                        + '<td class="span20p text-right">' +
                        '<a href="javascript:;" class="editableInline" cus-data-type="1" cus-data-label="Price change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' '+ data[i].name + ' rate change" cus-data-message="This will change the ' + rates[j].name + ' ' + data[i].name + ' Rate" mes cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].rate+'">' + number_format(rates[j].rate, 2, '.', ',');

                    if( rates[j].rateEffectDate.length ) {
                        result += this.renderNote(number_format(rates[j].newRate, 2, '.', ','), rates[j].rateEffectDate);
                    }

                    result += '</a></td>'

                        + '<td class="span30p text-right"><a href="javascript:;" class="editableInline" cus-data-type="2" cus-data-label="Price change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' Shipping Rate Charged to Patient" cus-data-message="This will change the ' + rates[j].name + ' Shipping Rate Charged to Patient" cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].shippingRate+'">' + number_format(rates[j].shippingRate, 2, '.', ',');
                    if( rates[j].shippingRateEffectDate.length ) {
                        result += this.renderNote(number_format(rates[j].newShippingRate, 2, '.', ','), rates[j].shippingRateEffectDate);
                    }
                    result += '</a></td>';

                    result += '<td class="span20p align-right">' +
                        '<a href="javascript:;" class="editableInline" cus-data-type="7" cus-data-label="Price change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' Collection and Destruction Rates" cus-data-message="This will change the ' + rates[j].name + ' Collection and Destruction Rates" cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].CDRate+'">'+ number_format(rates[j].CDRate, 2, '.', ',') ;
                    if( rates[j].CDRateEffectDate.length ) {
                        result += this.renderNote(number_format(rates[j].newCDRate, 2, '.', ','), rates[j].CDRateEffectDate);
                    }
                    result += '</a></td>';

                    if(rates[j].sg) {
                        result += '<td class="span20p align-right"><a href="javascript:;" class="editableInline" cus-data-type="3" cus-data-label="Price change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' IG Permit Fee " cus-data-message="This will change the ' + rates[j].name + ' IG Permit Fee" cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].igFee+'">'+ number_format(rates[j].igFee, 2, '.', ',') ;
                        if( rates[j].igFeeEffectDate.length ) {
                            result += this.renderNote(number_format(rates[j].newIgFee, 2, '.', ','), rates[j].igFeeEffectDate);
                        }
                        result += '</a></td>';
                    } else {
                        result += '<td></td>';
                    }
                    result += '<td class="span10p align-right"><a href="javascript:;" class="editableInline" cus-data-type="6" cus-data-label="Estimated delivery change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' Estimated Delivery " cus-data-message="This will change the ' + rates[j].name + ' Estimated Delivery" cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].estDelivery+'">'+ rates[j].estDelivery +'</a></td>';
                    result += '</tr>';
                }
            }

            result += '</tbody>'
            + '</table>'
            + '<h5 class="sub-table-title mt-0 mb-0">Cold Chain Deliveries</h5>'
            + '<table class="table table-hover table-head-bordered mb-10">'
            + '<thead>'
            + '<tr><th>Cold Chain</th></tr>'
            + '<tr>'
            + '<th class="align-left pl-40 span20p">Locations</th>'
            + '<th class="align-right span20p">Westmead Rate <br> (SGD)</th>'
            + '<th class="align-right span30p">\n\
                <a href="javascript:;" class="editablePopup mr-10" data-original-title="Change Percentage" cus-data-type="4" \n\
                cus-data-title = "Shipping Rate Charged To Patient Change" \n\
                cus-data-message = "This will recalculate Shipping Rate Charged to Patient for all locations" \n\
                cus-data-id="'+data[i].id+'" \n\
                cus-data-label = "Percentage change <br/> will  take effect on:"\n\
                cus-data-value="'+ data[i].margin +'">' + number_format(data[i].margin, 2, '.', ',') + '%</a>';

            if(data[i].effectDate.length > 0) {
                result += this.renderNote(number_format(data[i].newMargin, 2, '.', ',')+ '%', data[i].effectDate);
            }

            result += ' Shipping Rate Charged <br> to Patient (SGD)</th>'
            + '<th>Collection and Destruction Rates (SGD)</th>'
            + '<th class="align-right span20p editable-right">IG Permit Fee <br> (SGD)</th>'
            + '<th class="align-right span10p">Estimated Delivery</th>'
            + '</tr>'
            + '</thead>'
            + '<tbody>';
            var rates = data[i]['cold_chain_rate_data'];
            if(rates.length > 0) {
                for (var j = 0; j < rates.length; j++) {
                    result += '<tr>'
                        + '<td class="span20p align-left pl-40">'+rates[j].name+'</td>'
                        + '<td class="span20p text-right">' +
                        '<a href="javascript:;" class="editableInline" cus-data-type="1" cus-data-label="Price change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' '+ data[i].name + ' rate change" cus-data-message="This will change the ' + rates[j].name + ' ' + data[i].name + ' Rate" mes cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].rate+'">' + number_format(rates[j].rate, 2, '.', ',');

                    if( rates[j].rateEffectDate.length ) {
                        result += this.renderNote(number_format(rates[j].newRate, 2, '.', ','), rates[j].rateEffectDate);
                    }

                    result += '</a></td>'

                        + '<td class="span30p text-right"><a href="javascript:;" class="editableInline" cus-data-type="2" cus-data-label="Price change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' Shipping Rate Charged to Patient" cus-data-message="This will change the ' + rates[j].name + ' Shipping Rate Charged to Patient" cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].shippingRate+'">' + number_format(rates[j].shippingRate, 2, '.', ',');
                    if( rates[j].shippingRateEffectDate.length ) {
                        result += this.renderNote(number_format(rates[j].newShippingRate, 2, '.', ','), rates[j].shippingRateEffectDate);
                    }
                    result += '</a></td>';

                    result += '<td class="span20p align-right">' +
                        '<a href="javascript:;" class="editableInline" cus-data-type="7" cus-data-label="Price change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' Collection and Destruction Rates" cus-data-message="This will change the ' + rates[j].name + ' Collection and Destruction Rates" cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].CDRate+'">'+ number_format(rates[j].CDRate, 2, '.', ',') ;
                    if( rates[j].CDRateEffectDate.length ) {
                        result += this.renderNote(number_format(rates[j].newCDRate, 2, '.', ','), rates[j].CDRateEffectDate);
                    }
                    result += '</a></td>';

                    if(rates[j].sg) {
                        result += '<td class="span20p align-right"><a href="javascript:;" class="editableInline" cus-data-type="3" cus-data-label="Price change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' IG Permit Fee " cus-data-message="This will change the ' + rates[j].name + ' IG Permit Fee" cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].igFee+'">'+ number_format(rates[j].igFee, 2, '.', ',') ;
                        if( rates[j].igFeeEffectDate.length ) {
                            result += this.renderNote(number_format(rates[j].newIgFee, 2, '.', ','), rates[j].igFeeEffectDate);
                        }
                        result += '</a></td>';
                    } else {
                        result += '<td></td>';
                    }
                    result += '<td class="span10p align-right"><a href="javascript:;" class="editableInline" cus-data-type="6" cus-data-label="Estimated delivery change will <br/> take effect on:" cus-data-title="'+ rates[j].name+' Estimated Delivery " cus-data-message="This will change the ' + rates[j].name + ' Estimated Delivery" cus-data-id="'+rates[j].id+'" cus-data-value="'+rates[j].estDelivery+'">'+ rates[j].estDelivery +'</a></td>';
                    result += '</tr>';
                }
            }

            result += '</tbody>'
            + '</table>'
            + '</td>'
            + '</tr>';
        }

        if (result == '') {
            result = '<tr role="row"><td colspan="7">Have no record in result  </td> </tr>';
        }
        $(this.bodyTag).html(result);
    },

    confirmdata: function (el, newValue) {
        DataTable.currentType = '';
        DataTable.currentRateId = '';
        DataTable.currentRateData ='';
        var type = $(el).attr('cus-data-type');
        var id = $(el).attr('cus-data-id');
        var data = number_format($(el).attr('cus-data-value'), 2, '.', ',');
        if (type == 6) {
            data = $(el).attr('cus-data-value');
        }
        if(typeof type !== typeof undefined && type !== false) {
            DataTable.currentType = type;
        }
        if(typeof id !== typeof undefined && id !== false) {
            DataTable.currentRateId = id;
        }
        if(typeof data !== typeof undefined && data !== false) {
            DataTable.currentRateData = data;
        }
        newValue = newValue.replace('%','');
        DataTable.updateRateData = number_format(newValue, 2, '.', ',');
        var retVal = number_format(data, 2, '.', ',');
        if(type == 4 ) {
            DataTable.updateRateData  += '%';
            DataTable.currentRateData += '%';
            retVal += '%';
        }
        if (type == 6) {
            var patt = /[0-9]+-[0-9]+/;
            if (!patt.test(newValue)) {
                return false;
            }
            var sep = newValue.split('-', 2);
            if (sep[0] >= sep[1]) {
                return false;
            }

            DataTable.updateRateData = newValue;
            retVal = newValue;
            $('#effectDateContainer').hide();
        } else {
            $('#effectDateContainer').show();
        }

        DataTable.currentParentRate = $(el);
        var updateVal = DataTable.updateRateData;

        $("#modal-update-patient-change").find('#cus-modal-title').first().html($(el).attr('cus-data-title'));
        $("#modal-update-patient-change").find('#cus-note-mesage').first().html($(el).attr('cus-data-message'));
        $("#modal-update-patient-change").find('#label-for-input').first().html($(el).attr('cus-data-label'));
        $("#modal-update-patient-change").find('#origin-value').first().html(DataTable.currentRateData);
        $("#modal-update-patient-change").find('#update-value').first().html(updateVal.replace('',''));
        $("#modal-update-patient-change").find('#date-effect').first().val('');
        $("#modal-update-patient-change").modal({backdrop: 'static', keyboard: false});
        $("#modal-update-patient-change").modal('show');
        return {'newValue': retVal};
    },

    initEditableForm: function(){
        $('#modal-update-patient-change').find('#btn-cancel-update').on('click',function(){
            DataTable.currentParentRate.removeClass('editable-unsaved');
           // DataTable.resetEditTableFormData();
            $("#modal-update-patient-change").modal('toggle');
        });
        var validobj = $('#modal-update-patient-change').find('form').validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("id")) {

                    case 'date-effect':
                        $e.parents('.wrap').first().append(error);
                        break;

                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            submitHandler: function (form) {
                DataTable.updateDeliveryData();
            }


        });
        $('#modal-update-patient-change').find('#btn-submit-update').on('click',function(){
            $('#modal-update-patient-change').find('form').submit();
        });
    },

    resetEditTableFormData: function() {

        DataTable.currentType = '';
        DataTable.currentRateId = '';
        DataTable.currentRateData ='';
        DataTable.updateRateData = '';
        DataTable.currentParentRate = '';

    },

    updateDeliveryData: function(){
        var updateVal = DataTable.updateRateData;
        var date =  $('#modal-update-patient-change').find('#date-effect').first().val();
        DataTable.currentParentRate.editable('submit', {
            url: $("#update-delivery-url").val(),
            data: {id:DataTable.currentRateId, type: DataTable.currentType, value: updateVal.replace('%', ''), date: date},
            success: function(data) {
                if(data.success) {
                    this.removeClass('editable-unsaved');
                    this.parent().find('.icon-tooltip').remove();
                    var updateVal = DataTable.updateRateData;
                    if (6 != this.attr('cus-data-type')) {
                        $(DataTable.renderNote(updateVal, date)).insertAfter(this);
                    }
                    this.parent().find('[data-toggle="tooltip"]').tooltip();
                    $("#modal-update-patient-change").modal('hide');
                }
            }
        });

    },

    renderNote:function(newValue, date){
        return '<a href="javascript:;" data-toggle="tooltip" title="New value '+ newValue + ' will be effective on '+date+'" class="icon-tooltip"><i class="fa fa-exclamation-circle"></i></a>';
    },

    suggestion: function(url, data, display_name) {
        var tag = $(this.autoClass);
        if(tag.length > 0) {
            data = typeof data != 'undefined' ? data : {};
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
    viewLogs: function () {
        $('#view_logs_delivery_partner').on('click', function () {
            $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
            $.ajax({
                type: "POST",
                url: $('#url_delivery_partner_log').val(),
                data: {module: 'delivery_partner'},
                beforeSend: function( ) {
                    $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
                },
                success: function(response){
                    $("#list-logs").html(response);
                    $('#modal-view-logs').modal();
                },
                complete: function(data){
                }
            });
        });
    }
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
