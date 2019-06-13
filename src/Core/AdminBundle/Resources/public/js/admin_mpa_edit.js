var Form = {
    baseUrl: $("#asset-url").val(),
    useCompany: false,
    data: {},
    validSize: true,

    initStatus: function () {

        $("#btn-save-mpa").on('click', function (e) {
            e.preventDefault();
            $("#admin-add-mpa").submit();
        });
        this.validateInfor();

    },


    validateInfor: function () {
        if($("#current-mpa-id").val() != '') {
            $("#admin_mpa_profile").removeAttr("required");
        }
        var validobj = $("#admin-add-mpa").validate({
            errorClass: "error",
            errorElement: 'span',
            errorPlacement: function (error, element) {
                var $e = element;
                switch (element.attr("name")) {


                    case 'admin_mpa[profile]':
                        var tag = $e.parents(".col-md-12").first().find('.fileinput');
                        tag.append(error);
                        break;
                    case 'admin_mpa[phoneLocation]':
                        var tag = $e.parents(".col-md-12").first().find('#admin_mpa_phoneLocation-error');
                        tag.html(error);
                        break;
                    case 'admin_mpa[phone]':
                        var tag = $e.parents(".col-md-12").first().find('#admin_mpa_phoneLocation-error');
                        tag.html(error);
                        break;



                    default :
                        error.insertAfter(element);
                        break;
                }

            },
            rules: {
                'admin_mpa[clinicName]': {
                    required: true
                },
                'admin_mpa[familyName]': {
                    required: true
                },
                'admin_mpa[email]': {
                    unique: true,
                    required: true
                },
                'admin_mpa[profile]': {
                    validUpload: true,
                },
                'admin_mpa[givenName]': {
                    required: true
                },
                'admin_mpa[phoneLocation]': {
                    required: true,
                },
                'admin_mpa[phone]': {
                    required: true,
                    digits: true
                }
            },
            submitHandler: function (form) {
                $("#btn-save-mpa").remove();
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

        $("#admin_mpa_phoneLocation").select2({placeholder: 'Select Country'});
        $("#admin_mpa_profile").on('change', function() {
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
