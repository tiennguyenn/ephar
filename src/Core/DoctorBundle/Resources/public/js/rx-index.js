var RXIndex = {
    init: function() {
        RXIndex.bootstrap();
    },
    bootstrap: function() {
        $('#modal-create-rx').modal({
            keyboard: false
        });
        $('#modal-create-rx').modal('show');

        var url = $('#patientUrl').val();
        jsCommon.suggestion(url, {}, 'text');

        $('#patientNameInput').bind('typeahead:select', function(ev, suggestion) {
            $('#createButton').attr('href', suggestion.url);
        });

        $('#cancelBtn').click(function() {
            window.history.back();
        })
    }
}

$(document).ready(function() {
    RXIndex.init();
});