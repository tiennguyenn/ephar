var Form = {
    baseUrl: $("#asset-url").val(),
    useCompany: false,
    data: {},
    validSize: true,
    dependentUrl: $("#ajaxUrlDependent").val(),
    singaporeCountryId: $('#singapore_id').val(),
    malaysiaCountryId: $('#malaysia_id').val(),
    gstChoice: function () {
        if ($("#admin_agent_gstSetting_0").is(":checked")) {
            $('#admin_agent_gstEffectDate').removeAttr('disabled');
            $('#admin_agent_gstNo').removeAttr('disabled');
        } else {
            $('#admin_agent_gstEffectDate').attr('disabled', true);
            $('#admin_agent_gstNo').attr('disabled', true);
        }
    },
    initStatus: function () {
        var date = new Date();
        date.setDate(date.getDate());
        $('.datepicker-min-valid').datepicker({ 
            startDate: date,
            orientation: "left",
            autoclose: true,
            format: 'd M yy'
        }).on('keypress paste', function (e) {
            e.preventDefault();
            return false;
        });
        Form.gstChoice();
        $(document).on('click', 'input[name="admin_agent[gstSetting]"]', function () {
            Form.gstChoice();
        });
        jsCommon.viewLogs('#view_logs_anchor', '#admin_view_logs_url');
        $('.detect-change').each(function () {
            $(this).data('oldValue', $(this).val())
        });


        $("#admin-register-agent-submit").on('click', function (e) {
            e.preventDefault();
            $("#admin-register-agent").submit();
        });
        this.validateInfor();
        $("#admin_agent_country").on('change', function () {
            if(Form.useCompany|| this.value == '') {
                return;
            }
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 1, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_state', dataPost, 'Select State / Province');
            dataPost = {'type': 6, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_city', dataPost, 'Select City');
        });
        $("#admin_agent_state").on('change', function () {
            if(Form.useCompany|| this.value == '') {
                return;
            }
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 2, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_city', dataPost, 'Select City');
        });

        $("#admin_agent_comCountry").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 1, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_comState', dataPost, 'Select State / Province');
            dataPost = {'type': 6, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_comCity', dataPost, 'Select City');
        });
        $("#admin_agent_comState").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 2, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_comCity', dataPost, 'Select City');
        });

        $("#admin_agent_bankCountryIssue").on('change', function () {
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

        var currentBankCountry = $('#admin_agent_bankCountryIssue').val();
        if (currentBankCountry != "" && (currentBankCountry == Form.singaporeCountryId || currentBankCountry == Form.malaysiaCountryId)) {
            $("#admin_agent_bankCountryIssue").change();
        }
        // $("#admin_agent_bankStateIssue").on('change', function () {
        //     var dependUrl = $("#ajaxUrlDependent").val();
        //     var dataPost = {'type': 2, 'data': this.value};
        //     Form.callAjaxForSelect2(dependUrl, '#admin_agent_bankCityIssue', dataPost, 'Select City', true);
        // });

        if ($("#main-agent-id").val()) {
            $("#admin_agent_logo").removeAttr('required');

            $("#admin_agent_logo").on('change', function () {
                $("#admin_agent_logo").attr('required', 'required');
            });
            // var dependUrl = $("#ajaxUrlDependent").val();
            // var dataPost = {'type': 1, 'data': $('#admin_agent_bankCountryIssue').val()};
            // Form.callAjaxForSelect2(dependUrl, $('#admin_agent_bankStateIssue'), dataPost, 'Select State', $('#admin_agent_bankStateIssue').val(), true);
            // if($('#admin_agent_bankStateIssue').val() == ''){
            //     var dataPost = {'type': 6, 'data': $('#admin_agent_bankCountryIssue').val()};
            // }else{
            //     var dataPost = {'type': 2, 'data': $('#admin_agent_bankStateIssue').val()};
            // }
            // Form.callAjaxForSelect2(dependUrl, $('#admin_agent_bankCityIssue'), dataPost, 'Select City', $('#admin_agent_bankCityIssue').val(), true);
        } else {
            $("#admin_agent_state").select2({placeholder: 'Select State / Province'});
            $("#admin_agent_comState").select2({placeholder: 'Select State / Province'});
            $("#admin_agent_comPhoneLocation").select2({placeholder: 'Country'});
            $("#admin_agent_country").select2({placeholder: 'Select Country'});
            $("#admin_agent_comCountry").select2({placeholder: 'Select Country'});
            $("#admin_agent_phoneLocation").select2({placeholder: 'Country'});
            $("#admin_agent_localIdPassportCountry").select2({placeholder: 'Select Country'});
            $("#admin_agent_bankCountryIssue").select2({placeholder: 'Select Country'});
            $("#admin_agent_comCity").select2({placeholder: 'Select City'});
            $("#admin_agent_city").select2({placeholder: 'Select City'});
            $("#admin_agent_bankCityIssue").select2({placeholder: 'Select City'});
            $("#admin_agent_site").select2({placeholder: 'Select a Site'});
        }
        $('#admin_agent_checkAddress').on('ifChecked', function (event) {
            Form.useCompany = true;
            Form.refillCompanyAddress();
            Form.addDisable();
        });
        $('#admin_agent_checkAddress').on('ifUnchecked', function (event) {
            Form.useCompany = false;
            Form.resetCompanyAddress();
            Form.removeDisable();
        });
        
    },

    addDisable(){
        $("#admin_agent_country").attr('disabled',true);
        $("#admin_agent_state").attr('disabled',true);
        $("#admin_agent_city").attr('disabled',true);
        $("#admin_agent_addressLine1").attr('disabled',true);
        $("#admin_agent_addressLine2").attr('disabled',true);
        $("#admin_agent_addressLine3").attr('disabled',true);
        $("#admin_agent_zipCode").attr('disabled',true);

    },
    refillCompanyAddress(){
        var dependUrl = $("#ajaxUrlDependent").val();
        var currentCountry = $("#admin_agent_comCountry").val();
        $("#admin_agent_country").val(currentCountry).trigger('change');
        if(currentCountry != ""){
            var dataPost = {'type': 1, 'data': currentCountry};
            Form.callAjaxForSelect2(dependUrl, '#admin_agent_state', dataPost, 'Select State / Province',$("#admin_agent_comState").val() );
            if($("#admin_agent_comState").val()  == ""){
                dataPost = {'type': 6, 'data': currentCountry};
                Form.callAjaxForSelect2(dependUrl, '#admin_agent_city', dataPost, 'Select City', $("#admin_agent_comCity").val());
            } else {
                var dependUrl = $("#ajaxUrlDependent").val();
                var dataPost = {'type': 2, 'data': $("#admin_agent_comState").val()};
                Form.callAjaxForSelect2(dependUrl, '#admin_agent_city', dataPost, 'Select City', $("#admin_agent_comCity").val());
            }
        }
        $("#admin_agent_addressLine1").val($("#admin_agent_comAddressLine1").val());
        $("#admin_agent_addressLine2").val($("#admin_agent_comAddressLine2").val());
        $("#admin_agent_addressLine3").val($("#admin_agent_comAddressLine3").val());
        $("#admin_agent_zipCode").val($("#admin_agent_comZipCode").val());



    },
    resetCompanyAddress(){
        $("#admin_agent_country").val("").trigger('change');
        $("#admin_agent_city").val("").trigger('change');
        $("#admin_agent_state").val("").trigger('change');
        $("#admin_agent_addressLine1").val("");
        $("#admin_agent_addressLine2").val("");
        $("#admin_agent_addressLine3").val("");
        $("#admin_agent_zipCode").val("");
    },
    removeDisable(){
        $("#admin_agent_state").removeAttr('disabled');
        $("#admin_agent_country").removeAttr('disabled');
        $("#admin_agent_city").removeAttr('disabled');
        $("#admin_agent_addressLine1").removeAttr('disabled');
        $("#admin_agent_addressLine2").removeAttr('disabled');
        $("#admin_agent_addressLine3").removeAttr('disabled');
        $("#admin_agent_zipCode").removeAttr('disabled');
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

    callAjaxForSelect2: function (url, target, dataPost, empty, val, allowClear = false) {
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
                    placeholder: empty,
                    allowClear: allowClear
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
        var validobj = $("#admin-register-agent").validate({
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
                    case 'admin_agent[phone]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_agent[comPhoneLocation]':
                        var tag = $e.parents(".row").first().find('.phone-company-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_agent[comPhone]':
                        var tag = $e.parents(".row").first().find('.phone-company-error-notice');
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
                    case 'admin_agent[bankCountryIssue]':

                        $e.parent().append(error);
                        break;
                    case 'admin_agent[bankName]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'bank_name':
                        var tag = $e.parent();
                        tag.append(error);
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

                    case 'admin_agent[fees][0][newAgentFee]':
                    case 'admin_agent[fees][1][newAgentFee]':
                    case 'admin_agent[fees][0][takeEffectOn]':
                    case 'admin_agent[fees][1][takeEffectOn]':
                        $e.parent().after(error);
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
                },
                'admin_agent[email]': {
                    unique: true
                },
                'admin_agent[logo]': {
                    validUpload: true
                },
                'admin_agent[gstNo]': {
                    required: true
                },
                'admin_agent[gstEffectDate]': {
                    required: true
                },
                'admin_agent[accountNumber]': {
                    digits: true
                }
            },
            submitHandler: function (form) {
                var isChanged = false;
                $('.detect-change').each(function () {
                    if( $(this).val() != $(this).data('oldValue') ) {
                        isChanged = true;
                        $("#detect_changed_hf").val(true);
                        return false;
                    } else {
                        $("#detect_changed_hf").val(false);
                    }
                });
                $(form).find('.button-back').addClass('disabled');
                $("#admin-register-agent-submit").addClass('disabled');
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
                    var t = elem.parent().find(".select2-selection").first();
                    t.attr('style', '');
                } else {
                    elem.removeClass(errorClass);
                }
            }

        });
        $("#admin_agent_logo").on('change', function() {
            Form.checkDataUpload($(this));
        });
    },
    checkDataUpload(ele){
        var imgname = ele.val();
        if (imgname) {
            var ext = imgname.substr((imgname.lastIndexOf('.') + 1));
            var size = ele[0].files[0].size;
            if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG') {
                if (size <= 10000000) {
                    Form.validSize = true;
                    return true;
                } else {
                    Form.validSize = false;
                    return false;
                }
            }
            Form.validSize = false;
        }
    }
};

$(document).ready(function () {
    jQuery.validator.addMethod("empty", function (value, element) {
        return value != 'empty';
    }, "This field is required");
    jQuery.validator.addMethod("validUpload", function (value, element) {
        return Form.validSize;
    }, "The file is invalid");
    jQuery.validator.addMethod("unique", function (value, element) {
        var check = false;
        $.ajax({
            type: "POST",
            url: $("#validate-email-url").val(),
            data: {data: value, id: $("#main-agent-id").val(), type: 2},
            async: false,
            success: function (data, textStatus, jqXHR) {

                check = data.success;
            }
        });

        return check;

    }, "This value must be unique");

    Form.initStatus();
});
