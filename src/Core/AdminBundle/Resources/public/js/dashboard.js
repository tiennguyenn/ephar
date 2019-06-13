var jsSalesReport = {
    init: function() {
        var data = [{
                "monthly": "2012-01",
                "totalSales": 227,
                "agentFee": 408
            },{
                "monthly": "2012-02",
                "totalSales": 127,
                "agentFee": 2080
            }];

        $.ajax({
            type: "POST",
            url: $('#ajax_data_chart_url').val(),
            success: function(data){
                jsSalesReport.chart(data.dataChart);
                $('#totalFee').html(data.totalFee);
            },
            complete: function(data){
                $("a[href='http://www.amcharts.com/javascript-charts/']").remove();

            }
        });
        // this.chart(data);
    },
    chart: function(data){
        var chart = AmCharts.makeChart("agent_chart", {
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
                "fillColors" : "#007164",
                "color" : "#249987"
            }, {
                "bullet": "square",
                "balloonText": "SGD [[value]]",
                "bulletBorderAlpha": 1,
                "bulletBorderThickness": 1,
                "dashLengthField": "dashLength",
                "legendValueText": "SGD [[value]]",
                "legendPeriodValueText": "SGD [[value.sum]]",
                "title": "G-MEDS Fees",
                "fillAlphas": 0,
                "valueField": "totalFee",
                "valueAxis": "totalFeeAxis",
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
            "dataDateFormat": "YYYY-MM",
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
    getData: function(){
        successCallback = function (res) {
            jsSalesReport.chart(res);
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };
        jsDataService.callAPI($("#url_ajax_list_doctors").val(), $('#frm-doctor-filter').serialize(), "POST", successCallback, errorCallback, null, 'html');
    },
}

$(document).ready(function() {
    jsSalesReport.init();
});