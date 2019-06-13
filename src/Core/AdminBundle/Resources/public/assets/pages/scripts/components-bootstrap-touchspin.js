var ComponentsBootstrapTouchSpin = function() {

    var handleDemo = function() {

        $(".touchspin_7").TouchSpin({
            initval: 40
        });

    }

    return {
        //main function to initiate the module
        init: function() {
            handleDemo();
        }
    };

}();

jQuery(document).ready(function() {
    ComponentsBootstrapTouchSpin.init();
});