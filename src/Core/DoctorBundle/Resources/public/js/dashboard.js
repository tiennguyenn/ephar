var DoctorDashboard = {
    init: function() {
        jsCommon.pagingAjax();
        DoctorDashboard.search();
        $.ajax({
            type: "POST",
            url: $('#ajax_data_chart_url').val(),
            success: function(data){
                DoctorDashboard.chart(data);
            },
            complete: function(data){
                $("a[href='http://www.amcharts.com/javascript-charts/']").remove();

            }
        });

        if ($('#isShowDialog').length > 0) {
            $('#modal-refill-rx-complete').modal('show');
        }
        DoctorDashboard.closeNotification();
        DoctorDashboard.onClickViewClosedMessages();

    },
    closeNotification: function () {
        $(document).on('click', '.close-refill-reminder', function () {
            $('#rrId_hf').val($(this).data('rrid'));
            $('#rrId_hf').data('notification', $(this).data('notification'));

            $('#modal-close').on('hidden.bs.modal', function() {
                $('#rrId_hf').data('notification', 0);
            });
        });

        $(document).on('click', '#modal_close_anchor', function () {
            var url = $('#close_notification_hf').val();
            if ($('#rrId_hf').data('notification')) {
                url += '?msgId=' + $('#rrId_hf').val();
            } else {
                url += '?rrId=' + $('#rrId_hf').val();
            }
            $.ajax({
                type: "GET",
                url: url,
                success: function(data){
                    console.log(data);
                    if (data.status === true) {
                        targetId = '#row_' + $('#rrId_hf').val();
                        window.location.reload(true)
                    }
                }
            });
        })
    },
    onClickViewClosedMessages: function() {
        $(document).on('click', '#vcmLink', function(event) {
            var self = this;
            $.ajax({
                url: $('#cmURL').val(),
                beforeSend: function(){
                    $(self).addClass('disabled');
                },
                success: function(data){
                    $('#cmDiv').html(data);
                    $('#modal-closed-messages').modal('show');
                },
                complete: function(){
                    $(self).addClass('disabled');
                }
            });
        });

        $('#cmDiv').on('click', '.sorting', function(e) {
            e.preventDefault();

            var sorting = '';
            var colName = $(this).data('colname');
            if ($(this).hasClass('sorting_asc')) {
                $(this).removeClass('sorting_asc');
                $(this).addClass('sorting_desc');
                sorting = colName+'_desc';
            } else {
                $(this).removeClass('sorting_desc');
                $(this).addClass('sorting_asc');
                sorting = colName+'_asc';
            }

            var url = $('#cmURL').val();
            url += '?' + $.param({'sorting': sorting});

            $.ajax({
                url: url,
                success: function(data){
                    $('#cmDiv').html(data);
                }
            });
        });
    },
    chart: function(data){
        var chart = AmCharts.makeChart("agent_chart", {
            "numberFormatter": {
                "precision": 2,
                "decimalSeparator": ".",
                "thousandsSeparator": ","
            },
            "type": "serial",
            "theme": "light",
            "fontFamily": 'Open Sans',
            "color":    '#888888',

            "legend": {
                "equalWidths": false,
                "useGraphSettings": true,
                "valueAlign": "left",
                "valueWidth": 120
            },
            "dataProvider": data,
            "valueAxes": [{
                "id": "totalsalesAxis",
                "axisAlpha": 0,
                "gridAlpha": 0,
                "position": "left",
                "unit" : 'SGD ',
                "unitPosition" : "left"
            }],
            "graphs": [{
                "alphaField": "alpha",
                "balloonText": "SGD [[value]]",
                "dashLengthField": "dashLength",
                "fillAlphas": 0.7,
                "legendPeriodValueText": "SGD [[value.sum]]",
                "legendValueText": "SGD [[value]]",
                "title": "Total Sales",
                "type": "column",
                "valueField": "totalSales",
                "valueAxis": "totalSalesAxis",
                "fillColors" : "#007164"
            }, {
                "bullet": "square",
                "balloonText": "SGD [[value]]",
                "bulletBorderAlpha": 1,
                "bulletBorderThickness": 1,
                "dashLengthField": "dashLength",
                "legendValueText": "SGD [[value]]",
                "legendPeriodValueText": "SGD [[value.sum]]",
                "title": "Doctor Fees",
                "fillAlphas": 0,
                "valueField": "totalDoctorFee",
                "valueAxis": "totalDoctorFeeAxis",
                "lineColor": "#F68F5A"
            }],
            "chartCursor": {
                "categoryBalloonDateFormat": "MM",
                "cursorAlpha": 0.1,
                "cursorColor": "#000000",
                "fullWidth": true,
                "valueBalloonsEnabled": false,
                "zoomable": false
            },
            "dataDateFormat": "YYY-MM",
            "categoryField": "monthly",
            "categoryAxis": {
                "dateFormats": [{
                    "period": "DD",
                    "format": "DD"
                }, {
                    "period": "WW",
                    "format": "MMM DD"
                }, {
                    "period": "MM",
                    "format": "MMM"
                }, {
                    "period": "YYYY",
                    "format": "YYYY"
                }],
                "axisColor": "#555555",
                "gridAlpha": 0.1,
                "gridColor": "#FFFFFF",
                "gridCount": data.length
            },
            "exportConfig": {
                "menuBottom": "20px",
                "menuRight": "22px",
                "menuItems": [{
                    "icon": App.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
                    "format": 'png'
                }]
            }
        });

        $('#agent_chart').closest('.portlet').find('.fullscreen').click(function() {
            chart.invalidateSize();
        });
    },

    search: function(start, end){
        var df = $("#frm-rx-filter");
        var dc = $("div.content-table.rx-container");
        //on action
        df.on('change','#ps_filter_per_page', function(e){
            e.preventDefault();
            DoctorDashboard.getData();
        });
        //on sorting
        dc.on('click', 'th.sorting', function(e){
            e.preventDefault();
            var colName = $(this).data('colname');
            if($(this).hasClass('sorting_asc')){
                $(this).removeClass('sorting_asc');
                $(this).addClass('sorting_desc');
                df.find("input[name='ps_filter[sorting]']").val(colName+'_desc');
            } else {
                $(this).removeClass('sorting_desc');
                $(this).addClass('sorting_asc');
                df.find("input[name='ps_filter[sorting]']").val(colName+'_asc');
            }
            DoctorDashboard.getData();
        });
    },
    getData: function(){
        successCallback = function (res) {
            $("div.content-table.rx-container" ).html(res);
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };
        jsDataService.callAPI($("#url_ajax_list_rx").val(), $('#frm-rx-filter').serialize(), "POST", successCallback, errorCallback, null, 'html');
    },

}

$(document).ready(function() {
    DoctorDashboard.init();
});