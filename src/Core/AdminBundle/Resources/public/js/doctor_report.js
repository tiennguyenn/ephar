var DoctorReportJs = {
    init: function() {
        this.showOrderDetail();
    },
    showOrderDetail: function() {
        $(document).off('click', '.rowItemBtn i');
    }
}

$(document).ready(function() {
    DoctorReportJs.init();
});