var Form = {
    curStep: 1,
    curClinic: 1,
    countryData: [],
    defaultPhoneCode: '+273',
    dataStep1: [],
    dataStep2: [],
    baseUrl: $("#asset-url").val(),
    initClinicData: {},
    data: {},
    validSize: true,
    disable:false,
    dependentUrl: $("#ajaxUrlDependent").val(),
    singaporeCountryId: $('#singapore_id').val(),
    malaysiaCountryId: $('#malaysia_id').val(),
    initStatus: function () {
        jsCommon.viewLogs('#view_logs_anchor', '#admin_view_logs_url');
        $('.select-rx-fee').select2({
            placeholder: 'Select Amount'
        });

        $('.select-rx-fee').on('change',function(){
            var el = $(this).parents('.col-md-3').next();
            var input = el.find('input');
            if($(this).val() == 'Other'){
                el.removeClass('hidden');
                input.attr('required','required');
                input.val('');
            }else{
                el.addClass('hidden');
                input.removeAttr('required');
            }
        });

        $("#btnBackToStep2").on('click', function (e) {
            $(".last").removeClass('done');
            e.preventDefault();
            $("#admin-register-doctor-form-step2").attr('style', "display:block;");
            $("#admin-register-doctor-form-step1").attr('style', "display:none;");
            $("#admin-register-doctor-form-step3").attr('style', "display:none;");
        });

        $("#btnBackToStep1").on('click', function (e) {
            e.preventDefault();
            $("#admin-register-doctor-form-step2").attr('style', "display:none;");
            $("#admin-register-doctor-form-step1").attr('style', "display:block;");
            $("#admin-register-doctor-form-step3").attr('style', "display:none;");
            $(".second").removeClass('done');
            $(".last").removeClass('done');
        });

        $("#btSubmitData").on('click', function (e) {
            e.preventDefault();
            if(Form.disable){
                return;
            }
            $('#btnBackToStep2').addClass('disabled');
            $(this).addClass('disabled');

            var dependUrl = $("#ajax-url").val();

            if ($("#current-doctor-id").val()) {
                Form.data.append('doctor-id', $("#current-doctor-id").val());
            }

            if (Form.dataStep1.profile) {
                Form.data.append('profile', Form.dataStep1.profile);
            }

            if (Form.dataStep1.signature) {
                Form.data.append('signature', Form.dataStep1.signature);
            }

            if (Form.dataStep2.logo.length) {
                $.each(Form.dataStep2.logo, function (i, v) {
                    if (typeof v !== 'undefined') {
                        Form.data.append('clini-logo-' + i, v);
                    }
                });
            }
            Form.disable = true;
            $.ajax({
                type: "POST",
                url: dependUrl,
                data: Form.data,
                processData: false,
                contentType: false,
                beforeSend: function () {
                },
                success: function (data, textStatus, jqXHR) {
                    Form.disable = false;
                    if (data.success == 1) {
                        window.location.href = $("#success-url").val();
                    } else {
                        console.log(data);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Form.disable = false;
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                }
            });
        });

        if ($("#current-doctor-id").val()) {


            $("#admin_doctor_signature").removeAttr('required');

            $("#admin_doctor_signature").on('change', function () {
                $("#admin_doctor_signature").attr('required', 'required');
            });

            $("#admin_doctor_mainClinicLogo").removeAttr('required');

            $("#admin_doctor_mainClinicLogo").on('change', function (e) {
                $("#admin_doctor_mainClinicLogo").attr('required', 'required');
            });

            var dependUrl = $("#ajaxUrlDependent").val();

            $.ajax({
                type: "POST",
                url: dependUrl,
                data: {'type': 5, 'data': $("#current-doctor-id").val()},
                beforeSend: function () {
                },
                success: function (data, textStatus, jqXHR) {
                    if (data.total > 0) {
                        $.each(data.data, function (i, v) {
                            var t = Form.renderClinicWithdata(v);
                            var index = Form.curClinic;
                            $("#admin-register-doctor-form-step2").find('.form-body').find('.addition-content').append(t);
                            if($('.sub-clinic').length >= 4){
                                $('#btnAddClinicBlock').parent().addClass('hidden');
                            }
                            Form.getListCountryPhone("#phone-location" + index, v.phoneLocation);
                            Form.initCountrySelectBox("#country_" + index, v.country);
                            var dependUrl = $("#ajaxUrlDependent").val();
                            var dataPost = {'type': 1, 'data': v.country};
                            Form.callAjaxForSelect2(dependUrl, '#state' + index, dataPost, 'Select State / Province', v.state);
                            var dataPost = {'type': 2, 'data': v.state};
                            Form.callAjaxForSelect2(dependUrl, '#city' + index, dataPost, 'Select City', v.city);

                            Form.initClinicData[index] = v;
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                }
            });
        } else {
            $('.select-rx-fee').parents('.col-md-3').next().addClass('hidden');
            $("#admin_doctor_agentId").select2({placeholder: 'Select Agent Name'});
            $("#admin_doctor_localMedicalCountry").select2({placeholder: 'Select Country'});
            $("#admin_doctor_phoneLocation").select2({placeholder: 'Select Country'});
            $("#admin_doctor_localIdPassportCountry").select2({placeholder: 'Select Country'});
            $("#admin_doctor_bankCountryIssue").select2({placeholder: 'Select Country'});
            $("#admin_doctor_title").select2({placeholder: 'Select Title'});
        }

        $('.admin-doctor-agent-select').select2({
            templateResult: function (data, container) {
                var $agent = data.text;
                if ($(container).attr('class') != 'select2-results__group') {
                    var string = data.text;
                    string = string.replace('(', '<span style="color:red">(');
                    string = string.replace(')', ')</span>');
                    $agent = $(
                        '<span>' + string + '</span>'
                    );
                }
                return $agent;
            }
        });

        $('#admin_doctor_check3rd').on('ifChecked', function (event) {
            $("#admin_doctor_secondaryAgentId").removeAttr("disabled");
        });
        $('#admin_doctor_check3rd').on('ifUnchecked', function (event) {
            $("#admin_doctor_secondaryAgentId").attr("disabled", true);
        });
    },

    initStep1: function () {
        $("#admin-register-doctor-form-step2").attr('style', "display:none;");
        $("#admin-register-doctor-form-step1").attr('style', "display:block;");
        $("#admin-register-doctor-form-step3").attr('style', "display:none;");
        $("#btnvalidateInfo").on('click', function (e) {
            e.preventDefault();
            $("#admin-register-doctor-form-step1").submit();
        });
        $('#admin_doctor_specialization').multiSelect();
        $('.year-picker').datepicker({
            changeYear: true,
            showButtonPanel: true,
            format: 'yyyy',
            viewMode: "years", 
            minViewMode: "years",
        });
        $("#admin_doctor_bankCountryIssue").on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 7, 'data': this.value};
            if (this.value == Form.singaporeCountryId || this.value == Form.malaysiaCountryId) {
                Form.callAjaxForSelect2(dependUrl, '#bank_name', dataPost, 'Select Bank Name', '');
            } else {
                $('#admin_doctor_bankName').val('').show();
                $('#bank_name').removeAttr('required').next(".select2-container").hide();
                $('#admin_doctor_bankSwiftCode').val('').removeAttr('readonly');
            }

        });
        // $("#admin_doctor_bankStateIssue").on('change', function () {
        //     var dependUrl = $("#ajaxUrlDependent").val();
        //     var dataPost = {'type': 2, 'data': this.value};
        //     Form.callAjaxForSelect2(dependUrl, '#admin_doctor_bankCityIssue', dataPost, 'Select City', '');
        // });

        var currentBankCountry = $('#admin_doctor_bankCountryIssue').val();
        if (currentBankCountry != "" && (currentBankCountry == Form.singaporeCountryId || currentBankCountry == Form.malaysiaCountryId)) {
            $("#admin_doctor_bankCountryIssue").change();
        }
    },

    initStep2: function () {

        $(".select-gst-code").select2({
            placeholder: 'Select GST code',
            width: null
        });

        $('#admin_doctor_applyGstReviewLocal').on('ifChanged',function(e) {
            if($(this).is(":checked")) {
                $('#admin_doctor_gstCodeReviewLocal').attr('required', 'required');
                $('#admin_doctor_reviewLocalDate').attr('required', 'required');
            }else{
                $('#admin_doctor_reviewLocalDate').removeAttr('required', 'required');
                $('#admin_doctor_gstCodeReviewLocal').removeAttr('required', 'required');
            }
        });

        $('#admin_doctor_applyGstConsultLocal').on('ifChanged',function(e) {
            if($(this).is(":checked")) {
                $('#admin_doctor_gstCodeConsultLocal').attr('required', 'required');
                $('#admin_doctor_consultLocalDate').attr('required', 'required');
            }else{
                $('#admin_doctor_consultLocalDate').removeAttr('required', 'required');
                $('#admin_doctor_gstCodeConsultLocal').removeAttr('required', 'required');
            }
        });

        $('#admin_doctor_applyGstReviewInternational').on('ifChanged',function(e) {
            if($(this).is(":checked")) {
                $('#admin_doctor_gstCodeReviewInternational').attr('required', 'required');
                $('#admin_doctor_reviewInternationalDate').attr('required', 'required');
            }else{
                $('#admin_doctor_reviewInternationalDate').removeAttr('required', 'required');
                $('#admin_doctor_gstCodeReviewInternational').removeAttr('required', 'required');
            }
        });

        $('#admin_doctor_applyGstConsultInternational').on('ifChanged',function(e) {
            if($(this).is(":checked")) {
                $('#admin_doctor_gstCodeConsultInternational').attr('required', 'required');
                $('#admin_doctor_consultInternationalDate').attr('required', 'required');
            }else{
                $('#admin_doctor_consultInternationalDate').removeAttr('required', 'required');
                $('#admin_doctor_gstCodeConsultInternational').removeAttr('required', 'required');
            }
        });

        if ($("#admin_doctor_gstSetting_1").is(":checked")) {
            $('#admin_doctor_mainClinicGstNo').attr('disabled', true);
            $('#admin-register-doctor-form-step2 .date-picker').datepicker('remove');
            $('#admin-register-doctor-form-step2 .date-picker > .form-control').prop('disabled', true);
            $('#admin-register-doctor-form-step2 .date-picker .date-set').prop('disabled', true);
        }
     
        // apply change country change state
        $.each(this.initClinicData, function (index, value) {


            $("#phone-location" + index).select2({placeholder: 'Select Country'});

            $("#country_" + index).select2({placeholder: 'Select Country'});

            $("#country_" + index).val(value.country);
            $("#phone-location" + index).val(value.phoneLocation);

            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 1, 'data': value.country};
            Form.callAjaxForSelect2(dependUrl, '#state' + index, dataPost, 'Select State / Province', value.state);

            var dataPost = {'type': 2, 'data': value.state};
            Form.callAjaxForSelect2(dependUrl, '#city' + index, dataPost, 'Select City', value.city);


        });
        $("#admin_doctor_mainClinicCountry").on('change', function () {
            var dataPostState = {'type': 1, 'data': this.value};
            var dataPostCity = {'type': 6, 'data': this.value};
            Form.getListStateAndCity(Form.dependentUrl, '#admin_doctor_mainClinicState', '#admin_doctor_mainClinicCity', dataPostState, dataPostCity);
        });
        $("#admin_doctor_mainClinicState").on('change', function () {
            var dataPost = {'type': 2, 'data': this.value};
            Form.getListCity(Form.dependentUrl, '#admin_doctor_mainClinicCity', dataPost);

        });
        // $("#admin_doctor_mainClinicGstDate").datepicker();
        //validate before submit
        $("#btnvalidateClinic").on('click', function (e) {
            e.preventDefault();
            $("#admin-register-doctor-form-step2").submit();
        });
        // add new rule for select box
        $('#btnAddClinicBlock').on('click', function (e) {
            e.preventDefault();
            Form.addClinic();
        });
        $("#admin_doctor_mainClinicTelephoneLocation").select2({placeholder: 'Select Country'});
        $("#admin_doctor_mainClinicCountry").select2({placeholder: 'Select Country'});
        $("#admin_doctor_mainClinicState").select2({placeholder: 'Select  State / Province'});
        $("#admin_doctor_mainClinicCity").select2({placeholder: 'Select  City'});

        $('.fileinput').on('change.bs.fileinput', function() {
            $(this).find('span.error').remove();
        });

        $('.fileinput').on('max_size.bs.fileinput', function() {
            $(this).find('span.error').remove();
            $(this).append('<span class="error">Image need to be less than 1mb</span>');
        });
    },

    getListStateAndCity: function (dependUrl, targetState, targetCity, dataPostState, dataPostCity) {
        Form.callAjaxForSelect2(dependUrl, targetState, dataPostState, 'Select State / Province', '');
        Form.getListCity(dependUrl, targetCity, dataPostCity)
    },

    getListCity: function(dependUrl, targetCity, dataPost) {
        Form.callAjaxForSelect2(dependUrl, targetCity, dataPost, 'Select City', '');
    },

    getListCountryPhone: function (input, val) {
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
                    var t = '<option value> Select Country </option>';
                    $.each(data, function (index, value) {
                        var slect = '';
                        if (Form.defaultPhoneCode == value.id) {
                            slect = 'selected="selected"'
                        }
                        t += '<option ' + slect + '  value="' + value.id + '">' + value.name + ' (+' + value.phoneCode + ')</option>';
                    });
                    $(input).html(t);
                    $(input).val(val);
                    $(input).select2({placeholder: 'Select Country'});

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                }
            });
        } else {
            var data = Form.countryData;
            var t = '<option value> Select Country </option>';
            $.each(data, function (index, value) {
                var slect = '';
                if (Form.defaultPhoneCode == value.phoneCode) {
                    slect = 'selected="selected"'
                }
                t += '<option ' + slect + ' data-countrycode="' + value.code + '" value="' + value.phoneCode + '">' + value.name + ' (+' + value.phoneCode + ')</option>';
            });
            $(input).html(t);
            $(input).val(val);
            $(input).select2({placeholder: 'Select Country'});
        }

    },

    addClinic: function () {
        if($('.sub-clinic').length < 4){
            var t = this.renderEmptyClinic();
            $("#admin-register-doctor-form-step2").find('.form-body').find('.addition-content').append(t);
            this.initEventClinic();
        }
    },
    validLogo(e){
        var t = Form.checkDataUpload(e);
        var error = "<span class='error error-upload'>The file is invalid</span>"
        if(!t){
            var tag = e.parents(".col-md-4").first().find('.fileinput');
            tag.find('.error-upload').remove();
            tag.append(error);
        } else {
            var tag = e.parents(".col-md-4").first().find('.fileinput');

            tag.find('.error-upload').remove();
        }
    },
    initEventClinic: function () {
        //element event
        var index = this.curClinic;
        this.getListCountryPhone("#phone-location" + index);
        this.initCountrySelectBox("#country_" + index);
        // apply change country change state    
        this.countryChange(index);
        //add validate rule
        this.stateChange(index);
        $("#phone" + index).rules("add", {
            digits: true

        });

        $("#state" + index).select2({placeholder: 'Select State'});
        $("#city" + index).select2({placeholder: 'Select City'});

        $('.fileinput').on('change.bs.fileinput', function() {
            $(this).find('span.error').remove();
        });

        $('.fileinput').on('max_size.bs.fileinput', function() {
            $(this).find('span.error').remove();
            $(this).append('<span class="error">Image need to be less than 1mb</span>');
        });

        if($('.sub-clinic').length >= 4){
            $('#btnAddClinicBlock').parent().addClass('hidden');
        }
       
    },

    countryChange: function (index) {
        $("#country_" + index).on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 1, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#state' + index, dataPost, 'Select State / Province');
            dataPost = {'type': 6, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#city' + index, dataPost, 'Select City');
        });
    },

    stateChange: function (index) {
        $("#state" + index).on('change', function () {
            var dependUrl = $("#ajaxUrlDependent").val();
            var dataPost = {'type': 2, 'data': this.value};
            Form.callAjaxForSelect2(dependUrl, '#city' + index, dataPost, 'Select City');

        });
    },

    callAjaxForSelect2: function (url, target, dataPost, empty, val) {

        $.ajax({
            type: "POST",
            url: url,
            data: dataPost,
            beforeSend: function () {
                if (dataPost.type == 7) {
                    $('#admin_doctor_bankSwiftCode').val('');
                }
            },
            success: function (data, textStatus, jqXHR) {
                var t = '<option value>' + empty + '</option>';
                $.each(data, function (index, value) {
                    t += '<option value="' + index + '">' + value + '</option>';
                });
                $(target).html(t);
                $(target).val(val);
                $(target).select2({placeholder: empty});
            },
            complete: function () {
                if (dataPost.type == 7) {
                    $('#admin_doctor_bankName').hide();
                    $('#bank_name').show().addClass('select2').select2().change(function () {
                        var bankId = $(this).val();
                        Form.getBankSwiftCode(bankId);
                    }).attr('required', true);
                    if ($('#admin_doctor_bankName').val() != '') {
                        $('#bank_name').val($('#admin_doctor_bankName').val()).change();
                    }
                    $('#admin_doctor_bankSwiftCode').attr('readonly', true);
                }
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
                        $('#admin_doctor_bankSwiftCode').val(data.swift_code).valid();
                        $('#admin_doctor_bankName').val(bankId);
                    }
                }
            }
        });
    },

    initCountrySelectBox: function (input, val) {
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
                    var t = '<option value>Select Country</option>';
                    $.each(data, function (index, value) {
                        t += '<option  value="' + value.id + '">' + value.name + '</option>';
                    });
                    $(input).html(t);
                    $(input).val(val);
                    $(input).select2({placeholder: 'Select Country'});
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (typeof errorCallback == 'function')
                        return errorCallback(jqXHR, textStatus, errorThrown);
                    return false;
                }
            });
        } else {
            var data = Form.countryData;
            var t = '<option value>Select Country</option>';
            $.each(data, function (index, value) {
                t += '<option  value="' + value.id + '">' + value.name + '</option>';
            });
            $(input).html(t);
            $(input).val(val);
            $(input).select2({placeholder: 'Select Country'});
        }
    },

    renderEmptyClinic: function () {
        var index = this.curClinic;
        index++;
        var t = '<div class="form-content form-repeat sub-clinic"><div class="form-top-right pull-right"><a href="javascript:void(0);" class="btn btn-ahalf-circle text-uppercase red-fresh btn-icon-right btn-sm " onclick="removeblock(this)"> Delete<i class="fa fa-trash"></i></a>'
                + '</div><h4 class="block title-sub-clinic">' + "Details of Doctor\'s Sub-Clinic #"+ ($('.sub-clinic').length + 1) +"</h4>"
                + '<div class="form-group"><div class="col-md-4"><label>Clinic Name <span class="required" aria-required="true"> * </span></label>'
                + '<input type="text" data-identify = "name" id="name' + index + '" name="clinics[' + index + '][name]" required="required" placeholder="Enter Clinic Name" class="form-control">'
                + '</div></div><div class="form-group mb-grid"><div class="col-md-4"><label>Clinic Email <span class="required" aria-required="true"> * </span></label>'
                + '<input type="email" data-identify="email"  id="email' + index + '" name="clinics[' + index + '][email]" placeholder="Enter Email Address" required="required" class="form-control">'
                + '</div><div class="col-md-8"><label>Clinic Telephone Number <span class="required" aria-required="true"> * </span></label><div class="row"><div class="col-md-6"><div class=" dropdown-parent" id="dropdown-parent"><div class="form-float clearfix"><div class="col-md-5">'
                + '<select data-identify="phoneLocation" id="phone-location' + index + '" name="clinics[' + index + '][phoneLocation]" required="required" class="form-control select2"></select>'
                + '</div>'
                + '<div class="col-md-7">'
                + '<input type="text" data-identify="phoneNumber"  id="phone' + index + '"   name="clinics[' + index + '][phoneNumber]" required="required" placeholder="" class="form-control phone-num">'
                + '</div>'
                + '</div><div class="phone-error-notice"></div></div></div></div></div></div><div class="form-group mb-grid"><div class="col-md-4"><label>Address Line 1 <span class="required" aria-required="true"> * </span></label>'
                + '<input type="text" data-identify="address1" id="adress1' + index + '"  name="clinics[' + index + '][address1]"  required="required" placeholder="Enter Address" class="form-control">'
                + '</div><div class="col-md-4"><label>Address Line 2</label>'
                + '<input type="text" id="adress2' + index + '"  name="clinics[' + index + '][address2]"  placeholder="Enter Address" class="form-control">'
                + '</div> <div class="col-md-4"><label>Address Line 3</label>'
                + '<input type="text" placeholder="Enter Address" id="adress3' + index + '"  name="clinics[' + index + '][address3]" class="form-control">'
                + '</div></div><div class="form-group mb-grid"><div class="col-md-4"><label>Country <span class="required" aria-required="true"> * </span></label>'
                + '<select data-identify="country"  id="country_' + index + '" name="clinics[' + index + '][country]" class="form-control select2" required="required" onchange="Form.getListStateAndCity(\'' + Form.dependentUrl + '\', \'#state' + index + '\', \'#city' + index + '\',{\'type\': 1, \'data\': this.value}, {\'type\': 6, \'data\': this.value})" ></select>'
                + '</div><div class="col-md-4"><label>State / Province</label>'
                + '<select data-identify="state"  id="state' + index + '" name="clinics[' + index + '][state]" class="form-control select2" onchange="Form.getListCity(\'' + Form.dependentUrl + '\', \'#city' + index + '\', {\'type\': 2, \'data\': this.value})"><option value>Select State / Province</option></select>'
                + '</div><div class="col-md-4"><label>City <span class="required" aria-required="true"> * </span></label>'
                + '<select data-identify="city"  id="city' + index + '"  name="clinics[' + index + '][city]" class="form-control select2" required="required"><option value>Select City</option></select>'
                + '</div></div><div class="form-group mb-grid"><div class="col-md-4"><label>Zip / Postal Code <span class="required" aria-required="true"> * </span></label>'
                + '<input type="text" data-identify="zipCode" id="zip-code' + index + '" required="required"   name="clinics[' + index + '][zipCode]" placeholder="Enter Zip / Postal Code" class="form-control"> </div>'
                + '</div>'

                + '<h4 class="block">Clinic Logo <span class="text-normal">(for E-prescription)</span></h4><div class="form-group"><div class="col-md-4"> <label>Upload Clinic logo <span class="required" aria-required="true"> * </span></label><div class="row"><div class="col-md-12">'
                + '<div class="fileinput fileinput-new clearfix" data-provides="fileinput" data-max-size="1" id="clinic-logo-' + index + '">'
                + '<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">'
                + '<img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" /> </div>'
                + '<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div><div>'
                + '<span class="btn btn-outline blue btn-ahalf-circle btn-sm text-uppercase btn-file"><span class="fileinput-new"> Select image </span><span class="fileinput-exists"> Change </span>'
                + '<input type="file" class="logo-clinic" id="logo' + index + '" data-identify="logo" required="required"  name="clinics[' + index + '][logo]"> </span>'
                + '<a href="javascript:;" class="btn btn-outline red btn-ahalf-circle btn-sm text-uppercase fileinput-exists" data-dismiss="fileinput"> Remove </a>'
                + '</div></div></div><div class="col-md-12"><div class="error-logo"> </div> </div></div> </div></div> </div>';
        this.curClinic = index;

        return t;
    },

    renderClinicWithdata: function (clinic) {
        var index = this.curClinic;
        index++;
        var d = new Date();
        var n = d.getTime();
        var t = '<div class="form-content form-repeat sub-clinic"><div class="form-top-right pull-right"><a href="javascript:void(0);" class="btn btn-ahalf-circle text-uppercase red-fresh btn-icon-right btn-sm " onclick="removeblock(this,' + index + ')"> Delete<i class="fa fa-trash"></i></a>'
                + '</div><h4 class="block title-sub-clinic">' + "Details of Doctor's Sub-Clinic #"+ ($('.sub-clinic').length + 1) +"</h4>"
                + '<input type="hidden" data-identify = "id" id="id' + index + '" name="clinics[' + index + '][id]" required="required" value="' + clinic.id + '" placeholder="Enter Clinic Name" class="form-control">'
                + '<div class="form-group"><div class="col-md-4"><label>Clinic Name <span class="required" aria-required="true"> * </span></label>'
                + '<input type="text" data-identify = "name" id="name' + index + '" name="clinics[' + index + '][name]" required="required" value="' + clinic.name + '" placeholder="Enter Clinic Name" class="form-control">'
                + '</div></div><div class="form-group mb-grid"><div class="col-md-4"><label>Clinic Email <span class="required" aria-required="true"> * </span></label>'
                + '<input type="email" data-identify="email"  id="email' + index + '" name="clinics[' + index + '][email]" value="' + clinic.email + '" placeholder="Enter Email Address" required="required" class="form-control">'
                + '</div><div class="col-md-8"><label>Clinic Telephone Number <span class="required" aria-required="true"> * </span></label><div class="row"><div class="col-md-6"><div class=" dropdown-parent" id="dropdown-parent"><div class="form-float clearfix"><div class="col-md-5">'
                + '<select data-identify="phoneLocation" id="phone-location' + index + '" name="clinics[' + index + '][phoneLocation]" required="required" class="form-control select2"></select>'
                + '</div>'
                + '<div class="col-md-7">'
                + '<input type="text" data-identify="phoneNumber" value="' + clinic.phoneNumber + '" id="phone' + index + '"   name="clinics[' + index + '][phoneNumber]" required="required" placeholder="" class="form-control phone-num">'
                + '</div>'
                + '</div><div class="phone-error-notice"></div></div></div></div></div></div><div class="form-group mb-grid"><div class="col-md-4"><label>Address Line 1 <span class="required" aria-required="true"> * </span></label>'
                + '<input type="text" data-identify="address1" id="adress1' + index + '" value="' + clinic.line1 + '"    name="clinics[' + index + '][address1]"  required="required" placeholder="Enter Address" class="form-control">'
                + '</div><div class="col-md-4"><label>Address Line 2</label>'
                + '<input type="text" id="adress2' + index + '"  name="clinics[' + index + '][address2]" value="' + clinic.line2 + '"    placeholder="Enter Address" class="form-control">'
                + '</div> <div class="col-md-4"><label>Address Line 3</label>'
                + '<input type="text" placeholder="Enter Address" id="adress3' + index + '"  name="clinics[' + index + '][address3]" value="' + clinic.line3 + '"  class="form-control">'
                + '</div></div><div class="form-group mb-grid"><div class="col-md-4"><label>Country <span class="required" aria-required="true"> * </span></label>'
                + '<select data-identify="country"  id="country_' + index + '" name="clinics[' + index + '][country]" class="form-control select2" required="required" onchange="Form.getListStateAndCity(\'' + Form.dependentUrl + '\', \'#state' + index + '\', \'#city' + index + '\', {\'type\': 1, \'data\': this.value}, {\'type\': 6, \'data\': this.value})"></select>'
                + '</div><div class="col-md-4"><label>State / Province</label>'
                + '<select data-identify="state"  id="state' + index + '" name="clinics[' + index + '][state]" class="form-control select2" onchange="Form.getListCity(\'' + Form.dependentUrl + '\', \'#city' + index + '\', {\'type\': 2, \'data\': this.value})"><option value="empty">Select State / Province</option></select>'
                + '</div><div class="col-md-4"><label>City <span class="required" aria-required="true"> * </span></label>'
                + '<select data-identify="city"  id="city' + index + '"  name="clinics[' + index + '][city]" class="form-control select2" required="required"><option value="empty">Select City</option></select>'
                + '</div></div><div class="form-group mb-grid"><div class="col-md-4"><label>Zip / Postal Code <span class="required" aria-required="true"> * </span></label>'
                + '<input type="text" data-identify="zipCode" required="required" id="zip-code' + index + '" value="' + clinic.zipCode + '"   name="clinics[' + index + '][zipCode]" placeholder="Enter Zip / Postal Code" class="form-control"> </div>'
                + '</div>'

                + '<h4 class="block">Clinic Logo <span class="text-normal">(for E-prescription)</span></h4><div class="form-group"><div class="col-md-4"> <label>Upload Clinic logo <span class="required" aria-required="true"> * </span></label><div class="row"><div class="col-md-12">'
                + '<div class="fileinput fileinput-new clearfix" data-provides="fileinput" data-max-size="1" id="clinic-logo-' + index + '">'
                + '<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">'
                + '<img src="' + this.baseUrl + clinic.logo + '?' + n + '" alt="" /> </div>'
                + '<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div><div>'
                + '<span class="btn btn-outline blue btn-ahalf-circle btn-sm text-uppercase btn-file"><span class="fileinput-new"> Select image </span><span class="fileinput-exists"> Change </span>'
                + '<input type="file" id="logo' + index + '" data-identify="logo" name="clinics[' + index + '][logo]"> </span>'
                + '<a href="javascript:;" class="btn btn-outline red btn-ahalf-circle btn-sm text-uppercase fileinput-exists" data-dismiss="fileinput"> Remove </a>'
                + '</div></div></div><div class="col-md-12"><div class="error-logo"> </div> </div></div> </div></div> </div>';
        this.curClinic = index;

        return t;
    },

    validateInfor: function () {
        var validobj = $("#admin-register-doctor-form-step1").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {
                    case 'admin_doctor[phoneLocation]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_doctor[phone]':
                        var tag = $e.parents(".row").first().find('.phone-error-notice');
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_doctor[gender]':
                        $("#admin_doctor_gender").parent().append(error);
                        break;
                    case 'admin_doctor[localMedicalCountry]':

                        $e.parent().append(error);
                        break;
                    case 'admin_doctor[localMedicalDate]':
                        $e.parents(".col-md-4").append(error);
                        break;
                    case 'admin_doctor[localIdPassportDate]':
                        $e.parents(".col-md-4").append(error);
                        break;
                    case 'admin_doctor[signature]':
                        var tag = $e.parents(".col-md-4").first().find('.fileinput');
                        tag.append(error);
                        break;
                    case 'admin_doctor[specialization][]':
                        var tag = $e.parents(".multi-select-col-4").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[bankCountryIssue]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'admin_doctor[bankName]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'bank_name':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'admin_doctor[bankCityIssue]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'admin_doctor[agentId]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'admin_doctor[secondaryAgentId]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    case 'admin_doctor[title]':
                        var tag = $e.parent();
                        tag.append(error);
                        break;
                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                
                'admin_doctor[phone]': {
                    digits: true
                },
                'admin_doctor[email]': {
                    unique: true
                },
                'admin_doctor[rxReviewFeeLocal]':{
                    decimal:true
                },
                'admin_doctor[rxReviewFeeInternational]':{
                    decimal:true
                },
                'admin_doctor[rxFeeLiveConsultLocal]':{
                    decimal:true
                },
                'admin_doctor[rxFeeLiveConsultInternational]':{
                    decimal:true
                },
                'admin_doctor[accountNumber]': {
                    digits: true
                },
               'admin_doctor[lastName]': {
                    maxlength: 18
                },
                'admin_doctor[displayName]': {
                    maxlength: 18
                },
                'admin_doctor[secondaryAgentId]': {
                    uniqueAgent: true
                }
            },
            submitHandler: function (form) {
                Form.dataStep1.profile = '';        
                Form.dataStep1.signature = '';       
                Form.gotoStep2(form);
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
        $(document).on('change', '.select2', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });
        $("#admin_doctor_phone").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });

        $("#admin_doctor_localMedicalDate").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });
        $("#admin_doctor_specialization").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });



    },

    validateClinic: function () {

        var validobj = $("#admin-register-doctor-form-step2").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;

                switch (element.attr("name")) {
                    case 'admin_doctor[mainClinicTelephoneLocation]':
                        var tag = $e.parents(".row").first().find(".phone-error-notice");
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_doctor[mainClinicPhone]':
                        var tag = $e.parents(".row").first().find(".phone-error-notice");
                        tag.html('');
                        tag.append(error);
                        break;
                    case 'admin_doctor[gstSetting]':
                        $e.parents('.col-md-4').append(error);
                        break;
                    case 'admin_doctor[mainClinicCountry]':
                        $e.parents(".col-md-4").append(error);
                        break;
                    case 'admin_doctor[mainClinicCity]':
                        $e.parent().append(error);
                        break;
                    case 'admin_doctor[mainClinicGstDate]':
                        $e.parents('.col-md-4').first().append(error);
                        break;
                    case 'admin_doctor[mainClinicLogo]':
                        var tag = $e.parents(".col-md-4").first().find('.fileinput');
                        tag.append(error);
                        break;
                    case 'admin_doctor[reviewLocalDate]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[gstCodeReviewLocal]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[gstCodeConsultLocal]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[consultLocalDate]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[gstCodeMedicineInternational]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[medicineInternationalDate]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[gstCodeReviewInternational]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[reviewInternationalDate]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[gstCodeConsultInternational]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    case 'admin_doctor[consultInternationalDate]':
                        var tag = $e.parents("td").first();
                        tag.append(error);
                        break;
                    default :
                        error.insertAfter(element);
                        break;
                }

                $("select[id^=phone-location]").each(function (i, e) {
                    var index = i + 2;
                    if (element.attr('id') == 'phone-location' + index) {
                        var tag = $e.parents(".row").first().find(".phone-error-notice");
                        tag.html('');
                        tag.append(error);
                    }
                });
                $("input[id^=phone-area]").each(function (i, e) {
                    var index = i + 2;
                    if (element.attr('id') == 'phone-area' + index) {
                        var tag = $e.parents(".row").first().find(".phone-error-notice");
                        tag.html('');
                        tag.append(error);
                    }
                });

                $("input[id^=phone]").each(function (i) {
                    var index = i + 2;
                    if (element.attr('id') == 'phone' + index) {
                        var tag = $e.parents(".row").first().find(".phone-error-notice");
                        tag.html('');
                        tag.append(error);
                    }
                });

                $("select[id^=country]").each(function (i, e) {
                    var index = i + 2;
                    if (element.attr('id') == 'country_' + index) {
                        var tag = $e.parent();
                        tag.append(error);
                    }
                });
                $("select[id^=city]").each(function (i, e) {
                    var index = i + 2;
                    if (element.attr('id') == 'city' + index) {
                        var tag = $e.parent();
                        tag.append(error);
                    }
                });
                $("input[name *=logo]").each(function (i) {
                    var index = i + 2;
                    if (element.attr('name') == 'clinics[' + index + '][logo]') {
                        var tag = $e.parents(".row").first().find('.error-logo');
                        tag.append(error);
                    }
                });
            },
            rules: {
                'admin_doctor[mainClinicPhone]': {
                    digits: true
                }

            },
            submitHandler: function (form) {
                if(Form.validSize) {
                    Form.getDataStep2();
                    Form.gotoStep3(form);
                }
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
        $(document).on('click', 'input[name="admin_doctor[gstSetting]"]', function () {
            if ($("#admin_doctor_gstSetting_0").is(":checked")) {
                $('#admin-register-doctor-form-step2 .date-picker').datepicker({
                    rtl: App.isRTL(),
                    orientation: "left",
                    autoclose: true,
                    format: 'd M yy'
                });
                $('#admin-register-doctor-form-step2 .date-picker > .form-control').removeAttr('disabled');
                $('#admin-register-doctor-form-step2 .date-picker .date-set').removeAttr('disabled');
                $('#admin_doctor_mainClinicGstNo').removeAttr('disabled');
                $('#gst-apply').removeClass('hide');
            } else {
                if ($("#current-doctor-id").val()) {
                    $('#admin_doctor_mainClinicGstNo').attr('disabled', true);
                    if($('#admin_doctor_mainClinicGstNo').hasClass('error')) {
                        $('#admin_doctor_mainClinicGstNo').removeClass('error');
                    }
                    if($("#current-gst-status").val() == 1){
                        $('#admin-register-doctor-form-step2 .date-picker').datepicker('remove');
                        $('#admin-register-doctor-form-step2 .date-picker > .form-control').prop('disabled', true);
                        $('#admin-register-doctor-form-step2 .date-picker .date-set').prop('disabled', true);
                    
                    }
                } else {
                    $('#admin-register-doctor-form-step2 .date-picker').datepicker('remove');
                    $('#admin-register-doctor-form-step2 .date-picker > .form-control').prop('disabled', true);
                    $('#admin-register-doctor-form-step2 .date-picker .date-set').prop('disabled', true);
                    $('#admin_doctor_mainClinicGstNo').attr('disabled', true);
                    if($('#admin_doctor_mainClinicGstNo').hasClass('error')) {
                        $('#admin_doctor_mainClinicGstNo').removeClass('error');
                    }
                    if($('#admin_doctor_mainClinicGstDate').hasClass('error')) {
                        $('#admin_doctor_mainClinicGstDate').removeClass('error');
                    }
                }
                $('#gst-apply').addClass('hide');
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
        $(document).on('change', '.phone-area', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });
        $(document).on('change', '.phone-num', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });

        $("#admin_doctor_mainClinicGstDate").on('change', function () {
            if (!$.isEmptyObject(validobj.submitted)) {
                validobj.form();
            }
        });

    },

    gotoStep2: function (form)
    {
        $("#admin-register-doctor-form-step2").attr('style', "display:block;");
        $("#admin-register-doctor-form-step1").attr('style', "display:none;");
        $("#admin-register-doctor-form-step3").attr('style', "display:none;");
        $(".second").addClass('done');
        $(".last").removeClass('done');
        $('html, body').animate({
            scrollTop: $("#form_wizard_1").offset().top
        }, 200);
        this.initStep2();
        this.validateClinic();

    },

    getDataStep2: function () {
        this.dataStep2.logo = [];
        var imgname = $('#admin_doctor_mainClinicLogo').val();
        if (imgname) {
            var ext = imgname.substr((imgname.lastIndexOf('.') + 1));
            var size = $('#admin_doctor_mainClinicLogo')[0].files[0].size;
            if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG') {
                if (size <= 1000000) {
                    this.dataStep2.logo[1] = $('#admin_doctor_mainClinicLogo')[0].files[0];
                }
            }
        }
        var index = this.curClinic;
        if (index == 1) {
            return;
        }

        for (var i = 2; i <= index; i++) {
            var imgname = $('#logo' + i).val();
            if (imgname) {
                var ext = imgname.substr((imgname.lastIndexOf('.') + 1));
                var size = $('#logo' + i)[0].files[0].size;
                if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG')
                {
                    if (size <= 1000000) {
                        this.dataStep2.logo[i] = $('#logo' + i)[0].files[0];
                    }
                }
            }
        }


    },

    gotoStep3: function (form) {
        if(Form.disable){
            return;
        }
        var dependUrl = $("#ajaxUrlDependent").val();
        var form_data = new FormData();
        var step1 = $("#admin-register-doctor-form-step1").serializeArray();
        $.each(step1, function (index, value) {
            form_data.append(value.name, value.value);
        });
        var step2 = $("#admin-register-doctor-form-step2").serializeArray();
        $.each(step2, function (index, value) {
            form_data.append(value.name, value.value);
        });
        this.data = form_data;
        form_data.append('type', 4);
        Form.disable = true;
        $.ajax({
            type: "POST",
            url: dependUrl,
            data: form_data,
            contentType: false,
            processData: false,
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                Form.disable = false;
                $("#admin-register-doctor-form-step3").find('.form-content').html(data);
                $(".last").addClass('done');
                $("#admin-register-doctor-form-step2").attr('style', "display:none;");
                $("#admin-register-doctor-form-step1").attr('style', "display:none;");
                $("#admin-register-doctor-form-step3").attr('style', "display:block;");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                Form.disable = false;
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus) {
                if ($('#main-clinic-logo').find('.fileinput-preview img').length > 0) {
                    $("#main-logo-preview").attr('src', $('#main-clinic-logo').find('.fileinput-preview img').attr('src'));
                } else {
                    $("#main-logo-preview").attr('src', $('#main-clinic-logo').find('.fileinput-new img').attr('src'));
                }                
                $("#main-logo-preview-profile").parents('.personal-information-wrap').first().remove();
                $("#main-logo-preview-signature").parents('.personal-information-wrap').first().remove();
                for (var index = 2; index <= Form.curClinic; index++) {
                    if ($('#clinic-logo-' + index).find('.fileinput-preview img').length > 0) {
                        $("#main-logo-preview-" + index).attr('src', $('#clinic-logo-' + index).find('.fileinput-preview img').attr('src'));

                    } else {
                        $("#main-logo-preview-" + index).attr('src', $('#clinic-logo-' + index).find('.fileinput-new img').attr('src'));
                    }
                }
            }
        });

    }
    ,
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
            
        }
        Form.validSize = true;
            return true;
    }

}

$(document).ready(function () {
    jQuery.validator.addMethod("empty", function (value, element) {
        var check =  value != 'empty@gmail.com';
        console.log(check);
        return check;
    }, "This field is unique");
    jQuery.validator.addMethod("validUpload", function (value, element) {
        return Form.validSize;
    }, "The file is too big");
    jQuery.validator.addMethod("unique", function (value, element) {
        var check = false;
        if($("#current-doctor-id").val() == '') {
            $.ajax({
                type: "POST",
                url: $("#validate-email-url").val(),
                data: {data: value,id: $("#current-doctor-id").val(),type: 1},
                async: false,
                success: function (data, textStatus, jqXHR) {

                    check = data.success;
                }
            });
        } else {
            check = true;
        }

        
        return check;
        
    }, "This value must be unique");

    jQuery.validator.addMethod("uniqueAgent", function (value, element) {
        var check = true;
        if($("#admin_doctor_agentId").val() == value) {
            check   = false;
        }
        return check;

    }, "This value must not be the same with the primary agent");
    $.validator.addMethod('decimal', function(value, element) {
        return this.optional(element) || /^(\d+(?:[\.]\d{1,2})?)$/.test(value);
    }, "Please enter a correct number, format xxxx.xx");
    $.validator.addMethod("greaterThan", function (value, element ,param) {
           
            return value > param;
    }, "Value must be greater than {0}" );
    Form.initStatus();
    Form.initStep1();
    Form.validateInfor();
    // Form.gotoStep2();
});

function removeblock(e)
{
    $(e).parents('.form-repeat').first().remove();
    if($('.sub-clinic').length < 4){
        $('#btnAddClinicBlock').parent().removeClass('hidden');
    }
    $('.sub-clinic').each(function(index){
        $(this).find('.title-sub-clinic').html('Details of Doctor\'s Sub-Clinic #' + (index + 1));
    });
}
