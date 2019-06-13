var jsProfile = {

    piForm: '#personal-information-form',
    pwForm: '#change-password-form',
    stepModalGoogleAuth: 0,
    profileData : '',
    profileAjaxUrl : $('#ajax_profile_change').val(),
    passwordData : '',
    passwordAjaxUrl : $('#ajax_password_change').val(),

    init: function() {

        jsProfile.profileAjaxUrl = $('#ajax_profile_change').val();
        jsProfile.submitFormPI();
        jsProfile.validatePassword();
        jsProfile.submitFormPassword();
        jsProfile.validateRequiredOnChange();
        jsProfile.initGoogleAuth();

    },

    initGoogleAuth: function() {
        $('#google-auth-register').click(function () {
            var generateGoogleAuthUrl = $('#generate_google_auth_url').val();
            $.ajax({
                type: "POST",
                url: generateGoogleAuthUrl,
                success: function(res){
                    if (res.status) {
                        $('<img id="google-auth-qr-code" src="'+ res.data.qrCodeUrl +'">').load(function() {
                            $('#google-auth-qr').html(this);
                            $('#google_auth_secret').val(res.data.secret);
                            $('#btnSaveGoogleAuth').click(function () {
                                if (jsProfile.stepModalGoogleAuth === 0) {
                                    $('#google-auth-qr-step1').hide();
                                    $('#google-auth-qr-step2').show();
                                    $(this).text('Save');
                                    jsProfile.stepModalGoogleAuth = 1;
                                } else {
                                    if ($('#google-auth-code').val().length > 0) {
                                        var saveGoogleAuthUrl = $('#save_google_auth_url').val();
                                        $.ajax({
                                            type: "POST",
                                            url: saveGoogleAuthUrl,
                                            data: { google_auth_secret: $('#google_auth_secret').val(), google_auth_code: $('#google-auth-code').val() },
                                            success: function(res){
                                                if (res.status) {
                                                    $('#modal-google-auth-qr').modal('hide');
                                                    $('#google-auth-alert-title').text('Success');
                                                    $('#google-auth-alert').text('Google Authentication saved successfully.');
                                                    $('#google-auth-delete').removeClass('hidden');
                                                    $('#google-auth-register').addClass('hidden');
                                                    $('#modal-google-auth-alert').modal('show');
                                                    jsProfile.resetModalGoogleAuthQr();
                                                } else {
                                                    if (typeof res.message != 'undefined') {
                                                        $('#google-auth-qr-error').text(res.message).show();
                                                    } else {
                                                        $('#google-auth-alert-title').text('Error');
                                                        $('#google-auth-alert').text('Oops, something error happened.');
                                                        $('#modal-google-auth-alert').modal('show');
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        $('#google-auth-qr-error').text('This field is required').show();
                                    }
                                }
                            });
                        });
                        $('#modal-google-auth-qr').modal('show');
                    } else {
                        $('#google-auth-alert-title').text('Error');
                        $('#google-auth-alert').text('Oops, something error happened');
                        $('#modal-google-auth-alert').modal('show');
                    }
                }, error: function () {
                    $('#google-auth-alert-title').text('Error');
                    $('#google-auth-alert').text('Oops, something error happened');
                    $('#modal-google-auth-alert').modal('show');
                }
            });
        });
        $('#google-auth-delete').click(function () {
            var delConfirmation = confirm('Are you sure wan\'t to delete this google auth?');
            if (delConfirmation) {
                var deleteGoogleAuthUrl = $('#remove_google_auth_url').val();
                $.ajax({
                    type: "POST",
                    url: deleteGoogleAuthUrl,
                    data: { google_auth_secret: $('#google_auth_secret').val() },
                    success: function(res){
                        if (res.success) {
                            $('#google-auth-alert-title').text('Success');
                            $('#google-auth-alert').text('Google Authentication successfully deleted.');
                            $('#google-auth-delete').addClass('hidden');
                            $('#google-auth-register').removeClass('hidden');
                        } else {
                            $('#google-auth-alert-title').text('Error');
                            $('#google-auth-alert').text('Oops, something error happened.');
                        }
                        $('#modal-google-auth-alert').modal('show');
                    }
                });
            }
        });
        $('#btnCancelGoogleAuth').click(function () {
            jsProfile.resetModalGoogleAuthQr();
        });
    },

    resetModalGoogleAuthQr: function () {
        jsProfile.stepModalGoogleAuth = 0;
        $('#google-auth-qr-step1').show();
        $('#google-auth-qr-step2').hide();
        $('#btnSaveGoogleAuth').text('Next').off('click');
        $('#google-auth-code').val('');
        $('#google-auth-qr-error').hide();
    },

    submitFormPassword: function(){
        $('#btn-submit-pwd').on('click',function(e){

            $(jsProfile.pwForm).find('input[type="password"]').each(function(e){
                if($(this).val() == ''){
                    jsProfile.showError($(this), 'Please input this field.');
                }
            });

            if($(jsProfile.pwForm).find('span.error:not(".hidden")').length > 0){
                e.preventDefault();
            }else{
                jsProfile.passwordData = $(jsProfile.pwForm).serializeArray();
                $.ajax({
                    type: "POST",
                    url: jsProfile.passwordAjaxUrl,
                    data: jsProfile.passwordData,
                    success: function(data){
                        $(jsProfile.pwForm)[0].reset();
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

    submitFormPI: function(){
        $('#btn-submit-pi').on('click',function(e){
            jsProfile.validateEmail();
            jsProfile.validateRequired();
            jsProfile.profileData = $(jsProfile.piForm).serializeArray();
            
            if($(jsProfile.piForm).find('span.error:not(".hidden")').length > 0){
                e.preventDefault();
            }else{

                var imgname = $('#ProfileBundle_admin_image').val();
                var profile = '';

                var fd = new FormData();

                if(imgname)
                {
                    var ext = imgname.substr((imgname.lastIndexOf('.') + 1));
                    var size = $('#ProfileBundle_admin_image')[0].files[0].size;
                    if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG')
                    {
                        if (size <= 1000000)
                        {
                            profile = $('#ProfileBundle_admin_image')[0].files[0];
                        }
                    }
                }

                $.each(jsProfile.profileData,function(key,input){
                    fd.append(input.name,input.value);
                });
                
                fd.append('image', profile ); 

                $.ajax({
                    type: "POST",
                    url: jsProfile.profileAjaxUrl,
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(data){
                        if(data.success){
                            for (var i = 0; i < jsProfile.profileData.length; i++) {
                                if (jsProfile.profileData[i].name === 'ProfileBundle_admin[email]') {
                                    console.log(jsProfile.profileData[i]);
                                    if (jsProfile.profileData[i].value !== $('#old_email').val()) {
                                        $('#old_email').val(jsProfile.profileData[i].value);
                                        $('#google-auth-delete').addClass('hidden');
                                        $('#google-auth-register').removeClass('hidden');
                                        jsProfile.resetModalGoogleAuthQr();
                                    }
                                    return false;
                                }
                            }
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

        $('#btn-submit-pi-cc').on('click',function(e){
            $(document).find('span.error').remove();
            jsProfile.validateEmail();
            jsProfile.validateRequired();
            jsProfile.profileData = $(jsProfile.piForm).serializeArray();

            if($(jsProfile.piForm).find('span.error:not(".hidden")').length > 0){
                $("html, body").animate({ scrollTop: 0 }, 600);
                $('input.error').first().focus();
                e.preventDefault();
            }else{
                var imgname = $('#ProfileBundle_cc_image').val();
                var imgsignature = $('#ProfileBundle_cc_signature').val();
                var image = '';
                var signature = '';
                var fd = new FormData();
                if(imgname) {
                    var ext = imgname.substr((imgname.lastIndexOf('.') + 1));
                    var size = $('#ProfileBundle_cc_image')[0].files[0].size;
                    if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG')
                    {
                        if (size <= 1000000) {
                            image = $('#ProfileBundle_cc_image')[0].files[0];
                        }
                    }
                }

                if(image == "" && $("#profile-image").val() == "") {
                    $("#item-profile").find('span.error').remove();
                    $("#item-profile").append('<span class="error">This field is required.</span>');
                    return false;
                }

                if(imgsignature) {
                    var ext = imgsignature.substr((imgsignature.lastIndexOf('.') + 1));
                    var size = $('#ProfileBundle_cc_signature')[0].files[0].size;
                    if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG')
                    {
                        if (size <= 1000000) {
                            signature = $('#ProfileBundle_cc_signature')[0].files[0];
                        }
                    }
                } else if (SignModal.signature != null) {
                    if (SignModal.signature.size <= 1000000) {
                        signature = SignModal.signature;
                    }
                }

                $.each(jsProfile.profileData,function(key,input){
                    fd.append(input.name,input.value);
                });

                fd.append('image', image );
                fd.append('signature', signature );

                $.ajax({
                    type: "POST",
                    url: jsProfile.profileAjaxUrl,
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(data){
                        if(data.success){
                            for (var i = 0; i < jsProfile.profileData.length; i++) {
                                if (jsProfile.profileData[i].name === 'ProfileBundle_admin[email]') {
                                    console.log(jsProfile.profileData[i]);
                                    if (jsProfile.profileData[i].value !== $('#old_email').val()) {
                                        $('#old_email').val(jsProfile.profileData[i].value);
                                        $('#google-auth-delete').addClass('hidden');
                                        $('#google-auth-register').removeClass('hidden');
                                        jsProfile.resetModalGoogleAuthQr();
                                    }
                                    return false;
                                }
                            }
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
                    }
                });

            }
        });
    },

    validateRequiredOnChange: function(){
        $(jsProfile.piForm).find('input:not("#ProfileBundle_admin_email"), select').each(function(e){
            $(this).on('change', function(){
                if($(this).is('input') && typeof $(this).attr("required") !== typeof undefined && $(this).attr("required") !== false  && $(this).val() == '' ){
                    if($(this).data('required') == '' || $(this).data('required') == undefined){
                        jsProfile.showError($(this), 'This field is required.');
                    }
                }else{
                    jsProfile.removeError($(this));
                }
            });
        });

        $('#ProfileBundle_admin_email').on('keydown', function(e){
            jsProfile.validateEmail();
        });
    },

    validateRequired: function(){
        $(jsProfile.piForm).find('input, select').each(function(e){
            if($(this).is('input') && typeof $(this).attr("required") !== typeof undefined && $(this).attr("required") !== false  && $(this).val() == '' ){
                if($(this).data('required') == '' || $(this).data('required') == undefined){
                    jsProfile.showError($(this), 'This field is required.');
                }
            }
        });
    },

    validateEmail: function(){
        var filter = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
        var email = $('#ProfileBundle_admin_email').val();
        if ($.trim(email).length == 0) {
            jsProfile.showError($('#ProfileBundle_admin_email'),'Please enter valid email address.');
        } else if (!filter.test(email)) {
            jsProfile.showError($('#ProfileBundle_admin_email'),'Invalid email address.');
        } else {
            jsProfile.removeError($('#ProfileBundle_admin_email'));
        }
    },

    showError: function(el, msg){
        var div = el.closest('div[class*=col-md-]').find('div.error');
        el.addClass('error');
        div.html('<span class="error">'+msg+'</span>');
    },

    removeError: function(el){
        var div = el.closest('div[class*=col-md-]').find('div.error');
        el.removeClass('error');
        div.find('span.error').addClass('hidden');
    },

    validatePassword: function(){

        $('#ChangePasswordBundle_agent_current_password, #ChangePasswordBundle_agent_new_password, #ChangePasswordBundle_agent_confirm_password').on('keyup', function(){
            jsProfile.removeError($(this));
        });

        $('#ChangePasswordBundle_agent_new_password').on('keyup', function(){
            //Minimum eight characters, at least one uppercase letter, one lowercase letter and one number
            var regex = /^(?=.*\d)(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            if(!regex.test($(this).val()) && $(this).val() != ''){   
                jsProfile.showError($(this),'Minimum eight characters, at least one uppercase letter, one lowercase letter and one number.');
            }else{
                jsProfile.removeError($(this));
            }

            if($(this).val() != $('#ChangePasswordBundle_agent_confirm_password').val() && $('#ChangePasswordBundle_agent_confirm_password').val() != ''){
                jsProfile.showError($('#ChangePasswordBundle_agent_confirm_password'),'Password does not match.');
            }
        });

        $('#ChangePasswordBundle_agent_confirm_password').on('keyup', function(){
            if($(this).val() != $('#ChangePasswordBundle_agent_new_password').val() && $(this).val() != ''){   
                jsProfile.showError($(this),'Password does not match.');
            }else{
                jsProfile.removeError($(this));
            }
        });
    }
};
var SignModal = {
    isFirst: true,
    signature: null,
    init: function() {
        SignModal.show();
        // SignModal.sign();
        SignModal.signCreate();
    },
    show: function() {
        $('#create-signature').click(function(e){
            $('#btn-remove-sign').click();
            SignModal.signature = null;
            SignModal.bootstrap();
        });
    },
    bootstrap: function() {
        $('#modal-create-sign-fda').modal('show');
        $('#modal-create-sign-fda').on('shown.bs.modal', function() {
            SignModal.signClear();
            SignModal.warning();
            SignModal.watermark();
        });

        $('#modal-create-sign-fda').on('hidden.bs.modal', function() {
            SignModal.reset();
        });

        $('#cancelBtn').click(function(){
            $('#modal-error').modal('hide');
        });
    },
    sign: function() {
        var canvas     = document.getElementById('paint'),
            ctx        = canvas.getContext('2d'),
            sign       = document.getElementById('area-sign'),
            signStyle  = getComputedStyle(sign),
            createSign = document.getElementById('createSign');

        canvas.width     = parseInt(signStyle.getPropertyValue('width'));
        canvas.height    = parseInt(signStyle.getPropertyValue('height'));

        ctx.lineWidth   = 5;
        ctx.lineJoin    = 'round';
        ctx.lineCap     = 'round';
        ctx.strokeStyle = 'black';

        ctx.fillStyle = "rgba(0, 0, 0, 0)";
        ctx.fillRect(30, 30, 55, 50);

        // Get a regular interval for drawing to the screen
        window.requestAnimFrame = (function (callback) {
            return window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.oRequestAnimationFrame ||
                window.msRequestAnimaitonFrame ||
                function (callback) {
                    window.setTimeout(callback, 1000/60);
                };
        })();

        // Set up mouse events for drawing
        var drawing = false;
        var mousePos = { x:0, y:0 };
        var lastPos = mousePos;

        canvas.addEventListener("mousedown", function (e) {
            drawing = true;
            lastPos = getMousePos(canvas, e);
        }, false);
        canvas.addEventListener("mouseup", function (e) {
            drawing = false;
        }, false);
        canvas.addEventListener("mousemove", function (e) {
            mousePos = getMousePos(canvas, e);
        }, false);

        // Set up touch events for mobile, etc
        canvas.addEventListener("touchstart", function (e) {
            e.preventDefault();
            mousePos = getTouchPos(canvas, e);
            var touch = e.touches[0];
            var mouseEvent = new MouseEvent("mousedown", {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }, false);
        canvas.addEventListener("touchend", function (e) {
            e.preventDefault();
            var mouseEvent = new MouseEvent("mouseup", {});
            canvas.dispatchEvent(mouseEvent);
        }, false);
        canvas.addEventListener("touchmove", function (e) {
            e.preventDefault();
            var touch = e.touches[0];
            var mouseEvent = new MouseEvent("mousemove", {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }, false);

        // Get the position of the mouse relative to the canvas
        function getMousePos(canvasDom, mouseEvent) {
            var rect = canvasDom.getBoundingClientRect();
            return {
                x: mouseEvent.clientX - rect.left,
                y: mouseEvent.clientY - rect.top
            };
        }

        // Get the position of a touch relative to the canvas
        function getTouchPos(canvasDom, touchEvent) {
            var rect = canvasDom.getBoundingClientRect();
            return {
                x: touchEvent.touches[0].clientX - rect.left,
                y: touchEvent.touches[0].clientY - rect.top
            };
        }

        // Draw to the canvas
        function renderCanvas() {
            if (drawing) {
                if (SignModal.isFirst) {
                    SignModal.watermarkReset();
                }

                ctx.beginPath();
                ctx.moveTo(lastPos.x, lastPos.y);
                ctx.lineTo(mousePos.x, mousePos.y);
                ctx.stroke();
                lastPos = mousePos;
            }
        }

        // Allow for animation
        (function drawLoop () {
            requestAnimFrame(drawLoop);
            renderCanvas();
        })();
    },
    signClear: function() {
        $("#clearBtn").click(function() {
            SignModal.reset();
            SignModal.watermark();
        });
    },
    reset: function() {
        var canvas     = document.getElementById('paint'),
            ctx        = canvas.getContext('2d'),
            createSign = document.getElementById('createSign');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        createSign.classList.add('disabled');
    },
    signCreate: function() {
        $("#createSign").click(function(e) {
            $(this).addClass('disabled');
            var canvas  = document.getElementById('paint'),
                dataUrl = canvas.toDataURL('image/png');

            if(!dataUrl) {
                $(this).removeClass('disabled');
                return false;
            }
            var block = dataUrl.split(";");
            // Get the content type
            var contentType = block[0].split(":")[1];
            // get the real base64 content of the file
            var realData = block[1].split(",")[1];

            // Convert to blob
            var blob = b64toBlob(realData, contentType);

            SignModal.signature = blob;

            if ($('#doctor-signature').find('.fileinput-preview img').length > 0) {
                $('#doctor-signature').find('.fileinput-preview img').attr('src', dataUrl);
            } else {
                var img = "<img src='" + dataUrl + "' style='max-height: 140px;'>";
                $('#doctor-signature').removeClass('fileinput-new');
                $('#doctor-signature').addClass('fileinput-exists');
                $('#doctor-signature').find('.fileinput-preview').html(img);
            }

            if ($("#current-doctor-sign").val().length == 0) {
                $("#admin_doctor_signature").removeAttr('required');
            }

            $('#modal-create-sign-fda').modal('hide');
        });
    },
    warning: function() {
        $('.cancel-sign').click(function() {
            $('#modal-warning').modal('show');
            SignModal.confirmCancel();
            SignModal.confirmSign();
        });
    },
    confirmCancel: function() {
        $('#confirmCancel').click(function(){
            $('#warning-cancel-msg').removeClass('hide');
        });
    },
    confirmSign: function() {
        $('#confirmSign').click(function(){
            $('#modal-warning').modal('hide');
            SignModal.bootstrap();
        });
    },
    canvasSize: function() {
        var canvas     = document.getElementById('paint'),
            canvasImg  = canvas.toDataURL("image/png"),
            head       = 'data:image/png;base64,',
            canvasSize = Math.round( ((canvasImg.length - head.length)*3/4)/1024 );
        return canvasSize;
    },
    modalMsg: function(title, message) {
        var eleModalError = $('#modal-error');
        eleModalError.find('.modal-title').html(title);
        eleModalError.find('.msg-error').html(message);
        eleModalError.modal('show');
        return true;
    },
    watermark: function() {
        SignModal.isFirst = true;
        var canvas     = document.getElementById('paint'),
            ctx        = canvas.getContext('2d'),
            sign       = document.getElementById('area-sign'),
            signStyle  = getComputedStyle(sign),
            createSign = document.getElementById('createSign');

        canvas.width     = parseInt(signStyle.getPropertyValue('width'));
        canvas.height    = parseInt(signStyle.getPropertyValue('height'));

        ctx.font = "30px Arial";
        ctx.textAlign = "center";
        ctx.fillStyle = "#787878";
        ctx.fillText("Sign Here", canvas.width/2, 50);

        ctx.beginPath();
        ctx.lineWidth = 5;
        ctx.lineCap="round";
        ctx.strokeStyle = "#787878";
        ctx.moveTo(50,100);
        ctx.lineTo(canvas.width - 50, canvas.height - 100);
        ctx.stroke();

        ctx.beginPath();
        ctx.lineWidth = 5;
        ctx.lineCap="round";
        ctx.strokeStyle = "#787878";
        ctx.moveTo(50,canvas.height - 100);
        ctx.lineTo(canvas.width - 50, 100);
        ctx.stroke();
    },
    watermarkReset: function() {
        SignModal.isFirst = false;
        var canvas     = document.getElementById('paint'),
            ctx        = canvas.getContext('2d'),
            createSign = document.getElementById('createSign');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = 'black';
        createSign.classList.remove('disabled');
    }
};

$(document).ready(function() {
    jsProfile.init();
    SignModal.init();
});
function b64toBlob(b64Data, contentType, sliceSize) {
    contentType = contentType || '';
    sliceSize = sliceSize || 512;

    var byteCharacters = atob(b64Data);
    var byteArrays = [];

    for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
        var slice = byteCharacters.slice(offset, offset + sliceSize);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    var blob = new Blob(byteArrays, {type: contentType});
    return blob;
}