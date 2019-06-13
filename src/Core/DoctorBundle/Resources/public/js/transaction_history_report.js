
$autoSuggessBox    = $('#patient_auto_suggest_txb');
$orderSuggestBox    = $('#order_auto_suggest_txb');
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
        $(document).on('click', '.rowItemBtnText', function () {
        // $('.rowItemBtnText').click(function () {
            var $_that = $(this);
            $targetTr = $_that.closest('tr').next();
            if ($_that.attr('loaded') != 'true') {
                url = $_that.data('url');
                $.ajax({
                    url: url,
                    method: "GET",
                    dataType: "json",
                    success: function (res) {
                        $(res).each(function () {
                            var $_item = $(this)[0];

                            if ($_item.lineType == 2 && $_item.hasRxReviewFee == true) {

                                if ($_item.shareFee) {
                                    $targetTr.find('#col_1').prepend("<p><strong>GMedes Admin Service Fee Per Prescription </strong></p>");

                                    if ($_item.gmedesFixed < 0) {
                                        $targetTr.find('#col_2').prepend("<p class='text-right txt-red'>" + $_item.gmedesFixed + "</p>");
                                    } else {
                                        $targetTr.find('#col_2').prepend("<p class='text-right'>" + $_item.gmedesFixed + "</p>");
                                    }
                                    $targetTr.find('#col_3').prepend("<p class='text-right'>0.00</p>");
                                }

                                $targetTr.find('#col_1').prepend("<p><strong>Doctor Fees - Prescribing Fee </strong></p>");

                                if ($_item.totalFee < 0) {
                                    $targetTr.find('#col_2').prepend("<p class='text-right txt-red'>" + $_item.costPrice + "</p>");
                                } else {
                                    $targetTr.find('#col_2').prepend("<p class='text-right'>" + $_item.costPrice + "</p>");
                                }
                                
                                if ($_item.doctorServiceFee < 0) {
                                    $targetTr.find('#col_3').prepend("<p class='text-right txt-red'>" + $_item.doctorServiceFee + "</p>");
                                } else {
                                    $targetTr.find('#col_3').prepend("<p class='text-right'>" + $_item.doctorServiceFee + "</p>");
                                }
                                
                            }

                            if ($_item.lineType == 1) {
                                $targetTr.find('#col_1').prepend("<p><strong>" + $_item.name + "</strong></p>");
                                if ($_item.totalFee < 0) {
                                    $targetTr.find('#col_2').prepend("<p class='text-right txt-red'>" + $_item.totalFee + "</p>");
                                } else {
                                    $targetTr.find('#col_2').prepend("<p class='text-right'>" + $_item.totalFee + "</p>");
                                }

                                if ($_item.doctorMedicineFee < 0) {
                                    $targetTr.find('#col_3').prepend("<p class='text-right txt-red'>" + $_item.doctorMedicineFee + "</p>");
                                } else {
                                    $targetTr.find('#col_3').prepend("<p class='text-right'>" + $_item.doctorMedicineFee + "</p>");
                                }
                                
                                
                            }
                        });

                        $_that.attr('loaded', true);
                    },
                    error: function (error) {
                        alert("Error");
                    }
                });
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
                if ($autoSuggessBox.data('searched') == true ) {
                    $data.term  = $autoSuggessBox.val();
                }
                if ($orderSuggestBox.data('searched') == true ) {
                    $data.orderTerm  = $orderSuggestBox.val();
                }
				
                $data.perPage = $('#rx_report_filter_per_page').val();

                rxReportJs.getData($data);
            }
        }).on('change','#rx_report_filter_per_page', function(e){ // filter by perpage
            e.preventDefault();
            $data = rxReportJs.buildFilter($(this));

            rxReportJs.getData($data);
        }).on('click','.btn-update-filter', function(e){ // click on Update search result button
            e.preventDefault();
            $(this).data('searched', true);
            $autoSuggessBox.data('searched', true);
			$orderSuggestBox.data('searched', true);

            $data = rxReportJs.buildFilter($(this));
            rxReportJs.getData($data);
        }).on('click','.btn-clear-filter', function(e){
            $autoSuggessBox.data('searched', false);
			$orderSuggestBox.data('searched', false);
            $updateFilterBtn.data('searched', false)
            $autoSuggessBox.val('');
			$orderSuggestBox.val('');
            $('#rx_report_filter_status a.active').removeClass('active');
            $('#rx_report_filter_status a').first().addClass('active');
            $('#doctor_fee_gte').val('');
            $('#doctor_fee_lte').val('');
            rxReportJs.setDefaultDateTime();
            
            rxReportJs.getData({});
        }).on('click','.btn-download', function(e){
            doctorCode = $(this).data('doctorcode');
            e.preventDefault();

            $data = rxReportJs.buildFilter($(this));

            start = moment($("[name=from_date]").val(), "YYYY-MM-DD").format('DDMMYY');
            end = moment($("[name=to_date]").val(), "YYYY-MM-DD").format('DDMMYY');

            filename = doctorCode + '_rx_transaction_history_ '+ start + '_'  + end + '.csv';
            jsCommon.downloadCSV($('#url_ajax_download_sales_report').val(), $data, filename);
        }).on('keydown', '#patient_auto_suggest_txb, #order_auto_suggest_txb', function(e){
			var $_that = $(this);
			
            if(e.keyCode == 13){
				$_that.data('searched', true);
				
				$data = {};
				if ($_that.hasClass('patient')) {
					$data.term = $.trim($_that.val());
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

            $data = rxReportJs.buildFilter($(this));
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

        $(document).on('click', '#auto_suggest_i', function () {
            var $_that = $(this);
            
            $txb = $_that.next().children('input').next();
            $txb.data('searched', true);
			
            $data = {};
            if ($txb.hasClass('patient')) {
                $data.term = $.trim($txb.val());
            } else if ($txb.hasClass('order')) {
                $data.orderTerm = $.trim($txb.val());
            }
			
            $data.patientType = $('#rx_report_filter_status a.active').data('patienttype');
            $data.perPage     = $('#rx_report_filter_per_page').val();

            rxReportJs.getData($data);
        })
        jsCommon.suggestion($("#url_patient_suggestion_search").val(), {term: $autoSuggessBox.val() }, undefined, $autoSuggessBox);
		jsCommon.suggestion($("#url_patient_suggestion_search").val(), {term: $orderSuggestBox.val(), personType: 'order' }, undefined, $orderSuggestBox);
    },
    buildFilter: function ($elObj) {
        $data = {};
        fromDate = $("[name=from_date]").val();
        toDate = $("[name=to_date]").val();
        if (fromDate != '') {
            $data.fromDate = fromDate;
        }
        if (toDate != '') {
            $data.toDate = toDate;
        }

        doctorFeeGte = $('#doctor_fee_gte').val();
        doctorFeeLte = $('#doctor_fee_lte').val();
        if ($.trim(doctorFeeGte) != '') {
            $data.doctorFeeGte = doctorFeeGte;
        }
        if ($.trim(doctorFeeLte) != '') {
            $data.doctorFeeLte = doctorFeeLte;
        }

        $data.term = $.trim($autoSuggessBox.val());
		$data.orderTerm = $.trim($orderSuggestBox.val());
        $data.patientType = $('#rx_report_filter_status a.active').data('patienttype');
        $data.perPage = $('#rx_report_filter_per_page').val();

        if ($elObj.hasClass('sorting') || $elObj.attr('id') == 'rx_report_filter_per_page') {
            if ($autoSuggessBox.data('searched') == false) {
                delete $data.term;
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