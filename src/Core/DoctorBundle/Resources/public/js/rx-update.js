var RXCreate = {
    init: function() {
        RXCreate.onSearchDrug();
        RXCreate.onClickContinue();
        RXCreate.onClickAddMedication();
        RXCreate.onClickSaveAsDraft();
       // RXCreate.onClickDateTabs();
        RXCreate.onClickButtonBack();
        RXCreate.onClickTop30Tab();
        RXCreate.onClickMyFavoriteTab();
        RXCreate.onClickReview();
        // RXCreate.onChangeRXReviewFee();
        RXCreate.onClickDeleteButton();
        RXCreate.onClickPrintPdfButton();
        RXCreate.onClickDeleteDrugButton();
        RXCreate.bootstrap();
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

        // Medicine counter
        RXCreate._medicineCounter();

        // Trigger firt tab


    },
    // onChangeRXReviewFee: function() {
    //     $('#chargeRXReviewFee').on('ifChanged', function() {
    //         if ($('#chargeRXReviewFee').is(':checked')) {
    //             $('#reviewFeeContainer').removeClass('hide');
    //         } else {
    //             $('#reviewFeeContainer').addClass('hide');
    //         }
    //     });
    // },
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
                }
            });
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
                }
            });
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
                }
            });
        });

        $('#stepOne').on('click', '.favoriteLink', function(e) {
           e.preventDefault();
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
                }
            });
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
    onClickReview: function() {
        $('#reviewBtn').click(function(event) {
            event.preventDefault();
            if (!RXCreate._validateRxForm()) {
                return false;
            }
            RXCreate._getReviewContent();
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
    loadStep2Content(ele){
        RXCreate._handleStepsVisile(2);
        var step2Url = $(ele).data('url');

        $.ajax({
            url: step2Url,
            method: 'POST',
            data: {'rxDrugIds': $('#rxDrugIds').val(), 'drugIds': $('#drugIds').val(), 'patientId': $('#patientId').val()},
            success: function(response) {

                $('#listOfDrugs').html(response);

                $('.mt-step-col').eq(1).addClass('done');

                $(".touchspin_7").TouchSpin({
                    initval: 40
                }).on('change', function() {
                    var qSpan = $(this).parents('tr').first().find('.quantitySpan');
                    var value = qSpan.data('origin');
                    qSpan.text(value*$(this).val());
                });

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
                $("#step2Container").attr('style','display:block');
                $('#listOfDrugs').find('tr').each(function(){
                   $(this).find('td').first().find('input').attr('readonly','true');
                    $(this).find('td').first().find('button').attr('disabled',true);
                    $(this).find('td').first().find('.removeDrugBtn').attr('disabled',true);
                    $(this).find('td').first().find('.removeDrugBtn').unbind('click');
                });
                $("#step2Container").find('input').each(function(){
                    if($(this).attr('name') == 'refillReminder'){
                        $(this).attr('disabled',true);
                    }
                });
                $("#lengthOfSupply").select2('destroy');
                $("#lengthOfSupply").attr('disabled',true);
            }
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



            if ($('#isBack').val()) {
                return true;
            }
            RXCreate.loadStep2Content(this)

        });

        $('#step3Link').click(function(event) {
            event.preventDefault();
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

            RXCreate._handleStepsVisile(3);

            $('.mt-step-col').eq(2).addClass('done');
            $('#reviewBtn').removeClass('disabled');
            $('#reviewBtn').addClass('green-seagreen');
            $("#stepThree").find('.icheck ').iCheck('disable');
            $('#isBack').val(1);
        });

        $('#step1Link').click(function(event) {
            event.preventDefault();
            RXCreate._handleStepsVisile(1);
        });
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

            var url  = $(this).data('url');
            var data = $('#rxForm').serialize();
            $.ajax({
                url: url,
                data: data,
                method: 'POST',
                success: function(response) {
                    if(response.success) {
                        window.location.href = response.data;
                    }
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

        $('#confirmBtn').click(function() {
            $('#rxForm').submit();
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
            window.frames['iframeOne'].print();
        });
    },
    _addSelectedMedications: function(activeTab, isDrug) {
        var isDrug = isDrug || false;
        var container = $('#rxDrugIds');
        if (isDrug) {
            container = $('#drugIds');
            activeTab = $('#drugContainer');
        }

        // Reset
        container.val('');

        var listId     = [];
        var listDrugId = [];
        activeTab.find('.drugCheckbox:checked').each(function() {
            var drugId = $(this).data('drugid');
            if ($.inArray(drugId.toString(), listId) >= 0) {
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
                if ($.inArray(value.toString(), result) < 0) {
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
        var url = $('#reviewBtn').data('url');
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
                $('.mt-step-col').eq(2).addClass('done');
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
        });

        $('.otherInput').keyup(function() {
            var parent = $(this).parents('tr');
            RXCreate._sigPreview(parent);
        });

        $('.checkboxPRN').each(function() {
            if ('checked' === $(this).data('val').trim()) {
                $(this).iCheck('check');
                return true;
            }

            var parent = $(this).parents('tr');
            RXCreate._sigPreview(parent);
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
    }
}

$(document).ready(function() {
    $.validator.addMethod("greaterThan",
        function (value, element, param) {
            return parseInt(value) >= param;
        }, "RX review fee must more than SGD 0.00");
    RXCreate.init();
    $('#step2Link').first().trigger('click');
});