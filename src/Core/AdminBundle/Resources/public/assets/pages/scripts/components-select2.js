var ComponentsSelect2 = function() {

    var handleDemo = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Select a State";

        $(".select2, .select2-multiple").select2({
            placeholder: placeholder,
            width: null
        });
      
      $(".select2-no-search").select2({
          placeholder: placeholder,
          width: null,
          minimumResultsForSearch: Infinity
        });
      
      
        function formatState (state) {
          if (!state.id) {
            return state.text;
          }
          var $state = state.id;
          return $state;
        };

        $(".select2-value").select2({
          templateSelection: formatState,
          dropdownParent: $('#dropdown-parent')
        });
		
		$(".select2-value-default").select2({
          templateSelection: formatState,
		  width: null
        });
      
        $(".select2, .select2-multiple, .select2-value, .select2-value-default, .select2-no-search").on('select2:closing', function(e){
          var $this = $(this);
          $this.select2("focus");
        });
      
       $('.select2, .select2-multiple, .select2-value, .select2-value-default, .select2-no-search').keydown(function(e) {
           var _thisItem = $(this);
          if(e.keyCode == 40) {
            _thisItem.prev().select2('open');  
          }
        });

        // copy Bootstrap validation states to Select2 dropdown
        //
        // add .has-waring, .has-error, .has-succes to the Select2 dropdown
        // (was #select2-drop in Select2 v3.x, in Select2 v4 can be selected via
        // body > .select2-container) if _any_ of the opened Select2's parents
        // has one of these forementioned classes (YUCK! ;-))
        $(".select2, .select2-multiple, .select2-value, .select2-value-default").on("select2:open", function() {
            if ($(this).parents("[class*='has-']").length) {
                var classNames = $(this).parents("[class*='has-']")[0].className.split(/\s+/);

                for (var i = 0; i < classNames.length; ++i) {
                    if (classNames[i].match("has-")) {
                        $("body > .select2-container").addClass(classNames[i]);
                    }
                }
            }
        });
       
    }

    return {
        //main function to initiate the module
        init: function() {
            handleDemo();
        }
    };

}();

if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function() {
        ComponentsSelect2.init();
    });
}