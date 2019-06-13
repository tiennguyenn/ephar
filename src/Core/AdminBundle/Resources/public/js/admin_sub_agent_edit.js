var Form = {
    baseUrl: $("#asset-url").val(),
    data: {},

    initStatus: function () {
        $("#admin-register-agent-submit").on('click', function (e) {
            e.preventDefault();
            $("#admin-register-agent").submit();
        });
        this.validateInfor();
        $("#admin_agent_country").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 1, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_state', dataPost, 'Select State / Province');
            dataPost = {'type': 6, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_city', dataPost, 'Select City');
        });
        $("#admin_agent_state").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 2, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_city', dataPost, 'Select City');
        });
        if ($('#current-agent-id').val()) {
            $("#admin_agent_logo").removeAttr('required');

            $("#admin_agent_logo").on('change', function () {
                $("#admin_agent_logo").attr('required', 'required');
            });
        } else {
            $("#admin_agent_state").select2({placeholder: 'Select State / Province'});
            $("#admin_agent_country").select2({placeholder: 'Select Country'});
            $("#admin_agent_phoneLocation").select2({placeholder: 'Select Country'});
            $("#admin_agent_localIdPassportCountry").select2({placeholder: 'Select Country'});
            $("#admin_agent_city").select2({placeholder: 'Select City'});
        }
    },

    getListCountryPhone(input) {
        if (this.countryData.length == 0) {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 3};
            $.ajax({
                type: "POST",
                url: dependUrl,
                data: dataPost,
                beforeSend: function () {
                },
                success: function (data, textStatus, jqXHR) {
                    Form.countryData = data;
                    var t = '';
                    $.each(data, function (index, value) {
                        var slect = '';
                        if (Form.defaultPhoneCode == value.id)
                        {
                            slect = 'selected="selected"'
                        }

                        t += '<option ' + slect + '  value="' + value.id + '">' + value.name + ' (+' + value.phoneCode + ')</option>';
                    });
                    $(input).html(t);
                    $(input).select2();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                }
            });
        } else {
            var data = Form.countryData;
            var t = '';
            $.each(data, function (index, value) {
                var slect = '';
                if (Form.defaultPhoneCode == value.phoneCode)
                {
                    slect = 'selected="selected"'
                }
                t += '<option ' + slect + ' data-countrycode="' + value.code + '" value="' + value.phoneCode + '">' + value.name + ' (+' + value.phoneCode + ')</option>';
            });
            $(input).html(t);
            $(input).select2();
        }
    },

    callAjaxForSelect2: function (url, target, dataPost, empty, val = '') {
        $.ajax({
            type: "POST",
            url: url,
            data: dataPost,
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                var t = '';
                $.each(data, function (index, value) {
                    t += '<option value="' + index + '">' + value + '</option>';
                });
                $(target).html(t);
                $(target).val(val);
                $(target).select2({placeholder: empty});
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            }
        });
    },

    validateInfor: function () {
        var validobj = $("#admin-register-agent").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_agent[phoneLocation]':
                        var tag = $e.parents(".col-md-8").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_agent[phone]':
                        var tag = $e.parents(".col-md-8").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_agent[gender]':
                        $("#admin_agent_gender").parent().append(error);
                        break;
                    case 'admin_agent[localIdPassportCountry]':
                        $e.parent().append(error);
                        break;
                    case 'admin_agent[country]':

                        $e.parent().append(error);
                        break;
                    case 'admin_agent[city]':

                        $e.parent().append(error);
                        break;
                    case 'admin_agent[localIdPassportDate]':
                        $e.parents(".col-md-4").append(error);
                        break;

                    case 'admin_agent[logo]':
                        var tag = $e.parents(".col-md-4").first().find('.fileinput');
                        tag.append(error);
                        break;

                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {              
                'admin_agent[phoneArea]': {
                    digits: true
                },
                'admin_agent[phone]': {
                    digits: true
                }
            },
            submitHandler: function (form) {
                form.submit();
            },
            highlight: function (element, errorClass, validClass) {

                var elem = $(element);
                if (elem.hasClass('select2')) {
                    var t = elem.parent().find(".select2-selection").first();
                    t.attr('style', 'border: 1px solid red');

                } else {
                    elem.addClass(errorClass);
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                var elem = $(element);
                if (elem.hasClass('select2')) {
                    console.log(elem);
                    var t = elem.parent().find(".select2-selection").first();
                    t.attr('style', '');
                } else {
                    elem.removeClass(errorClass);
                }
            }

        });
        $(document).on('change', '.select2', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });
        $("#admin_agent_phoneArea").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });
        $("#admin_agent_phone").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });

        $("#admin_agent_localIdPassportDate").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });




    },
}
$(document).ready(function () {
    jQuery.validator.addMethod("empty", function (value, element) {
        return value != 'empty';
    }, "This field is required");
    jQuery.validator.addMethod("unique", function (value, element) {
        var check = false;
        $.ajax({
            type: "POST",
            url: $("#validate-email-url").val(),
            data: {data: value,id: $("#current-agent-id").val(),type: 2},
            async: false,
            success: function (data, textStatus, jqXHR) {  
                
                check = data.success;
            }      
        });
        
        return check;
        
    }, "This value must be unique");
    Form.initStatus();
});
