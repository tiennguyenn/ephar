var Form = {
    baseUrl: $("#asset-url").val(),
    dependentUrl: $("#ajaxUrlDependent").val(),
    singaporeCountryId: $('#singapore_id').val(),
    malaysiaCountryId: $('#malaysia_id').val(),
    data: {},

    initStatus: function () {
        jsCommon.viewLogs('#view_logs_anchor', '#admin_view_logs_url');
        
        $("#admin-register-delivery-submit").on('click', function (e) {
            e.preventDefault();
            $("#admin-register-delivery").submit();
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
        if ($("#main-agent-id").val()) {
            $("#admin_agent_logo").removeAttr('required');

            $("#admin_agent_logo").on('change', function () {
                $("#admin_agent_logo").attr('required', 'required');
            });
        } else {
            $("#admin_agent_state").select2({placeholder: 'Select State / Province'});
            $("#admin_agent_country").select2({placeholder: 'Select Country'});
            $("#admin_agent_phoneLocation").select2({placeholder: 'Select Country'});
            $("#admin_agent_localIdPassportCountry").select2({placeholder: 'Select Country'});
            $("#admin_agent_bankCountryIssue").select2({placeholder: 'Select Country'});
            $("#admin_agent_bankCountry").select2({placeholder: 'Select Country'});
            $("#admin_agent_city").select2({placeholder: 'Select City'});
        }

        $("#admin_agent_bankCountry").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 7, 'data': this.value};
            if (this.value == Form.singaporeCountryId || this.value == Form.malaysiaCountryId) {
                Form.callAjaxForSelect2(dependUrl, '#bank_name', dataPost, 'Select Bank Name', '');
            } else {
                $('#admin_agent_bankName').val('').show();
                $('#bank_name').removeAttr('required').next(".select2-container").hide();
                $('#admin_agent_bankSwiftCode').val('').removeAttr('readonly');
            }
        });

        var currentBankCountry = $('#admin_agent_bankCountry').val();
        if (currentBankCountry != "" && (currentBankCountry == Form.singaporeCountryId || currentBankCountry == Form.malaysiaCountryId)) {
            $("#admin_agent_bankCountry").change();
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

    callAjaxForSelect2: function (url, target, dataPost, empty, val) {
        $.ajax({
            type: "POST",
            url: url,
            data: dataPost,
            beforeSend: function () {
                if (dataPost.type == 7) {
                    $('#admin_agent_bankSwiftCode').val('');
                }
            },
            success: function (data, textStatus, jqXHR) {
                var t = '';
                $.each(data, function (index, value) {
                    t += '<option value="' + index + '">' + value + '</option>';
                });
                $(target).html(t);
                $(target).val(val);
            },
            complete: function () {
                if (dataPost.type == 7) {
                    $('#admin_agent_bankName').hide();
                    $('#bank_name').show().addClass('select2').select2().change(function () {
                        var bankId = $(this).val();
                        Form.getBankSwiftCode(bankId);
                    }).attr('required', true);
                    if ($('#admin_agent_bankName').val() != '') {
                        $('#bank_name').val($('#admin_agent_bankName').val()).change();
                    }
                    $('#admin_agent_bankSwiftCode').attr('readonly', true);
                }
                $(target).select2({
                    placeholder: empty
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            }
        });
    },

    getBankSwiftCode: function (bankId) {
        var dataPost = {'type': 8, 'data': bankId};
        var url = Form.dependentUrl;
        $.ajax({
            type: "POST",
            url: url,
            data: dataPost,
            success: function (data, textStatus, jqXHR) {
                if (typeof data.status !== 'undefined' && data.status ===  true) {
                    if (typeof data.swift_code !== "undefined") {
                        $('#admin_agent_bankSwiftCode').val(data.swift_code).valid();
                        $('#admin_agent_bankName').val(bankId);
                    }
                }
            }
        });
    },

    validateInfor: function () {
        if ($("#admin_agent_gstSetting_1").is(":checked")) {
            $('#admin_agent_gstNum').attr('disabled', true);
         
        }
        var validobj = $("#admin-register-delivery").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_agent[phoneLocation]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_agent[phoneArea]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_agent[phone]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
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
                    case 'admin_agent[bankCountry]':

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
                    case 'admin_agent[bankName]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'bank_name':
                        var tag = $e.parent();
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
                $('#admin-register-delivery-submit').addClass('disabled');
                $('#admin-register-delivery-cancel').addClass('disabled');
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
        $(document).on('click', 'input[name="admin_agent[gstSetting]"]', function () {
            if ($("#admin_agent_gstSetting_0").is(":checked")) {
                $('#admin_agent_gstNum').removeAttr('disabled');
                
            } else {      
                $('#admin_agent_gstNum').attr('disabled', true);
                if($('#admin_agent_gstNum').hasClass('error')) {
                    $('#admin_agent_gstNum').removeClass('error');
                }
            }
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
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


    Form.initStatus();
});
