var Form = {
    baseUrl: $("#asset-url").val(),
    data: {},

    initStatus: function () {
        $("#admin-register-agent-login-submit").on('click', function (e) {
            e.preventDefault();
            $("#admin-register-agent-login").submit();
        });
        this.validateInfor();
        if ($('#current-agent-login-id').val()) {
            $("#admin_agent_login_photo").removeAttr('required');

            $("#admin_agent_login_photo").on('change', function () {
                $("#admin_agent_login_photo").attr('required', 'required');
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
        var validobj = $("#admin-register-agent-login").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_agent_login[gender]':
                        $("#admin_agent_login_gender").parent().append(error);
                        break;
                    case 'admin_agent_login[privilege][]':
                        $("#admin_agent_login_privilege").parent().append(error);
                        break;
                    case 'admin_agent_login[photo]':
                        var tag = $e.parents(".col-md-4").first().find('.fileinput');
                        tag.append(error);
                        break;
                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                'admin_agent_login[privilege][]': {
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
