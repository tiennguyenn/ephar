var browserCheck = {
    init: function() {
        this.checkBorwserType();
        this.triggerCheckBrowser();
    },
    checkBorwserType: function () {
        var supportedBrowser = /chrome|firefox|safari|edge/;
        var currentBrowser = navigator.userAgent.toLowerCase();
        if (currentBrowser.match(supportedBrowser) === null) {
            $('#browser-detection').modal('show');
        }
    },
    triggerCheckBrowser: function () {
        $('#username, #password').focus(function() {
            browserCheck.checkBorwserType();
        });
    }
};

$(document).ready(function () {
    browserCheck.init();
});