var jsChangePassword = {

    pwForm: '#change-password-form',

    init: function() {
        // jsChangePassword.submitFormPassword();
        jsChangePassword.validatePassword();
        $('#btn-submit-setting-pwd, #btn-submit-pwd').on('click',function(e) {
            e.preventDefault();
            $(this).submit();
        });
    },

    validatePassword: function(){

        var validobj = $(jsChangePassword.pwForm).validate({
            errorClass: "error",
            errorElement: 'span',
            rules: {
                'ChangePasswordBundle_public[new_password]': {
                    pwcheck: true,
                },
                'ChangePasswordBundle_public[confirm_password]': {
                    equalTo: "#ChangePasswordBundle_public_new_password"
                }
            },
            messages: {
                'ChangePasswordBundle_public[new_password]': {
                    pwcheck: "Minimum eight characters, at least one uppercase letter, one lowercase letter and one number."
                },
                'ChangePasswordBundle_public[confirm_password]': {
                    equalTo: "Password does not match",
                },
            },
            submitHandler: function (form) {
                var formId = jsChangePassword.pwForm;
                if($('#btn-submit-pwd').length > 0){
                    form.submit();
                }else{
                    successCallback = function (res) {
                        if(res == true) {
                            //show popup
                            $('#modal-setting-pwd').modal();
                        } else {
                            location.href = $(formId).attr('action')+"?id="+$(formId).find('input[name=id]').val()+"&type="+$(formId).find('input[name=type]').val();
                        }
                    };
                    errorCallback = function (xhr, ajaxOptions, thrownError) {};
                    jsDataService.callAPI($(formId).attr('action'), $(formId).serialize(), "POST", successCallback, errorCallback, null, 'json');
                }
            },
            highlight: function (element, errorClass, validClass) {
                var elem = $(element);
                elem.addClass(errorClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                var elem = $(element);
                elem.removeClass(errorClass);
            }
        });

        $.validator.addMethod("pwcheck",
            function(value, element) {
                return /^(?=.*\d)(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(value);
        });

    },
};
$(document).ready(function() {
    jsChangePassword.init();
});