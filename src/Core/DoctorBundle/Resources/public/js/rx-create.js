function numberWithCommas(x){
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


var RXCreate = {
    haveStockdrug : false,
    reviewUrl: '',
    autosaveTimeout: null,
    savedAsDraftShowTimer: null,
    init: function() {
        RXCreate.onClickAddSelectedMedications();
        RXCreate.onClickAddAllMedication();
        RXCreate.onSearchDrug();
        RXCreate.onClickContinue();
        RXCreate.onClickAddMedication();
        RXCreate.onClickSaveAsDraft();
        RXCreate.onClickDateTabs();
        RXCreate.onClickButtonBack();
        RXCreate.onClickTop30Tab();
        RXCreate.onClickMyFavoriteTab();
        RXCreate.onClickReview();
        RXCreate.onChangeRXReviewFee();
        RXCreate.onClickDeleteButton();
        RXCreate.onClickPrintPdfButton();
        RXCreate.onClickDeleteDrugButton();
        RXCreate.bootstrap();
        RXCreate.messageOutOfStockDrug();
        RXCreate.onClickSendToAssistant();
    },
    bootstrap: function() {
        var yourBody = $("body");
        var stickyDiv = "page-patient-info-fixed";
        var stickyContent = $('.patient-top-content');
        var headerDiv = $('.page-header');
        var sidebarDiv = $('.page-sidebar');
        var yourHeader = headerDiv.outerHeight();
        var yourPageHead = $('.page-head').outerHeight();
        var yourBreadcrumb = $('.page-breadcrumb').outerHeight();
        var topSticky = yourHeader + yourPageHead + yourBreadcrumb + 50;

        var today = new Date();
        $('#scheduled_send_date').datepicker({
            format: 'yyyy-mm-dd',
            startDate: '+1d',
            forceParse: false,
            autoclose: true
        });

        $('#scheduleRxBtn').click(function () {
            $('#modalScheduleRxOrder').modal('show');
        });

        $('#calendar-icon').click(function(event) {
            $('#scheduled_send_date').datepicker('show');
        });

        $(window).scroll(function() {
            if( $(this).scrollTop() > topSticky ) {
                stickyContent.css('height', stickyContent.outerHeight());
                yourBody.addClass(stickyDiv);
                headerDiv.css('position', 'absolute');
                sidebarDiv.css('top', '10px');
            } else {
                stickyContent.css('height', 'auto');
                yourBody.removeClass(stickyDiv);
                headerDiv.css('position', 'fixed');
                sidebarDiv.css('top', '95px');
            }
        });

        // Checkbox all
        $('#stepOne').on('change', '.checkboxAll', function() {
            var table = $(this).parents('table').first();
            if ($(this).is(':checked')) {
                table.find('input[type="checkbox"]').prop('checked', true);
            } else {
                table.find('input[type="checkbox"]').prop('checked', false);
            }
        });

        // Medicine counter
        RXCreate._medicineCounter();

        if ($('.requestAmend').length || ($('#isScheduledRx').val() == 1 && $('#isDraftRX').val() != 1)) {
            $('#step2Link').trigger('click');
            RXCreate.triggerFlag = true;
            if ($('#isScheduledRx').val() == 1) {
                RXCreate.isScheduledRx = true;
            }
            return false;
        }

        if (0 == $('.dateTab').length) {
            return false;
        }

        // Trigger firt tab
        $('.dateTab').first().trigger('click');
        $('#accordion3').find('a').first().trigger('click');

        // View step 2 for rx status 5
        if ($('#popup_notification').length) {
            $('#step2Link').trigger('click');
            RXCreate.collapseFlag = true;
        }
    },
    onClickSendToAssistant: function() {
        $('#sendToAssistant').click(function() {
            var url = $('#requestAssistantForm').prop('action');
            var data = $('#requestAssistantForm').serialize();
            $.ajax({
                url: url,
                data: data,
                method: 'POST',
                beforeSend: function() {
                    $('#sendToAssistant').addClass('disabled');
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-request-assistant-success').modal('show');
                    } else {
                        $('#modal-request-assistant-failed').modal('show');
                    }
                }
            });
        });

        $('#modal-request-assistant-success').on('hide.bs.modal', function (e) {
            window.location.href = $('#draft-rx-url').val();
        });

        $('#modal-request-assistant-failed').on('hide.bs.modal', function (e) {
            window.location.href = $('#draft-rx-url').val();
        });
    },
    onChangeRXReviewFee: function() {
        $('#chargeRXReviewFee').on('ifChanged', function() {
            if ($('#chargeRXReviewFee').is(':checked')) {
                $('#reviewFeeContainer').removeClass('hide');
            } else {
                $('#reviewFeeContainer').addClass('hide');
            }
        });
    },
    onClickDateTabs: function() {
        $('.dateTab').on('click', function() {
            var self = this;

            if ($('#dateTabContent-' + $(self).data('id')).length) {
                return true;
            }

            // Get content
            $.ajax({
                url: $(self).data('url'),
                data: {'patientId': $('#patientId').val()},
                success: function(response) {
                    if (false == response) {
                        return false;
                    }

                    $('#tabContainer').find('.active').removeClass('active');

                    var html = $(response).prop('id', 'dateTabContent-' + $(self).data('id'));
                    $('#tabContainer').append(html);

                    RXCreate._checkCheckedItems($('#tabContainer').find('.active'));
                    RXCreate.initShortLifeEvent();
                }
            });
        });

        $('#tabContainer').on('click', '.sorting', function() {
            var self = this;
            var sorting = RXCreate._getSorting($(self));

            var url = $('#dateTabs').find('.active a').data('url');
            $.ajax({
                url: url,
                data: {'patientId': $('#patientId').val(), 'sorting': sorting},
                success: function(response) {
                    $('#tabContainer').find('.active').html(response);
                    RXCreate._checkSortOrder(sorting);
                    RXCreate._checkCheckedItems( $('#tabContainer').find('.active'));
                    RXCreate.initShortLifeEvent();
                }
            });
        });
    },
    onClickAddSelectedMedications: function() {
        $('#pageContent').on('click', '.addSelectedMedication', function() {
            var activeTab = $('#tabContainer');
            RXCreate._addSelectedMedications(activeTab, true, true);
        });
    },
    onClickAddAllMedication: function() {
        $('#pageContent').on('click', '.addAllMedication', function() {
            var activeTab = $(this).parents('.active').first();
            activeTab.find('.drugCheckbox').each(function() {
                $(this).prop('checked', true);
            });

            RXCreate._addSelectedMedications(activeTab, true, true);
        });
    },
    onClickAddMedication: function() {
        $('#searchTabContent').on('click', '.addMedication', function() {
            var list = $('#drugIds').val();
            var listArr = list.split(',');

            if ($.inArray($(this).data('drugid').toString(), listArr) >= 0) {
                return true;
            }

            listArr.push($(this).data('drugid'));

            $('#drugIds').val(listArr.join(','));

            $(this).addClass('disabled');
            $(this).after($('#addedBtnTemplate').html());

            RXCreate._medicineCounter();

            // reload step 2
            $('#isBack').val('');
        });
    },
    onSearchDrug: function() {
        $('#drugSearchInput').keyup(function(event) {
            if ($(this).val().length < 3) {
                return false;
            }

            var self = this;
            $.ajax({
                url: $(self).data('url'),
                data: {'query': $(self).val(), 'patientId': $('#patientId').val()},
                success: function(response) {
                    $('#searchTabContent').html(response);
                    RXCreate._checkCheckedItems($('#searchTabContent'), true);
                    RXCreate.initShortLifeEvent();
                }
            });
        });

        $('#searchTabContent').on('click', '.sorting', function() {
            var self = this;
            var sorting = RXCreate._getSorting($(self));

            $.ajax({
                url: $('#drugSearchInput').data('url'),
                data: {'query': $('#drugSearchInput').val(), 'patientId': $('#patientId').val(), 'sorting': sorting},
                success: function(response) {
                    $('#searchTabContent').html(response);
                    RXCreate._checkSortOrder(sorting);
                    RXCreate._checkCheckedItems($('#searchTabContent'), true);
                    RXCreate.initShortLifeEvent();
                }
            });
        });

        $('#searchTabContent').on('click', '.noResultYes', function() {
            var drugName = $('#drugSearchInput').val();
            $('#reportForm').find('input[name="drugName"]').val(drugName);
            $('#reportForm').submit();
        });

        $('#searchTabContent').on('click', '.noResultNo', function() {
            $(this).parent().remove();
        });
    },
    messageOutOfStockDrug: function() {
        $('#searchTabContent').on('click', '.outOfStockMessage', function() {
            var drugId = $(this).data('drugid');
            $('#reportPharmacyForm').find('input[name="drugId"]').val(drugId);
            $('#modalReportToPharmacy').modal('show');
        });

        $('#composeMessageToPharmacy').on('click', function() {
            $('#reportPharmacyForm').submit();
            $('#modalReportToPharmacy').modal('hide');
        });
    },
    onClickMyFavoriteTab: function() {
        $('#favoriteTab').click(function() {
            var self = this;
            $.ajax({
                url: $(self).data('url'),
                data: {'patientId': $('#patientId').val()},
                success: function(response) {
                    $('#myFavoritesTabContent').html(response);
                    RXCreate._checkCheckedItems($('#myFavoritesTabContent'));
                    RXCreate.initShortLifeEvent();
                }
            });
        });

        $('#stepOne').on('click', '.favoriteLink', function() {
            var self = this;
            $.ajax({
                url: $(self).data('url'),
                data: {},
                success: function(response) {
                    if (!response.succcess) {
                        $('#limit-error-msg').html(response.errMsg);
                        $('#modalDrugsLimit').modal('show');
                        return false;
                    }
                    if ($(self).data('type')) {
                        $(self).addClass('hide');
                        if ('remove' == $(self).data('type')) {
                            $(self).parent().find('a').last().removeClass('hide');
                        } else {
                            $(self).parent().find('a').first().removeClass('hide');
                        }
                        return false;
                    }

                    if (response.isRemove) {
                        $(self).parents('tr').first().remove();
                    }
                }
            });
        });

        $('#myFavoritesTabContent').on('click', '.addInFavoriteTab', function() {
            var activeTab = $('#myFavoritesTabContent');
            RXCreate._addSelectedMedications(activeTab, true);
        });

        $('#myFavoritesTabContent').on('click', '.sorting', function() {
            var self = this;
            var sorting = RXCreate._getSorting($(self));

            $.ajax({
                url: $('#favoriteTab').data('url'),
                data: {'patientId': $('#patientId').val(), 'sorting': sorting},
                success: function(response) {
                    $('#myFavoritesTabContent').html(response);
                    RXCreate._checkSortOrder(sorting);
                    RXCreate._checkCheckedItems($('#myFavoritesTabContent'));
                    RXCreate.initShortLifeEvent();
                }
            });
        });
    },
    onClickTop30Tab: function() {
        $('#top30Tab').click(function() {
            if ($('#top30TabContent').find('table').length) {
                RXCreate._checkCheckedItems($('#top30TabContent'));
                return true;
            }

            var self = this;
            $.ajax({
                url: $(self).data('url'),
                data: {'patientId': $('#patientId').val()},
                success: function(response) {
                    $('#top30TabContent').html(response);
                    RXCreate._checkCheckedItems($('#top30TabContent'));
                    RXCreate.initShortLifeEvent();

                }
            });
        });

        $('#top30TabContent').on('click', '.addInSearchTab', function() {
            var activeTab = $('#top30TabContent');
            RXCreate._addSelectedMedications(activeTab, true);
        });

        $('#top30TabContent').on('click', '.sorting', function() {
            var self = this;
            var sorting = RXCreate._getSorting($(self));

            $.ajax({
                url: $('#top30Tab').data('url'),
                data: {'patientId': $('#patientId').val(), 'sorting': sorting},
                success: function(response) {
                    $('#top30TabContent').html(response);
                    RXCreate._checkSortOrder(sorting);
                    RXCreate._checkCheckedItems($('#top30TabContent'));
                    RXCreate.initShortLifeEvent();
                }
            });
        });

    },
    onClickReview: function() {
        $('#reviewBtn').click(function(event) {
            event.preventDefault();

            if (!RXCreate._validateRxForm()) {
                return false;
            }
            RXCreate.reviewUrl = $(this).data('url');
            RXCreate.validateStockDrug();
        });
    },

    onClickRemoveDrug: function() {
        $('.removeDrugBtn').click(function(event) {
            event.preventDefault();

            $('#medicineName').text($(this).data('drugname'));
            $('#hiddenDrug').val($(this).data('drugid'));

            $('#modalRxDelete').modal('show');


        });
    },
    onClickDeleteDrugButton: function() {
        $('#rxConfirmBtn').click(function(event) {
            var drug = $('#hiddenDrug').val();
            var pObj = $('#listOfDrugs').find('tr[data-drugid="'+drug+'"]');
            var aObj = pObj.find('a').first();

            var rxLineId = aObj.data('rxlineid');
            var drugId   = aObj.data('drugid');

            if (rxLineId) {
                var list    = $('#rxDrugIds').val().split(',');
                var index   = $.inArray(rxLineId.toString(), list);
                var howmany = (index >= 0) ? 1 : 0;

                list.splice(index, howmany);
                $('#rxDrugIds').val(list.join(','));

                $('#tabContainer').find('input[data-drugid="'+rxLineId+'"]').prop('checked', false);
            }

            if (drugId) {
                var list    = $('#drugIds').val().split(',');
                var index   = $.inArray(drugId.toString(), list);
                var howmany = (index >= 0) ? 1 : 0;

                list.splice(index, howmany);
                $('#drugIds').val(list.join(','));

                $('#searchTabContent').find('a[data-drugid="'+drugId+'"]').removeClass('disabled');
                $('#searchTabContent').find('a[data-drugid="'+drugId+'"]').next('.addedBtn').remove();
                $('#drugContainer').find('input[data-drugid="'+drugId+'"]').prop('checked', false);

                RXCreate._medicineCounter();
            }

            pObj.remove();
            $('#modalRxDelete').modal('hide');
        });
    },
    onClickContinue: function() {
        $('#step2Link').click(function(event) {
            event.preventDefault();

            if (false == $('#rxDrugIds').val() &&
                    false == $('#drugIds').val()) {
                $('.mt-step-col').eq(1).removeClass('done');
                return false;
            }

            if ($('#step2Container').is(':visible')) {
                return true;
            }

            RXCreate._handleStepsVisile(2);

            if ($('#isBack').val()) {
                return true;
            }

            var step2Url = $(this).data('url');

            $.ajax({
                url: step2Url,
                method: 'POST',
                data: {'rxDrugIds': $('#rxDrugIds').val(), 'drugIds': $('#drugIds').val(), 'patientId': $('#patientId').val()},
                success: function(response) {
                    $('#listOfDrugs').html(response);

                    $('.mt-step-col').eq(1).addClass('done');

                    $('.otherInput').each(function() {
                        if (!$(this).hasClass('hide')) {
                            $(this).parent().addClass('open');
                        }
                    });

                    // Validate
                    RXCreate.onSubmitForm();

                    // Remove drug
                    RXCreate.onClickRemoveDrug();

                    RXCreate._initIcheck($('#stepTwo'));

                    RXCreate._checkboxPRN();

                    RXCreate._radioReminder();

                    ComponentsSelect2.init();
                },
                complete: function () {

                    $('.quantity_step').each(function (i, obj) {
                        var step = $(obj).data('step');
                        $(".touchspin_7_" + $(obj).data('id')).TouchSpin({
                            initval: 40,
                            max: 9999,
                            step: step
                        }).on('change', function() {
                            if ($(this).val() == 0) {
                                $(this).trigger("touchspin.uponce");
                                $('#removeDrug_' + $(obj).data('id')).click();
                                return false;
                            }
                            var qSpan = $(this).parents('tr').first().find('.quantitySpan');
                            var priceSpan = $(this).parents('tr').first().find('.priceSpan');
                            var priceSpan2 = $(this).parents('tr').first().find('.priceSpan2');
                            if ($(obj).val() == 1) {
                                var value = qSpan.data('origin');
                                qSpan.text($(this).val() * value);
                            } else {
                                qSpan.text($(this).val() * $(obj).val());
                            }
                            priceSpan.text(numberWithCommas(($(this).val() * priceSpan.data('price')).toFixed(2)));
                            priceSpan2.text(numberWithCommas(($(this).val() * priceSpan2.data('price')).toFixed(2)));
                        });
                    });

                    if (RXCreate.triggerFlag) {
                        RXCreate.reviewUrl = $('#reviewBtn').data('url');
                        RXCreate.triggerFlag = false;
                        RXCreate._getReviewContent();
                        $('.mt-step-col').eq(2).addClass('done');
                        if (RXCreate.isScheduledRx) {
                            $('#modalScheduleRxOrder').modal('show');
                            $('#buttonBack').html('Back / Edit RX');
                        } else {
                            $('#buttonBack').html('<i class="fa fa-arrow-left"></i> Back / Edit RX');
                        }
                    }

                    if (RXCreate.collapseFlag) {
                        $('#stepTwo').addClass('expanded');
                        $('#step2Container').show();
                        RXCreate.collapseFlag = false;
                    }
                }
            });
        });

        $('#step3Link').click(function(event) {
            event.preventDefault();

            if (!RXCreate._validateRxForm()) {
                return false;
            }

            RXCreate._handleStepsVisile(3);

            $('.mt-step-col').eq(2).addClass('done');
            $('#reviewBtn').removeClass('disabled');
            $('#reviewBtn').addClass('green-seagreen');

            $('#isBack').val(1);
        });

        $('#step1Link').click(function(event) {
            event.preventDefault();
            RXCreate._handleStepsVisile(1);
        });
    },
    reviewRx:function(){
        if(this.haveStockdrug){
            $("#modal-notify-drug").modal();
        } else {

            this._getReviewContent();
        }
    },
    validateStockDrug: function () {
        RXCreate.haveStockdrug = false;
        $.ajax({
            url: $("#check-drug-url").val(),
            type: "POST",
            data: {'drugIds': $('#drugIds').val()},
            success: function(response) {
                if(response.status) {
                    if(response.total > 0 ){
                        RXCreate.haveStockdrug = true;
                        RXCreate.renderStockNotify(response.data);
                    }
                }
            },
            complete: function () {
                RXCreate.reviewRx();
            }
        });
    },
    renderStockNotify: function (data) {
        var html = "";
        $.each(data, function () {
            html += "<p>" + this + "</p>";
        });

        $("#modal-notify-drug").find(".drug-list").html(html);
        $("#notify-drug-accept").unbind('click');
        $("#notify-drug-accept").on("click", function () {
            $("#modal-notify-drug").modal('toggle');
            RXCreate._getReviewContent();
        });


    },
    autoSaveAsDraft: function() {
        if ($('#isCreate').val() == 'true') {

            var scheduledSendDate = $('#scheduled_send_date').val();
            if (scheduledSendDate != "") {
                $('#isScheduledRx').val(1);
                $('#scheduledSendDate').val(scheduledSendDate);
            } else {
                $('#isScheduledRx').val(0);
                $('#scheduledSendDate').val('');
            }

            RXCreate.autosaveTimeout = setTimeout(function(){
                var url = $('#ajaxSaveAsDraft').val();
                var data = $('#rxForm').serialize();
                $.ajax({
                    url: url+'?get_type=autosave',
                    data: data,
                    method: 'POST',
                    beforeSend: function() {
                        $('.saveAsDraft').addClass('disabled');
                        $('#autosave-process').show();
                    },
                    success: function(response) {
                        $('#autosave-process').hide();
                        $('#autosave-done').show();
                        $('#rxId').val(response.rx_id);
                        $('.saveAsDraft').removeClass('disabled');
                        RXCreate.savingAsDraft();
                        setTimeout(function () {
                            $('#autosave-done').hide();
                            RXCreate.autoSaveAsDraft();
                        }, 1500);
                    }
                });
            }, 10000);
        }
    },
    savingAsDraft: function (isSaving = false) {
        $('#saved-draft-notification').show();
        if (isSaving) {
            $('#saving-draft-notification').html('<i class="fa fa-save"></i> Saving changes...');
        } else {
            $('#saving-draft-notification').html('<i class="fa fa-check"></i> Your changes are saved');
            if (RXCreate.savedAsDraftShowTimer != null) {
                clearTimeout(RXCreate.savedAsDraftShowTimer);
            }
            RXCreate.savedAsDraftShowTimer = setTimeout(function () {
                $('#saving-draft-notification').html('');
            }, 2000);

        }
    },
    onClickSaveAsDraft: function() {
        $('.saveAsDraft').click(function() {
            if (false == $('#rxDrugIds').val() &&
                    false == $('#drugIds').val()) {
                return false;
            }

            if (!$('#listOfDrugs').find('table').length) {
                return false;
            }
            var rules = {};
            rules['rxReviewFee'] = {
                number: true,
                required: true,
                greaterThan: 0
            };
            var validate = $('#rxForm').validate({
                rules: rules,
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    if ('rxReviewFee' === element.attr('name')) {
                        element.before(error);
                    }
                    return false;
                }
            });

            var rxReviewFee = $.trim($("#rxReviewFee").val());
            if(rxReviewFee < 0 || $("#rxReviewFee").hasClass('error'))
                return false;

            var scheduledSendDate = $('#scheduled_send_date').val();
            if (scheduledSendDate != "") {
                $('#isScheduledRx').val(1);
                $('#scheduledSendDate').val(scheduledSendDate);
            } else {
                $('#isScheduledRx').val(0);
                $('#scheduledSendDate').val('');
            }

            var url  = $(this).data('url');
            var data = $('#rxForm').serialize();
            $.ajax({
                url: url,
                data: data,
                method: 'POST',
                beforeSend: function() {
                    $('.saveAsDraft').addClass('disabled');
                    RXCreate.savingAsDraft(true);
                },
                success: function(response) {
                    if (response.rx_id) {
                        $('#rxId').val(response.rx_id);
                    }
                    $('.saveAsDraft').removeClass('disabled');
                    RXCreate.savingAsDraft();
                    // window.location.href = response.data;
                }
            });
        });
    },
    onSubmitForm: function() {
        var rules = {
        };

        $('.requiredItem').each(function() {
            rules[$(this).prop('name')] = {
                required: true
            }
        });
        rules['rxReviewFee'] = {
            number: true,
            required: true,
            greaterThan: 0
        };

        $('#rxForm').validate({
            rules: rules,
            submitHandler: function(form) {
                form.submit();
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                if ('lengthOfSupply' === element.attr('name')) {
                    element.parent().append($('<div></div>').append(error));
                }
                if ('rxReviewFee' === element.attr('name')) {
                    element.before(error);
                }
                return false;
            }
        });

        $('#rxForm').submit(function(event) {
            event.preventDefault();
        });

        $('#confirm-schedule-date').click(function () {
            $('#modalScheduleRxOrder').modal('hide');
        });

        $('#save-scheduled-rx').click(function () {
            var scheduledSendDate = $('#scheduled_send_date').val();
            if (scheduledSendDate != "") {
                $('#isScheduledRx').val(1);
                $('#scheduledSendDate').val(scheduledSendDate);
                $('#scheduled_send_date_error').hide();
                $(this).addClass('disabled');
                clearTimeout(RXCreate.autosaveTimeout);
                var url  = $('#forwardRxToDoctor').val();
                if (url) {
                    RXCreate.forwardRxToDoctor();
                } else {
                    $('#rxForm').submit();
                }
            } else {
                $('#scheduled_send_date_error').text('Date to send can\'t be blank').show();
            }
        });

        $('#confirmBtn').click(function() {
            $('#isScheduledRx').val(0);
            $('#scheduledSendDate').val('');

            $(this).addClass('disabled');
            clearTimeout(RXCreate.autosaveTimeout);
            var url  = $('#forwardRxToDoctor').val();
            if (url) {
                RXCreate.forwardRxToDoctor();
            } else {
                $('#rxForm').submit();
            }
        });

        $('#modal-forward-success').on('hide.bs.modal', function (e) {
            window.location.href = $('#draft-rx-url').val();
        });

        $('#modal-forward-failed').on('hide.bs.modal', function (e) {
            window.location.href = $('#draft-rx-url').val();
        });
    },
    onClickButtonBack: function() {
        $('#buttonBack').click(function() {
            $('#stepFour').hide();
            $('#stepThree').show();
            $('#stepTwo').show();
            $('#stepOne').show();

            $('#saveContainer').show();
            $('#confirmContainer').hide();

            $('#printPdfContainer').hide();

            // disable reload step 2
            $('#isBack').val(1);
        });
    },
    onClickDeleteButton: function() {
        $('#deleteBtn').click(function() {
            var href = $(this).data('url') || '';
            $('#modal-delete').find('#confirmBtn').attr('href', href);
        });
    },
    onClickPrintPdfButton: function() {
        $('#stepFour').on('click', '#printPdfBtn', function() {
            if (navigator.userAgent.indexOf("Firefox") > 0) {
                var win = window.open('', '', 'width=500,height=400,left=0,top=0,toolbar=0,scrollbars=0,status=0');
                win.document.write(document.getElementById('iframeOne').innerHTML);
                win.document.close();
                win.focus();
                win.print();
                win.close();
            } else {
                window.frames['iframeOne'].print();
            }
        });
        $('#stepFour').on('click', '#printProformaPdfBtn', function() {
            if (navigator.userAgent.indexOf("Firefox") > 0) {
                var win = window.open('', '', 'width=600,height=400,left=0,top=0,toolbar=0,scrollbars=0,status=0');
                win.document.write(document.getElementById('iframeProforma').innerHTML);
                win.document.close();
                win.focus();
                win.print();
                win.close();
            } else {
                window.frames['iframeProforma'].print();
            }
        });
    },
    forwardRxToDoctor: function() {
        var url  = $('#forwardRxToDoctor').val();
        var data = $('#rxForm').serialize();
        $.ajax({
            url: url,
            data: data,
            method: 'POST',
            beforeSend: function() {
                $('#confirmBtn').addClass('disabled');
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-forward-success').modal('show');
                } else {
                    $('#modal-forward-failed').modal('show');
                }
            }
        });
    },
    _validateRxForm: function() {
        // validate for select 2
        var check = true;
        $("#rxForm").find('.select2').each(function(){
            if($(this).attr('name') != 'lengthOfSupply') {
                if($(this).val() == '-1' && !$(this).parents('td').first().find('.checkboxPRN').is(':checked')) {
                    check = false;
                    $(this).parent().find('span').each(function(){
                        if($(this).hasClass('error')) {
                            $(this).remove();
                        }
                    });
                    $(this).parent().append("<span class='error'>This field is required </span>");
                }
            }
        });
        if (1 === $('#listOfDrugs tbody').find('tr').length) {
            if (0 == $('#listOfDrugs tbody').find('tr').find('.requiredItem').val()) {
                check = false;
            }
        }
        if(!check) {
            return false;
        }

        if (false === $('.mt-step-col').eq(1).hasClass('done')) {
            return false;
        }

        if (false == $('#rxDrugIds').val() &&
                false == $('#drugIds').val()) {
            return false;
        }

        if (false === $('#rxForm').valid()) {
            return false;
        }

        return true;
    },
    _addSelectedMedications: function(activeTab, isDrug, fromHistory) {
        var isDrug = isDrug || false;
        var fromHistory = fromHistory || false;
        var container = $('#rxDrugIds');
        if (fromHistory) {
            container = $('#drugIds');
        } else {
            if (isDrug) {
                container = $('#drugIds');
                activeTab = $('#drugContainer');
            }
        }

        // Reset
        container.val('');

        var listId     = [];
        var listDrugId = [];
        activeTab.find('.drugCheckbox:checked').each(function() {
            var drugId = $(this).data('drugid');
            if ($.inArray(drugId, listId) >= 0) {
                return true;
            }
            listId.push(drugId);

            if (!isDrug) {
                listDrugId.push($(this).data('itemid'));
            }
        });

        container.val(listId.join(','));

        if (!isDrug) {
            var oldList = $('#extIds').val().split(',');
            $('#extIds').val(listDrugId.join(','));

            // Remove old item
            var list   = $('#drugIds').val().split(',');
            var result = list.filter(function(value) {
                return $.inArray(value.toString(), oldList) < 0;
            });

            // Add new
            $.each(listDrugId, function(index, value) {
                if ($.inArray(value, result) < 0) {
                    result.push(value);
                }
            });

            $('#drugIds').val(result.join(','));
        }

        RXCreate._medicineCounter();

        // reload step 2
        $('#isBack').val('');
    },
    _medicineCounter: function() {
        var list   = $('#drugIds').val().split(',');
        var result = list.filter(function(item) {
            return item;
        });

        var total = result.length;
        var html = "";
        if (total > 0) {
            var template = Handlebars.compile($('#medicineCounterTemplate').html());
            html = template({'value': total, 'plural': total > 1});
            $('.notifyMedication').show();
        }
        $('#medicineCounter').html(html);
    },
    _checkCheckedItems: function(table, isSearch) {
        var activeTab = '';
        var list = $('#rxDrugIds').val();
        var listArr = list.split(',');

        var list1 = $('#drugIds').val();
        var listArr1 = list1.split(',');

        var isSearch = isSearch || false;

        $.merge(listArr, listArr1);

        table.find('input[type="checkbox"]').each(function() {
            var val = $(this).data('drugid');
            if (!val) {
                return true;
            }

            if ($.inArray(val.toString(), listArr) >= 0) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        });

        if ('searchTabContent' !== table.attr('id')) {
            return false;
        }

        if (isSearch) {
            table.find('.addedBtn').remove();
        }

        $.each(listArr, function(index, value) {
            if (value) {
                table.find('a[data-drugid='+value+']').addClass('disabled');
                if (isSearch) {
                    table.find('a[data-drugid='+value+']').after($('#addedBtnTemplate').html());
                }
            }
        });
    },
    _sigPreview: function(parent) {
        var list = [];
        $(parent).find('.sigPreviewElem').each(function() {
            var text = $(this).find('option:selected').text();
            if ('others' === $(this).val()) {
                text = $(this).parent().find('input').val();
            }
            var plural = $(this).find('option:selected').data('plural');
            if (plural) {
                if ($(parent).find('select').eq(1).val() > 1) {
                    text = plural;
                }
            }
            if (text && 'n/a' != text) {
                if ($(this).data('text')) {
                    list.push($(this).data('text'));
                }
                list.push(text);
            }

            $(this).removeAttr('disabled');
            $(this).parent().find('input').removeAttr('disabled');
            $(this).parents('tr').first().find('.otherElem').removeAttr('disabled');
        });

        $(parent).find('.sigPreview').text(list.join(' '));
    },
    _getReviewContent: function() {
        var url = this.reviewUrl;
        var data = $('#rxForm').serialize();
        $.ajax({
            url: url,
            data: data,
            method: 'POST',
            beforeSend: function() {
                $('#reviewBtn').addClass('disabled');
            },
            success: function(response) {
                if (!response) {
                    return false;
                }

                $('.mt-step-col').eq(3).addClass('done');

                $('#stepThree').hide();
                $('#stepTwo').hide();
                $('#stepOne').hide();
                $('#stepFour').html(response);
                $('#stepFour').show();

                $('#saveContainer').hide();
                $('#confirmContainer').show();

                if($('#iframeProforma').length > 0) {
                    $('#rxForm').attr('target', $('#iframeProforma').attr('name'));
                    var url1 = $('#iframeProforma').data('url');
                    $('#rxForm').attr('action', url1);
                    $('#iframeProforma').attr('src', url1);
                    $('#rxForm').submit();
                }

                $('#rxForm').attr('target', $('#iframeOne').attr('name'));
                var url = $('#iframeOne').data('url');
                $('#rxForm').attr('action', url);
                $('#iframeOne').attr('src', url);
                $('#rxForm').submit();

                var url = $('#rxForm').data('action');
                $('#rxForm').attr('action', url);
                $('#rxForm').removeAttr('target');

                RXCreate._initIcheck($('#stepFour'));
            },
            complete: function() {
                $('#reviewBtn').removeClass('disabled');
                $('#printPdfContainer').show();
            }
        });
    },
    _handleStepsVisile: function(step) {
        if (step === 1) {
            $('#stepTwo').addClass('unselected');
            $('#stepTwo').removeClass('expanded');
            $('#step2Container').hide();

            $('#stepThree').addClass('unselected');
            $('#stepThree').removeClass('expanded');
            $('#stepThree').find('.portlet-body').hide();

            $('#stepOne').removeClass('unselected');
        } else if (step === 2) {
            $('#stepOne').addClass('unselected');
            $('#stepOne').removeClass('expanded');
            $('#stepOne').find('.portlet-body').hide();

            $('#stepThree').addClass('unselected');
            $('#stepThree').removeClass('expanded');
            $('#stepThree').find('.portlet-body').hide();

            $('#stepTwo').removeClass('unselected');
        } else {
            $('#stepOne').addClass('unselected');
            $('#stepOne').removeClass('expanded');
            $('#stepOne').find('.portlet-body').hide();

            $('#stepTwo').addClass('unselected');
            $('#stepTwo').removeClass('expanded');
            $('#step2Container').hide();

            $('#stepThree').removeClass('unselected');
        }
    },
    _checkboxPRN: function () {
        $('.checkboxPRN').on('ifChanged', function() {
            var parent = $(this).parents('tr').first();
            if ($(this).is(':checked')) {
                var text = 'Take as needed';
                $(parent).find('.sigPreviewElem').attr('disabled', true);
                $(parent).find('.otherInput').attr('disabled', true);
                $(parent).find('.otherElem').attr('disabled', true);
                $(parent).find('.sigPreview').text(text);
                return true;
            }
            RXCreate._sigPreview(parent);
        });

        $('.sigPreviewElem').change(function() {
            if ('others' === $(this).val()) {
                $(this).parent().addClass('open');
                $(this).parent().find('input').removeClass('hide');
            } else {
                $(this).parent().removeClass('open');
                $(this).parent().find('input').addClass('hide');
            }

            var parent = $(this).parents('tr');
            RXCreate._sigPreview(parent);
            
            var packQuantity = $(this).attr('data-pack');
            var label = $(this).attr('data-label');
            var text = $(this).find('option:selected').text();
            var plural = $(this).find('option:selected').data('plural');

            if (typeof label !== typeof undefined && label !== false) {
                if ('others' !== $(this).val()) {
                    if (typeof packQuantity !== typeof undefined && packQuantity !== false && packQuantity > 1) {
                        $('.'+label).html(plural);
                    } else {
                        $('.'+label).html(text);
                    }
                }
            }
        });

        $('.otherInput').keyup(function() {
            var label = $(this).attr('data-label');
            if (typeof label !== typeof undefined && label !== false) {
                $('.'+label).html( $(this).val());
            }
            var parent = $(this).parents('tr');
            RXCreate._sigPreview(parent);
        });

        $('.takenWithFood').on('ifChanged', function() {
            var parent = $(this).parents('tr').first();
            RXCreate._sigPreview(parent);
        });

        $('.checkboxPRN').each(function() {
            if ('checked' === $(this).data('val').trim()) {
                $(this).iCheck('check');
                return true;
            }
        });

    },
    _radioReminder: function() {
        $('#refillYes').on('ifChanged', function() {
            if ($('#refillYes').is(':checked')) {
                $('#rxPeriodContainer').show();
            } else {
                $('#rxPeriodContainer').hide();
            }
        });
    },
    _initIcheck: function(parent) {
        parent.find('.icheck').each(function() {
            var checkboxClass = $(this).attr('data-checkbox') ? $(this).attr('data-checkbox') : 'icheckbox_minimal-grey';
            var radioClass = $(this).attr('data-radio') ? $(this).attr('data-radio') : 'iradio_minimal-grey';

            if (checkboxClass.indexOf('_line') > -1 || radioClass.indexOf('_line') > -1) {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass,
                    insert: '<div class="icheck_line-icon"></div>' + $(this).attr("data-label")
                });
            } else {
                $(this).iCheck({
                    checkboxClass: checkboxClass,
                    radioClass: radioClass
                });
            }
        });
    },
    _checkSortOrder: function(sorting) {
        if (!sorting) {
            return false;
        }

        var list = sorting.split('-');
        var sort = list[0];
        var order = list[1];

        $('.sorting').removeClass('sorting_desc');
        $('.sorting').removeClass('sorting_asc');

        if ('asc' === order) {
            $('th[data-sort="' + sort + '"]').removeClass('sorting_desc');
            $('th[data-sort="' + sort + '"]').addClass('sorting_asc');
        } else {
            $('th[data-sort="' + sort + '"]').removeClass('sorting_asc');
            $('th[data-sort="' + sort + '"]').addClass('sorting_desc');
        }
    },
    _getSorting: function(object) {
        var list = [];
        list.push(object.data('sort'));
        if (object.hasClass('sorting_asc')) {
            list.push('desc');
        } else {
            list.push('asc');
        }

        return list.join('-');
    },
    initShortLifeEvent: function () {
        $('[data-toggle="popover"]').popover({
            container: 'body',
            html: true,
            content: function() {
                var content = $(this).attr("data-popover-content");
                return $(content).children(".popover-body").html();
            }
        });

        $('[data-toggle="popover"]').on('inserted.bs.popover', function () {
            $('.popover').addClass('popover-important-note');
        });

        $('body').on("click", ".popover .popover-close" , function(e){
            $(this).parents(".popover").popover('hide');
            e.preventDefault();
        });

        $('body').on('click', function (e) {
            $('[data-toggle="popover"]').each(function () {
                //the 'is' for buttons that trigger popups
                //the 'has' for icons within a button that triggers a popup
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });
    },
    checkEditRxSession: function () {
        if (checkEditRxAction()){
            setInterval(checkEditRxAction, 2000);
        }
        function checkEditRxAction() {
            var flag = true;
            $.ajax({
                type: "POST",
                async: false,
                data: {
                    'rxId': $('#rxId').val(),
                    'curUser': curUser,
                    'curUserType': curUserType
                },
                url: checkEditRxSessionUrl,
                success: function(resp){
                    if(resp.status == 1){
                        clearInterval(checkEditRxAction);
                        $('#modal-locking-edit').modal({
                            show: true,
                            keyboard: false,
                            backdrop: 'static'
                        });

                        // Close modal then redirect to dashboard
                        $('#modal-locking-edit').on('hidden.bs.modal', function () {
                            var url = $('#modal-locking-edit').attr('data-url');
                            window.location.href = url;
                        });

                        $('#currentEdit').text(resp.currentEdit);
                        flag = false;
                    }
                },
            });
            return flag;
        }
    },
    autoClearEditRxSession: function () {
        var SetClearSessions;
        
        SetClearSessions = setInterval(clearEditSession, 900000);
        
        $(document).on('mousemove', function() {
            clearInterval(SetClearSessions);
            SetClearSessions = setInterval(clearEditSession, 900000);
        }); 
        
        function clearEditSession() {
            $('#modal-locking-edit .modal-header .modal-title').text('This page has expired due to inactivity, you can edit this Rx again by going to Rx > Draft Rx.');
            $('#modal-locking-edit .modal-body .mbRemove').remove();
            
            clearInterval(SetClearSessions);

            //Popup inform and redirect to dashboard
            $('#modal-locking-edit').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });

            // Close modal then redirect to dashboard
            $('#modal-locking-edit').on('hidden.bs.modal', function () {
                var url = $('#modal-locking-edit').attr('data-url');
                window.location.href = url;
            });
        }
    },
    rotateIcon: function () {
        if(!$('#popup_notification').hasClass('in')) {
            $('#pin_icon').css('transform', 'rotate(0deg)')
        }
        else {
            $('#pin_icon').css('transform', 'rotate(180deg)')
        }
    }
};

$(document).ready(function() {
    $.validator.addMethod("greaterThan",
        function (value, element, param) {
            return parseInt(value) >= param;
        }, "RX review fee must more than SGD 0.00");
    RXCreate.init();
    RXCreate.autoSaveAsDraft();
    if (typeof curUserType !== 'undefined' && (curUserType == 'MPA' || curUserType == 'Doctor')){
        RXCreate.autoClearEditRxSession();
        RXCreate.checkEditRxSession();
    }
});
