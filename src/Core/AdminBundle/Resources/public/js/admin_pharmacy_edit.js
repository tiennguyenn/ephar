var Form = {
    baseUrl: $("#asset-url").val(),
    dependentUrl: $("#ajaxUrlDependent").val(),
    singaporeCountryId: $('#singapore_id').val(),
    malaysiaCountryId: $('#malaysia_id').val(),
    data: {},

    initStatus: function () {
      
        this.validateInfor();
        $("#admin_pharmacy_country").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 1, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_pharmacy_state', dataPost, 'Select State / Province');
            dataPost = {'type': 6, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_pharmacy_city', dataPost, 'Select City');
        });
        $("#admin_pharmacy_state").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 2, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_pharmacy_city', dataPost, 'Select City');
        });
       
        $("#admin_pharmacy_state").select2({placeholder: 'Select State / Province'});
        $("#admin_pharmacy_country").select2({placeholder: 'Select Country'});
        $("#admin_pharmacy_city").select2({placeholder: 'Select City'});
        $("#admin_pharmacy_bankCountry").select2({placeholder: 'Select Country'});
        
        $(document).on('click', 'input[name="admin_pharmacy[gst]"]', function () {
            if ($("#admin_pharmacy_gst_0").is(":checked")) {               
                $('#admin_pharmacy_gstRegisterNumber').removeAttr('disabled');                
            } else {               
                $('#admin_pharmacy_gstRegisterNumber').attr('disabled', true); 
            }
          
        });
        if($("#admin_pharmacy_gst_1").is(":checked")) {
             $('#admin_pharmacy_gstRegisterNumber').attr('disabled', true); 
        }

        $("#admin_pharmacy_bankCountry").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 7, 'data': this.value};
            if (this.value == Form.singaporeCountryId || this.value == Form.malaysiaCountryId) {
                Form.callAjaxForSelect2(dependUrl, '#bank_name', dataPost, 'Select Bank Name', '');
            } else {
                $('#admin_pharmacy_bankName').val('').show();
                $('#bank_name').removeAttr('required').next(".select2-container").hide();
                $('#admin_pharmacy_bankSwiftCode').val('').removeAttr('readonly');
            }
        });

        var currentBankCountry = $('#admin_pharmacy_bankCountry').val();
        if (currentBankCountry != "" && (currentBankCountry == Form.singaporeCountryId || currentBankCountry == Form.malaysiaCountryId)) {
            $("#admin_pharmacy_bankCountry").change();
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
                    $('#admin_pharmacy_bankSwiftCode').val('');
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
                    $('#admin_pharmacy_bankName').hide();
                    $('#bank_name').show().addClass('select2').select2().change(function () {
                        var bankId = $(this).val();
                        Form.getBankSwiftCode(bankId);
                    }).attr('required', true);
                    if ($('#admin_pharmacy_bankName').val() != '') {
                        $('#bank_name').val($('#admin_pharmacy_bankName').val()).change();
                    }
                    $('#admin_pharmacy_bankSwiftCode').attr('readonly', true);
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
                        $('#admin_pharmacy_bankSwiftCode').val(data.swift_code).valid();
                        $('#admin_pharmacy_bankName').val(bankId);
                    }
                }
            }
        });
    },

    validateInfor: function () {
        var validobj = $("#admin-create-pharmacy").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_pharmacy[phoneLocation]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_pharmacy[phoneArea]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_pharmacy[phone]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    
                    case 'admin_pharmacy[country]':

                        $e.parent().append(error);
                        break;
                    case 'admin_pharmacy[bankCountry]':

                        $e.parent().append(error);
                        break;
                    case 'admin_pharmacy[bankName]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'bank_name':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'admin_pharmacy[city]':
                        console.log($e);
                        $e.parent().append(error);
                        break;              

                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                'admin_pharmacy[phoneArea]': {
                    digits: true
                },
                'admin_pharmacy[phone]': {
                    digits: true
                },
                'admin_pharmacy[accountNumber]': {
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
        $("#admin_pharmacy_phoneArea").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });
        $("#admin_pharmacy_phone").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });
        $("#admin_pharmacy_localIdPassportDate").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });
    }
};

$(document).ready(function () {
    jQuery.validator.addMethod("empty", function (value, element) {
        return value != 'empty';
    }, "This field is required");

   
    Form.initStatus();
});
