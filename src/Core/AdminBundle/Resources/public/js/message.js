function getQueryStringValue (key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

var jsMessage = {
    editor: null,
    init: function () {
        jsCommon.pagingAjax();
        jsMessage.initWysihtml5();
        jsMessage.checkboxPicked();
        jsMessage.search();
        jsMessage.sendAction();
        var slideIndex = 1;
        jsMessage.showSlides(slideIndex);
        
        $('a.view-images').on('click', function(evt) {
			evt.preventDefault();
            $(this).closest('div.image-item').find('img').click();
			$(".gallery-btn").hide();
        });
		
		$('a.view-all-images').on('click', function(evt) {
			evt.preventDefault();
            jsMessage.slide(1, 0);
			$(".gallery-btn").show();
        });

        $('[name="message"]').val($('#reply_email_content_body').html());
      
        //handle compose/reply cc input toggle
        $('.inbox-content').on('click', '.mail-to .inbox-cc', function () {
            jsMessage.handleCCInput();
        });

        //handle compose/reply bcc input toggle
        $('.inbox-content').on('click', '.mail-to .inbox-bcc', function () {
            jsMessage.handleBCCInput();
        });

        $('[name="messages[message]"]').removeAttr('required');
        
        var sources = function (term, sync) {
            $.ajax({
                url: $("#url_message_suggestion").val(),
                data: {term: term},
                dataType: "json",
                async: false,
                type: "POST",
                success: function (res) {
                    return sync(res);
                }
            });
        };
        if($('.on-suggestion').length > 0) {
            $('.on-suggestion').tagsinput({
                typeaheadjs:{
                    highlight: false,
                    hint: true,
                    minLength: 3,
					limit: 13,
                    name: 'emailAddress',
                    displayKey: 'emailAddress',
                    valueKey: 'emailAddress',
                    source: sources
                }
            })
        }
        $('.on-suggestion, .tt-input, #messages_subject').on('keypress', function(e) {
            if (e.keyCode == 13){
              e.preventDefault();
              return false;
            };
        });
    },
    sendAction: function(){
        $('.btn-send').on('click', function(e){
            var errorText = "";
            var formId = $("#frm-messages");
            var to = $.trim(formId.find('#messages_to').val());
			var tos = to.length > 0 ? to.split(',') : [];
			for (var i = 0; i < tos.length; i++) {
				tos[i] = tos[i].trim();
				if (tos[i][tos[i].length - 1] == '>') {
					tos[i] = tos[i].substring(tos[i].indexOf('<') + 1, tos[i].length - 1);
				}
			}
			if (tos.length > 0) {
				formId.find('#messages_to').val(tos.join(','));
			}
            var subject = $.trim(formId.find('#messages_subject').val());
            var message = $.trim(formId.find('#messages_message').val())
            if(to == "") {
                errorText += "<p>To is required.</p>";
            }
            if(subject == "") {
                errorText += "<p>Subject is required.</p>";
            }
            if(message == "") {
                errorText += "<p>Message is required.</p>";
            }
            if(errorText != "") {
                $("#modal-messages").on('shown.bs.modal', function (e) {
                    $(this).find('.item-content').html(errorText);
                }).modal();
                return false;
            }
            formId.find("input[name='messages[postType]']").val('send');
            formId.submit();
        });
        $('.btn-discard').on('click', function(e){
            $("#frm-messages").find("input[name='messages[postType]']").val('trash');
            $("#frm-messages").submit();
        });
        $('.btn-draft').on('click', function(e){
            $("#frm-messages").find("input[name='messages[postType]']").val('draft');
            $("#frm-messages").submit();
        });
        if($('#fileupload').length > 0) {
            //PDF, DOC & DOCX, XLS & XLSX, PNG, GIF, JPG, TIFF
            $('#fileupload').fileupload({
                autoUpload: true,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf|doc|docx|xls|xlsx|tiff)$/i,
                maxFileSize: 10000000,
            });
        }
    },
    handleCCInput: function () {
        var the = $('.inbox-compose .mail-to .inbox-cc');
        var input = $('.inbox-compose .input-cc');
        the.hide();
        input.show();
        $('.close', input).click(function () {
            input.hide();
            the.show();
        });
    },
    handleBCCInput: function () {
        var the = $('.inbox-compose .mail-to .inbox-bcc');
        var input = $('.inbox-compose .input-bcc');
        the.hide();
        input.show();
        $('.close', input).click(function () {
            input.hide();
            the.show();
        });
    },
    initWysihtml5: function () {
        if($('.inbox-wysihtml5').length > 0) {
            jsMessage.editor = $('.inbox-wysihtml5').wysihtml5({
                "stylesheets": wysiwygColorUrl
            });

            if (typeof getQueryStringValue('type') !== 'undefined' && ['reply', 'replyall', 'forward'].indexOf(getQueryStringValue('type')) > -1) {
                jsMessage.editor.focus();
            }
        }
    },
    checkboxPicked: function() {
        var ff = $("#frm-message-filter");
        var dc = $("div.content-table.inbox-content");
        dc.on('click', '.mail-group-checkbox', function() {
            var checked = $(this).prop('checked');
            dc.find('input.checkboxes').not(":disabled").prop('checked', checked);
            var searchIDs = dc.find('input:checked').map(function(){
                if($(this).val() != 'on')
                    return $(this).val();
            });
            if(searchIDs.get().length > 1) {
                dc.find("input[name=ids_picked]").val(searchIDs.get());
            } else {
                dc.find("input[name=ids_picked]").val("");
            }
        });
        dc.on('click', 'input.checkboxes', function(e) {
            var checked = $(this).prop('checked');
            var searchIDs = dc.find('input:checked').map(function(){
                if($(this).val() != 'on')
                    return $(this).val();
            });
            
            if(searchIDs.get().length > 0)
                dc.find("input[name=ids_picked]").val(searchIDs.get());
            else
                dc.find("input[name=ids_picked]").val("");
            if(dc.find('tbody').children().length == searchIDs.get().length)
                dc.find('.mail-group-checkbox').prop('checked', true);
            else 
                dc.find('.mail-group-checkbox').prop('checked', false);
        });
        ff.on('click', 'a.actions', function(e) {
            e.preventDefault();
            if(dc.find("input[name=ids_picked]").val() != "") {
                var params = {
                    ids: dc.find("input[name=ids_picked]").val(),
                    act: $(this).data('value'),
                    type: ff.find("input[name='filter[type]']").val()
                };
                successCallback = function (res) {
                    //console.log(res);
                    if(res.status == true) {
                        $('.inbox-sidebar').find('#it-inbox').html(res.data.inbox > 0? res.data.inbox: "");
                        $('.inbox-sidebar').find('#it-draft').html(res.data.draft > 0? res.data.draft: "");
                        $('.inbox-sidebar').find('#it-trash').html(res.data.trash > 0? res.data.trash: "");
                        $('.page-sidebar-menu').find('#it-total').html(res.data.total > 0? res.data.total: "");
                        jsMessage.getData();
                    }
                };
                errorCallback = function (xhr, ajaxOptions, thrownError) {};
                jsDataService.callAPI($("#url_message_change").val(), params, "POST", successCallback, errorCallback, null, 'json');
            } else {
                console.log('No items selected');
            }
        });
    },
    search: function(){
        var ff = $("#frm-message-filter");
        var dc = $("div.content-table.inbox-content");
        ff.on('click', '.btn-term-search', function(e){
            e.preventDefault();
            jsMessage.getData();
        })
        .on('keyup', '#filter_term', function(e) {
            if (e.keyCode == 13) {
                jsMessage.getData();
                e.preventDefault();
            }
        }).on('keypress keydown', '#filter_term', function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
            }
        }).on('click', 'a.read-date', function(e) {
            var vl = $(this).data('value');
            ff.find("input[name='filter[read_date]']").val(vl);
            e.preventDefault();
            jsMessage.getData();
        }).on('click', 'a.sort', function(e) {
            var vl = $(this).data('value');
            var od = $(this).data('order');
            ff.find("input[name='sort_name']").val(vl);
            ff.find("input[name='sort_order']").val(od);
            e.preventDefault();
            jsMessage.getData();
        });
    },
    getData: function() {
        successCallback = function (res) {
            $("div.content-table.inbox-content" ).html(res);
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };
        jsDataService.callAPI($("#url_message_list").val(), $('#frm-message-filter').serialize(), "POST", successCallback, errorCallback, null, 'html');
    },
    showSlides: function(n) {
        slideIndex = (typeof slideIndex == 'undefined')? 1: slideIndex;
        var i;
        var slides = $(document).find('.gallerySlides');
        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
        for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
        }
        $(document).find('.gallerySlides:nth-child('+ slideIndex +')').css('display',"block");
    },
    slide: function(n, t) {
        $('#galleryModal').modal('show');
        $('body').addClass('modal-hidden');
        slideIndex = (t == 0)? n: slideIndex + n;
        jsMessage.showSlides(slideIndex);
    },
    closeModal: function() {
        $('#galleryModal').modal('hide');
        $('body').removeClass('modal-hidden');
    }
};
$(document).ready(function () {
    jsMessage.init();
});
