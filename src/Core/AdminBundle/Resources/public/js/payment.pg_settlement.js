var jsPGSettlement = {
    init: function() {
        jsCommon.pagingAjax();
        this.handleDateRangePickers();
        this.search();
        this.pgsOnePicked();
    },
    search: function(){
        var pgf = $("#frm-pg-settlement-filter");
        var pgc = $("div.content-table.pg-settlement-container");
        //on action
        pgf.on('click', '.btn-ps-search', function(e){
            e.preventDefault();
            console.log('search');
            jsPGSettlement.getData();
        }).on('keyup','#pg_settlement_filter_term', function(e) {
            e.preventDefault();
            if (e.keyCode == 13) {
                jsPGSettlement.getData();
            }
        }).on('change','#pg_settlement_filter_per_page', function(e){
            e.preventDefault();
            jsPGSettlement.getData();
        }).on('click','#pg_settlement_filter_status > a', function(e){
            e.preventDefault();
            if(!$(this).hasClass('active')){
                $(this).closest('div').find('a').removeClass('active');
                $(this).addClass('active');
                $(this).closest('div').find('input').val($(this).data('value'));
                jsPGSettlement.getData();
            }
        });
        //on sorting
        pgc.on('click', 'th.sorting', function(e){
            e.preventDefault();
            var colName = $(this).data('colname');
            if($(this).hasClass('sorting_asc')){
                $(this).removeClass('sorting_asc');
                $(this).addClass('sorting_desc');
                pgf.find("input[name='pg_settlement_filter[sorting]']").val(colName+'_desc');
            } else {
                $(this).removeClass('sorting_desc');
                $(this).addClass('sorting_asc');
                pgf.find("input[name='pg_settlement_filter[sorting]']").val(colName+'_asc');
            }
            jsPGSettlement.getData();
        });
        jsCommon.suggestion($("#url_pg_settlement_suggestion").val(), {});
    },
    getData: function(){
        successCallback = function (res) {
            $("div.content-table.pg-settlement-container" ).html(res);
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };
        jsDataService.callAPI($("#url_pg_settlement_filter").val(), $("#frm-pg-settlement-filter").serialize(), "POST", successCallback, errorCallback, null, 'html');
    },
    pgsOnePicked: function(){
        var mu_pgs = $("#modal-update-pg-settlement");
        $(document).on('click', ".btn-modal-update-pg-settlement", function(e){
            e.preventDefault();
            var self_tr = $(this).closest('tr');
            var params = {orderNumber: self_tr.data('ordernumber')};
            successCallback = function (res) {
                mu_pgs.find("div.content-views").html(res);
                mu_pgs.find(".msg-container").html("");
                mu_pgs.modal();
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_pg_settlement_detail").val(), params, "POST", successCallback, errorCallback, null, 'html');
        });
        //on input
        mu_pgs.on('focus', 'input.on-money-format', function(){
            var self = $(this);
            self.val(self.val().replace(',',''));
        }).on('blur', 'input.on-money-format', function(){
            var self = $(this);
            var v = (self.val() != "")? self.val().replace(/[^0-9\.]/g, ''): 0;
            self.val(jsCommon.currencyFormat(v));
        }).on('input', 'input.on-money-format', function(e) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((e.which != 46 || self.val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)){
                e.preventDefault();
            }
        });

        jsPGSettlement.pgsOneUpdate(mu_pgs);
    },
    pgsOneUpdate: function(element) {
        $(element).on('click', ".btn-pgs-update", function(e){
            e.preventDefault();
            var self = $(this);
            successCallback = function (res) {
                var msgText = '';
                if($.isEmptyObject(res)){
                    msgText = '<div class="alert alert-danger"><button class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button> '+ msg.msgCannotEdited.replace('%s', 'Payment Gateway Settlement') +'</div>';
                } else {
                    msgText = '<div class="alert alert-success"><button class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button> '+ msg.msgUpdatedSuccess.replace('%s', 'Payment Gateway Settlement') +'</div>';
                    jsPGSettlement.getData();
                }
                self.closest('form').find(".msg-container").html(msgText);
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_pg_settlement_update").val(), self.closest('form').serialize() , "POST", successCallback, errorCallback, null, 'json');
        });
    },
    handleDateRangePickers: function () {
        if (!jQuery().daterangepicker) {
            return;
        }
        var pgRange = $('#pg_settlement_filter_date');
        pgRange.daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                startDate: moment().subtract('days', 29), //moment().subtract('days', 6),
                endDate: moment(),
                //minDate: '01/01/2012',
                //maxDate: '12/31/2014',
                dateLimit: {
                    days: 60
                },
                showDropdowns: true,
                showWeekNumbers: true,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                buttonClasses: ['btn'],
                applyClass: 'green',
                cancelClass: 'default',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                locale: {
                    applyLabel: 'Apply',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom Range',
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    firstDay: 1
                }
            },
            function (start, end) {
                pgRange.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                pgRange.find("input[name='start_date']").val(start.format('MMMM D, YYYY'));
                pgRange.find("input[name='end_date']").val(end.format('MMMM D, YYYY'));
                jsPGSettlement.getData();
            }
        );
        //Set the initial state of the picker label
        var sd = moment().subtract('days', 29).format('MMMM D, YYYY'), ed = moment().format('MMMM D, YYYY');
        pgRange.find('span').html(sd + ' - ' + ed);
        pgRange.find("input[name='start_date']").val(sd);
        pgRange.find("input[name='end_date']").val(ed);
    }
};
$(document).ready(function() {
    jsPGSettlement.init();
});