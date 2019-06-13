var rxMonthlyStatementJs = {
    init: function() {
        jsCommon.pagingAjax();
        var x = new Date();
        x.setDate(1);
        // x.setMonth(x.getMonth()-1); // should use this line for real code
        x.setMonth(x.getMonth()); // use this line of code for testing. 
        $('.monthly-statement-datepicker.to-date').datepicker({
            format: "M yyyy",
            viewMode: "months", 
            minViewMode: "months",
            endDate: x,
        });


        $('.monthly-statement-datepicker.from-date').datepicker({
            format: "M yyyy",
            viewMode: "months", 
            minViewMode: "months"
        });
        this.search();
    },

    search: function(){
        var df = $("#frm-rx-report-filter");
        var dc = $("div.content-table.doctor-container");
        $(document).on('click', '.daterangepicker .applyBtn', function () {
            rxMonthlyStatementJs.getData($(this));
        });
        //on sorting
        dc.on('click', 'th.sorting', function(e){
            e.preventDefault();
            var colName = $(this).data('colname');
            if($(this).hasClass('sorting_asc')){
                $(this).removeClass('sorting_asc');
                $(this).addClass('sorting_desc');
                $('#current_sort_info').data('direction', 'desc');
                df.find("input[name='rx_report_filter[sorting]']").val(colName+'_desc');
            } else {
                $(this).removeClass('sorting_desc');
                $(this).addClass('sorting_asc');
                $('#current_sort_info').data('direction', 'asc');
            }
            $('#current_sort_info').data('column', colName);

            $data = {};
            // for header fields sorting
            $sortInfo = $('#current_sort_info').data();
            if ($sortInfo.column != '') {
                $data.sortInfo = $sortInfo;
            }
            rxMonthlyStatementJs.getData($data);
        });

        $(document).on('click', '#searchBtn', function () {
            $data = {};
            $data.fromDate = $('#from_date').val();
            $data.toDate   = $('#to_date').val();

             rxMonthlyStatementJs.getData($data);
        });

    },
    getData: function($data){
        successCallback = function (res) {
            $("div.content-table.doctor-container" ).html(res);
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };

        $url = $("#url_doctor_report_monthly_statement").val();
        contentType = 'html';
        jsDataService.callAPI($url, $data, "POST", successCallback, errorCallback, null, contentType);
    }
};

$(document).ready(function() {
    rxMonthlyStatementJs.init();
});