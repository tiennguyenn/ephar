var jsDoctorChangePassword = {

    pwForm: '#change-password-form',
    passwordData : '',
    passwordAjaxUrl : $('#ajax_password_change').val(),

    init: function() {
        jsDoctorChangePassword.submitFormPassword();
        jsDoctorChangePassword.validatePassword();
    },

    submitFormPassword: function(){
        $('#btn-submit-pwd').on('click',function(e){

            $(jsDoctorChangePassword.pwForm).find('input[type="password"]').each(function(e){
                if($(this).val() == ''){
                    jsDoctorChangePassword.showError($(this), 'Please input this field.');
                }
            });
            if($(jsDoctorChangePassword.pwForm).find('span.error:not(".hidden")').length > 0){
                e.preventDefault();
            }else{
               jsDoctorChangePassword.passwordData = $(jsDoctorChangePassword.pwForm).serializeArray();
                $.ajax({
                    type: "POST",
                    url: jsDoctorChangePassword.passwordAjaxUrl,
                    data: jsDoctorChangePassword.passwordData,
                    success: function(data){
                        // $(jsDoctorChangePassword.pwForm)[0].reset();
                        if(data.success){
                            $('#modal-notice-successful').modal('show');
                        }else{
                            $('#modal-notice-unsuccessful').find('.txt-notice').html(data.message);
                            $('#modal-notice-unsuccessful').modal('show');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#modal-notice-unsuccessful').modal('show');
                        if (typeof errorCallback == 'function')
                            return errorCallback(jqXHR, textStatus, errorThrown);
                        return false;
                    },
                    beforeSend: function () {
                    },
                });
            }
        });
    },

    showError: function(el, msg){
        var div = el.next('div.errorBox');
        el.addClass('error');
        div.html('<span class="error">'+msg+'</span>');
    },

    removeError: function(el){
        var div = el.next('div.errorBox');
        el.removeClass('error');
        div.find('span.error').addClass('hidden');
    },

    validatePassword: function(){

        $('#ChangePasswordBundle_doctor_current_password ,#ChangePasswordBundle_doctor_new_password, #ChangePasswordBundle_doctor_confirm_password').on('keyup', function(){
            jsDoctorChangePassword.removeError($(this));
        });

        $('#ChangePasswordBundle_doctor_new_password').on('keyup', function(){
            //Minimum eight characters, at least one uppercase letter, one lowercase letter and one number
            var regex = /^(?=.*\d)(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            if(!regex.test($(this).val()) && $(this).val() != ''){   
                jsDoctorChangePassword.showError($(this),'Minimum eight characters, at least one uppercase letter, one lowercase letter and one number.');
            }else{
                jsDoctorChangePassword.removeError($(this));
            }
            if($(this).val() != $('#ChangePasswordBundle_doctor_confirm_password').val() && $('#ChangePasswordBundle_doctor_confirm_password').val() != ''){
                jsDoctorChangePassword.showError($('#ChangePasswordBundle_doctor_confirm_password'),'Password does not match.');
            }
        });

        $('#ChangePasswordBundle_doctor_confirm_password').on('keyup', function(){
            if($(this).val() != $('#ChangePasswordBundle_doctor_new_password').val() && $(this).val() != ''){   
                jsDoctorChangePassword.showError($(this),'Password does not match.');
            }else{
                jsDoctorChangePassword.removeError($(this));
            }
        });
    }
}

$(document).ready(function() {
    jsDoctorChangePassword.init();
});