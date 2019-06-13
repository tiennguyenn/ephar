
$patientSuggestBox = $('#patient_auto_suggest_txb');
$doctorSuggestBox  = $('#doctor_auto_suggest_txb');
$orderSuggestBox  = $('#order_auto_suggest_txb');
$updateFilterBtn   = $('.btn-update-filter');
$currentSortInfoHf = $('#current_sort_info');

var rxReportJs = {
    init: function() {
        rxReportJs.setDefaultDateTime();
        jsCommon.pagingAjax();
        this.search();
        this.showOrderDetail();
    },
    setDefaultDateTime: function () {
        var start = moment().subtract(29, 'days');
        var end = moment();
        function cb(start, end) {
            $("#frm-rx-report-filter").find('input[name="from_date"]').val(start.format('YYYY-MM-DD'));
            $("#frm-rx-report-filter").find('input[name="to_date"]').val(end.format('YYYY-MM-DD'));
            $('#reportrange1 span, #reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange1, #reportrange2').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
    },
    showOrderDetail: function() {
        $(document).on('click', '.rowItemBtn i', function () {
            var $_that = $(this);
            closestTrEl = $_that.closest('tr')
            url = $_that.attr('url');
            if ($_that.attr('loaded') != 'true') {
                url = $_that.data('url');
                $.ajax({
                    url: url,
                    method: "GET",
                    dataType: "html",
                    success: function (res) {
                        $_that.closest('tr').after(res);
                        $_that.attr('loaded', 'true');
                    },
                    error: function (error) {
                        alert("Error");
                    }
                });
            }
        });

        $(document).on('click', '.rowItemLink', function () {
            var $_that = $(this).find('i');
            var _parent = $(this).closest('.rowItem');
            if (_parent.hasClass('open')) {
                _parent.next('.rowItemExpand').slideUp();
                _parent.removeClass('open');
            } else {
                if ($_that.attr('loaded') != 'true') {
                    $_that.attr('loaded', 'true');
                    var url = $_that.data('url');
                    $.ajax({
                        url: url,
                        method: "GET",
                        dataType: "html",
                        success: function (res) {
                            _parent.after(res);
                            _parent.addClass('open');
                            _parent.next('.rowItemExpand').slideDown();
                        },
                        error: function (error) {
                            $_that.attr('loaded', 'false');
                        }
                    });
                } else {
                    _parent.addClass('open');
                    _parent.next('.rowItemExpand').slideDown();
                }
            }
        });
    },
    search: function(){
        var df = $("#frm-rx-report-filter");
        var dc = $("div.content-table.doctor-container");

        //on action
        df.on('click','#rx_report_filter_status > a', function(e){
            e.preventDefault();
            if(!$(this).hasClass('active')){
                $(this).closest('div').find('a').removeClass('active');
                $(this).addClass('active');
                $(this).closest('div').find('input').val($(this).data('value'));

                $data =  {};
                $data.patientType = $(this).data('patienttype');
                if ($patientSuggestBox.data('searched') == true ) {
                    $data.patientTerm  = $patientSuggestBox.val();
                }

                if ($doctorSuggestBox.data('searched') == true ) {
                    $data.doctorTerm  = $doctorSuggestBox.val();
                }
				
                if ($orderSuggestBox.data('searched') == true ) {
                    $data.orderTerm  = $orderSuggestBox.val();
                }
				
                rxReportJs.getData($data);
            }


        }).on('change','#rx_report_filter_per_page', function(e){ // filter by perpage
            e.preventDefault();
            $data = rxReportJs.buildFiler($(this));

            rxReportJs.getData($data);
        }).on('click','.btn-update-filter', function(e){ // click on Update search result button
            e.preventDefault();
            $(this).data('searched', true);
            $patientSuggestBox.data('searched', true);
            $doctorSuggestBox.data('searched', true);
			$orderSuggestBox.data('searched', true);

            $data = rxReportJs.buildFiler($(this));
            rxReportJs.getData($data);
        }).on('click','.btn-clear-filter', function(e){
            $patientSuggestBox.data('searched', false);
            $doctorSuggestBox.data('searched', false);
			$orderSuggestBox.data('searched', false);
            $updateFilterBtn.data('searched', false)
            $patientSuggestBox.val('');
            $doctorSuggestBox.val('');
			$orderSuggestBox.val('');
            $data.patientType = $('#rx_report_filter_status a.active').removeClass('active');
            $data.patientType = $('#rx_report_filter_status a').first().addClass('active');
            $('#doctor_fee_gte').val('');
            $('#doctor_fee_lte').val('');
            rxReportJs.setDefaultDateTime();
            
            rxReportJs.getData({});
        }).on('click','.btn-download', function(e){
            e.preventDefault();
            $data = rxReportJs.buildFiler($(this));
            var filename = 'Report from '+ $data.fromDate +' to '+ $data.toDate + '.csv';
            jsCommon.downloadCSV($('#url_ajax_download_sales_report').val(), $data, filename);
        }).on('keydown', '#patient_auto_suggest_txb, #doctor_auto_suggest_txb, #order_auto_suggest_txb', function(e){
            var $_that = $(this);
            if(e.keyCode == 13){
                $_that.data('searched', true);
                $data = {};
				if ($_that.hasClass('patient')) {
					$data.patientTerm = $.trim($_that.val());
				} else if ($_that.hasClass('doctor')) {
					$data.doctorTerm = $.trim($_that.val());
				} else if ($_that.hasClass('order')) {
					$data.orderTerm = $.trim($_that.val());
				}
                $data.patientType = $('#rx_report_filter_status a.active').data('patienttype');
                rxReportJs.getData($data);
            }
        });
        //on sorting
        dc.on('click', 'th.sorting', function(e){
            e.preventDefault();

            $data = rxReportJs.buildFiler($(this));
            var colName = $(this).data('colname');
            if($(this).hasClass('sorting_asc')){
                $(this).removeClass('sorting_asc');
                $(this).addClass('sorting_desc');
                $currentSortInfoHf.data('direction', 'desc');
                df.find("input[name='rx_report_filter[sorting]']").val(colName+'_desc');
            } else {
                $(this).removeClass('sorting_desc');
                $(this).addClass('sorting_asc');
                $currentSortInfoHf.data('direction', 'asc');
            }
            $currentSortInfoHf.data('column', colName);

            // $data = {};
            $sortInfo = $currentSortInfoHf.data();
            if ($sortInfo.column != '') {
                $data.sortInfo = $sortInfo;
            }

            rxReportJs.getData($data);
        });

        $(document).on('click', '.auto-suggest', function () {
            var $_that = $(this);
            
            $txb = $_that.next().children('input').next();
            $txb.data('searched', true);


            $data = {};
            if ($txb.hasClass('patient')) {
                $data.patientTerm = $.trim($txb.val());
            } else if ($txb.hasClass('doctor')) {
                $data.doctorTerm = $.trim($txb.val());
            } else if ($txb.hasClass('order')) {
                $data.orderTerm = $.trim($txb.val());
            }
            $data.patientType = $('#rx_report_filter_status a.active').data('patienttype');
            
            rxReportJs.getData($data);
        })
        jsCommon.suggestion($("#url_suggestion_search").val(), {term: $patientSuggestBox.val() }, undefined, $patientSuggestBox);
        jsCommon.suggestion($("#url_suggestion_search").val(), {term: $doctorSuggestBox.val(), personType: 'doctor' }, undefined, $doctorSuggestBox);
		jsCommon.suggestion($("#url_suggestion_search").val(), {term: $orderSuggestBox.val(), personType: 'order' }, undefined, $orderSuggestBox);
    },
    buildFiler: function ($elObj) {
        $data    = {};
        fromDate = $("[name=from_date]").val();
        toDate   = $("[name=to_date]").val();
        if (fromDate != '') {
            $data.fromDate = fromDate;
        }
        if (toDate != '') {
            $data.toDate = toDate;
        }

        orderValueGte = $('#order_value_gte').val();
        orderValueLte = $('#order_value_lte').val();
        if ($.trim(orderValueGte) != '') {
            $data.orderValueGte = orderValueGte;
        }
        if ($.trim(orderValueLte) != '') {
            $data.orderValueLte = orderValueLte;
        }
        $data.patientTerm = $.trim($patientSuggestBox.val());
        $data.doctorTerm  = $.trim($doctorSuggestBox.val());
		$data.orderTerm  = $.trim($orderSuggestBox.val());
        $data.countryCode = $('#country_dropdown').val();

        $data.patientType = $('#rx_report_filter_status a.active').data('patienttype');
        $data.perPage = $('#rx_report_filter_per_page').val();

        if ($elObj.hasClass('sorting') || $elObj.attr('id') == 'rx_report_filter_per_page') {
            if ($patientSuggestBox.data('searched') == false) {
                delete $data.patientTerm;
            }
            if ($patientSuggestBox.data('searched') == false) {
                delete $data.doctorTerm;
            }
            if ($orderSuggestBox.data('searched') == false) {
                delete $data.orderTerm;
            }
			
            if ($updateFilterBtn.data('searched') == false ) {
                delete $data.toDate;
                delete $data.fromDate;
                delete $data.doctorFeeLte;
                delete $data.doctorFeeGte;
            }
        }

        if ($elObj.hasClass('btn-download')) {
            delete $data.perPage;
        }

        return $data;
    },
    getData: function ($data) {
        successCallback = function (res) {
            $("div.content-table.doctor-container" ).html(res);
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };
        jsDataService.callAPI($("#url_doctor_report_transaction_history").val(), $data, "POST", successCallback, errorCallback, null, 'html');
    }
};

$(document).ready(function() {
    rxReportJs.init();
});