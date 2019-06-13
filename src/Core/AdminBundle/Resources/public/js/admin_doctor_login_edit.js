var Form = {
    baseUrl: $("#asset-url").val(),
    data: {},

    initStatus: function () {
        $("#admin-register-doctor-login-submit").on('click', function (e) {
            e.preventDefault();
            $("#admin-register-doctor-login").submit();
        });
        this.validateInfor();
        if ($('#current-doctor-login-id').val()) {
            $("#admin_doctor_login_photo").removeAttr('required');

            $("#admin_doctor_login_photo").on('change', function () {
                $("#admin_doctor_login_photo").attr('required', 'required');
            });
        }
		
		$("input.group").on("click", function(e) {
			var checked = this.checked;
			var parent = $(this).parents(".group").first();
			$(parent).find(".item input").each(function(){
				this.checked = checked;
			});
		});
    },
    validateInfor: function () {
        var validobj = $("#admin-register-doctor-login").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_doctor_login[gender]':
                        $("#admin_doctor_login_gender").parent().append(error);
                        break;
                    case 'admin_doctor_login[privilege][]':
                        $("#admin_doctor_login_privilege").parent().append(error);
                        break;
                    case 'admin_doctor_login[photo]':
                        var tag = $e.parents(".col-md-4").first().find('.fileinput');
                        tag.append(error);
                        break;
                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                'admin_doctor_login[privilege][]': {
                    required: true
                }
            },
            submitHandler: function (form) {
                form.submit();
            },
        });
    },
}
$(document).ready(function () {
    jQuery.validator.addMethod("empty", function (value, element) {
        return value != 'empty';
    }, "This field is required");
    Form.initStatus();
});
