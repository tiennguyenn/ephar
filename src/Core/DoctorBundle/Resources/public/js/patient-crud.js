
var PatientCrud = {
    birthDay: $('#utilbundle_patient_personalInformation_dateOfBirth_day'),
    birthMonth: $('#utilbundle_patient_personalInformation_dateOfBirth_month'),
    birthYear: $('#utilbundle_patient_personalInformation_dateOfBirth_year'),
    taxInput: $('#utilbundle_patient_taxId'),
    init: function() {
        PatientCrud.bootstrap();
        PatientCrud.onSubmitForm();
        PatientCrud.onClickAddAllergyLink();
        PatientCrud.displayTaxField();
        jsCommon.viewLogs('#view_logs_anchor', '#admin_view_logs_url');
        $(document).on('click', '#modal-patient-ok', function () {
            $('#saveBtn').addClass('disabled');
            $('#saveAndCreatRxBtn').addClass('disabled');
            $('#patientForm').submit();
        });
    },
    displayTaxField: function () {
        $taxDiv = $('#tax_id_div');
        $taxDrop = $("#utilbundle_patient_nationality"); 
        country = $("#utilbundle_patient_nationality").find("option:selected").text();
        if (country == 'Indonesia') {
            $taxDiv.show();
        }
        $(document).on('change', '#utilbundle_patient_nationality', function () {
            country = $(this).find("option:selected").text();
            if (country == 'Indonesia') {
                PatientCrud.taxInput.val(PatientCrud.taxInput.attr('originval'));
                $taxDiv.show();
            } else {
                PatientCrud.taxInput.val('');
                $taxDiv.hide();
            }
        });
    },
    getAge: function(dateString) {
        var today = new Date();
        var birthDate = new Date(dateString);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    },
    checkValidDate: function () {
        var day = PatientCrud.birthDay.val();
        var month = PatientCrud.birthMonth.val();
        var year = PatientCrud.birthYear.val();

        if (day != '' && month != '' && year != '') {
            if (day < 10) {
                day = '0' + day
            }

            if (month < 10) {
                month = '0' + month
            }
            var selectedDate = year + "-" + month + "-" + day;
            var date = moment(selectedDate).format('YYYY-MM-DD');
            if (date == selectedDate) {
                return true
            }
        }

        return false;
    },
    checkValidAge: function() {
        var day = PatientCrud.birthDay.val();
        var month = PatientCrud.birthMonth.val();
        var year = PatientCrud.birthYear.val();
        var selectedDate = year + "-" + month + "-" + day;
        var age = PatientCrud.getAge(selectedDate);
        if (age < 18) {
            return false;
        }

        return true;
    },
    bootstrap: function() {
        if (false == $('#hasCaregiver').find('input:checked').val()) {
            $('#caregiverContainer').hide();
        }

        $(".select2, .select2-multiple").select2({
            width: null
        });

        $('#utilbundle_patient_diagnosis').multiSelect({
            afterSelect: function(values){
                var selected = $('#utilbundle_patient_diagnosis').find('option[value=' + values + ']').text();
                if (selected.toLowerCase() == 'others') {
                    $('.other-diagnosis').removeClass("hidden");
                    $('#other_values').rules('add', {
                        required: true,
                        messages: {
                            required: "This field is required"
                        }
                    });
                }
            },
            afterDeselect: function(values){
                var selected = $('#utilbundle_patient_diagnosis').find('option[value=' + values + ']').text();
                if (selected.toLowerCase() == 'others') {
                    $('.other-diagnosis').addClass("hidden");
                    $('#other_values').rules('remove');
                }
            }
        });
    },
    onSubmitForm: function() {
        $('#patientForm').validate({
            rules: {
                'utilbundle_patient[personalInformation][emailAddress]': {
                    required: true,
                    email: true,
                    customValidateEmail: true
                },
                'utilbundle_patient[phones][0][areaCode]': {
                    digits: true
                },
                'utilbundle_patient[phones][0][number]': {
                    required: true,
                    digits: true
                },
                'utilbundle_patient[caregivers][0][personalInformation][emailAddress]': {
                    required: true,
                    email: true
                },
                'utilbundle_patient[caregivers][0][phones][0][areaCode]': {
                    digits: true
                },
                'utilbundle_patient[caregivers][0][phones][0][number]': {
                    required: true,
                    digits: true
                },
                'utilbundle_patient[taxId]': {
                    isValidNPWP: true,
                },
                'utilbundle_patient[personalInformation][dateOfBirth][day]': {
                    isValidDate: true
                },
                'utilbundle_patient[personalInformation][dateOfBirth][month]': {
                    isValidDate: true
                },
                'utilbundle_patient[personalInformation][dateOfBirth][year]': {
                    isValidDate: true
                }
            },
            errorElement: 'span',
            groups: {
                dob: "utilbundle_patient[personalInformation][dateOfBirth][day] utilbundle_patient[personalInformation][dateOfBirth][month] utilbundle_patient[personalInformation][dateOfBirth][year]"
            },
            submitHandler: function(form) {
                if ( PatientCrud.taxInput.is(":visible") && $.trim(PatientCrud.taxInput.val()) == '' && !$('#modal-npwp').hasClass('in')) {
                    $('#modal-npwp').modal();
                } else {
                    $('#saveBtn').addClass('disabled');
                    $('#saveAndCreatRxBtn').addClass('disabled');
                    form.submit();
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr('id') == 'utilbundle_patient_taxId' && $.trim(element.val()) != '') {
                    error.insertAfter(element);
                } else {
                    element.parents('.showHere').first().append(error);
                    return false;
                }
            }
        });

        $('#saveBtn').click(function() {
            $('#patientForm').submit();
        });

        $('#saveAndCreatRxBtn').click(function() {
            $('#andCreateRx').val(1);
            $('#patientForm').submit();
        })

    },
    onClickAddAllergyLink: function() {
        $collectionHolder = $('#utilbundle_patient_allergies').first();
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $('#addLink').click(function() {
            var prototype = $collectionHolder.data('prototype');
            var index = $collectionHolder.data('index');

            var newForm = prototype;
            newForm = newForm.replace(/__name__/g, index);

            $collectionHolder.data('index', index + 1);

            $collectionHolder.append(newForm);
        });

        $('#allergyContainer').on('click', '.removeLink', function() {
            $(this).parents('div').eq(2).remove();
            $collectionHolder.data('index', $collectionHolder.find(':input').length);
        });

        $('#knowMedication').find('input').change(function() {
            if (false == $(this).val()) {
                $('#allergyContainer').hide();
            } else {
                $('#allergyContainer').show();
            }
        });

        $('#hasCaregiver').find('input').change(function() {
            if (false == $(this).val()) {
                $('#caregiverContainer').hide();
            } else {
                $('#caregiverContainer').show();
            }
        });
    }
}

PatientCrud.birthYear.change(function() {
    $(this).valid();
});

$(document).ready(function() {

    jQuery.validator.addMethod("isValidDate", function (value, element) {
        if (!PatientCrud.checkValidDate() || !PatientCrud.checkValidAge()) {
            PatientCrud.birthDay.select2({ containerCssClass : "error" });
            PatientCrud.birthMonth.select2({ containerCssClass : "error" });
            PatientCrud.birthYear.select2({ containerCssClass : "error" });
            $('#dob-error').removeClass("hidden");

            return false;
        } else {
            PatientCrud.birthDay.select2({ containerCssClass : "" });
            PatientCrud.birthMonth.select2({ containerCssClass : "" });
            PatientCrud.birthYear.select2({ containerCssClass : "" });
            $('#dob-error').addClass("hidden");

            return true;
        }
    }, function () {
        if (!PatientCrud.checkValidDate()) {
            return 'Invalid date!';
        }

        if (!PatientCrud.checkValidAge()) {
            return 'All patients registered must be at least 18 years of age.';
        }
    });
    jQuery.validator.addMethod("customValidateEmail", function(value, element) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(value);
    }, function(params, element) {
        return "Please enter a valid email address."
    });
    jQuery.validator.addMethod("isValidNPWP", function (value, element) {
        if ($.trim(value) == '') {
            return true;
        }
        var invalid = false;

        value = $.trim(value);

        //checking number and 15 digits
        if($.isNumeric(value) && value.length == 15) {
            return patientCommonJs.isValidNPWP(value);
        }

        var formula = {2 : '.', 6 : '.', 10: '.', 12: '-', 16: '.'};
        var keys = Object.keys(formula);

        keys.forEach(function(item) {
            if (value.charAt(item) != formula[item]) {
                invalid = true;
                return false;
            }
        });
        if (invalid == true) {
            return false;
        }
        value = value.replace(/[\W\s\._\-]+/g, '');
        return patientCommonJs.isValidNPWP(value);
    }, function(params, element) {
        return 'Please input a valid NPWP.'
    });

    jQuery.validator.addMethod("warningNPWP", function (value, element) {
        if ($.trim(value) == '') {
            $('#modal-npwp').modal();
            return true;
        }
    });

    PatientCrud.init();
});