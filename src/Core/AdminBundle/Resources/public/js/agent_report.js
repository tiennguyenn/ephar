var AgentReportJs = {
    init: function() {
        this.showOrderDetail();
        this.onClickBreadown();
    },
    showOrderDetail: function() {
        $(document).off('click', '.rowItemBtn i');
    },
    onClickBreadown: function() {
        $(document).on('click', '.breakdownBtn', function() {
            var url = $(this).data('url');
            $.ajax({
                url: url,
                method: "GET",
                dataType: "html",
                success: function (res) {
                    $('#breakdownModal').html(res);
                    $('#modal-breakdown').modal('show');
                },
                error: function (error) {
                }
            });
        });
    }
}

$(document).ready(function() {
    AgentReportJs.init();
});