
var pharmacy = {
  init : function(){
        $("#pharmacy-form").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                // var parent  = element[0].parentElement.parentElement;
                // var cl = parent.getAttribute('class')+" has-error";
                //  $e.parents(".form-group").addClass('has-error');
                //  parent.setAttribute('class', cl);
                // if(element.attr("type") == 'radio')
                // {
                //   element.parent().parent().append(error);
                // } else 


                if (element.attr("name") == 'pharmacy[phoneLocation]' || element.attr("name") == 'pharmacy[phone]' || element.attr("name") == 'pharmacy[phoneArea]') {
                    var field = $e.parents(".form-group").find('.error-data');
                    field.html('');

                    field.append(error);
                } else if (element.attr("name") == 'pharmacy[gst]') {
                    var field = $e.parents(".col-md-4");
                    field.append(error);
                } else {
                    error.insertAfter(element);
                }
            }
        });
}
  
}
var doctor = {
  init : function(){
        $("#admin-doctor-form").validate({});
  }
}
$(document).ready(function() {
    pharmacy.init();
    doctor.init();

});