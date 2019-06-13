/**
Demo script to handle the theme demo
**/
var Custom = function () {

    // Handle Theme Settings
    var showRowContent = function () {
      
      $(document).on('click', '.view-row-content', function(){
        $(this).closest('tr').next('.row-content').fadeIn();
      });
      
    };
		 // Handle Theme Settings
    var rowExpanded = function () {
      $(document).on('click', '.rowItemBtn', function() {
				var _this = $(this);
				var _parent = _this.closest('.rowItem');
				
				if(_parent.hasClass('open')) {
					_parent.next('.rowItemExpand').slideUp();
					_parent.removeClass('open');
          
          if(_this.hasClass('rowItemBtnText')) {
            _this.find('.text').html('VIEW MORE');
          }
				}else{
					_parent.addClass('open');
					_parent.next('.rowItemExpand').slideDown();
          
          if(_this.hasClass('rowItemBtnText')) {
            _this.find('.text').html('VIEW LESS');
          }
				}
			})
        
    };
		
    var switchChange = function() {
        $('.make-switch').on('switchChange.bootstrapSwitch',function(){
            var _this = $(this);
            var _parent =  _this.closest('.bootstrap-switch');
            var _labelText = _parent.next('.switch-text');

            if(_this.prop('checked', true)) {
            }

            if(_parent.hasClass('bootstrap-switch-off')) {
                _labelText.find('.switch-text-on').hide();
                _labelText.find('.switch-text-off').show();
            }else {
                _labelText.find('.switch-text-off').hide();
                _labelText.find('.switch-text-on').show();
            }
        })
    };
    
    var subDropdown = function() {
      $(document).on('click', '.sub-dropdown-toggle', function(e){
           $(this).parent().toggleClass('open');       
           e.preventDefault();
           e.stopPropagation();
                });

          $(document).on('click', 'body', function(e){
            $('.sub-dropdown-toggle').parent().removeClass('open');
          });
    };
  
   var porletCollapse = function(){
     $(document).on('click', '.portlet-collapse .portlet-title', function(e) {
       var _this = $(this);
       var _parent = _this.parents('.portlet-collapse'); 
       
       if(_parent.hasClass('expanded')) {
         _parent.find('.portlet-body').slideUp();
         _parent.removeClass('expanded');
       }else{
         _parent.addClass('expanded');
         _parent.find('.portlet-body').slideDown();
       }
     });
   };

    return {

        //main function to initiate the theme
        init: function() {
            // handles style customer tool
            showRowContent(); 
            rowExpanded(); 
            switchChange();
            subDropdown();
            porletCollapse();
        }
    };

}();

function showOverLay(){
  $('.blink-loader').css({'opacity':0.7, 'visibility': 'visible'});
  
  $(document).on('click', function(){
    $('.blink-loader').css({'opacity':0, 'visibility': 'hidden'});
  });
  
}

if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function() {    
       Custom.init(); // init metronic core componets
    });
}