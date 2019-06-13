var ComponentsDropdowns = function () {
  
    var handleMultiSelect = function () {
        $('#my_multi_select1').multiSelect();
    }

    return {
        //main function to initiate the module
        init: function () {
            handleMultiSelect();
        }
    };

}();

jQuery(document).ready(function() {    
   ComponentsDropdowns.init(); 
});