var jsPayment = {
    init: function() {
        jsCommon.pagingAjax();
        this.search();
        this.psOnePicked();
        this.psMultiPicked();
    },
    search: function(){
        var df = $("#frm-ps-filter");
        var dc = $("div.content-table.ps-container");
        //var date = moment();
        $('.ps-filter-date-picker').datepicker({
            viewMode: "months",
            minViewMode: "months",
            orientation: "bottom",
            autoclose: true,
            format: 'M yyyy'
        }).datepicker('setDate', 'now');
        //on action
        df.on('click', '.btn-ps-search', function(e){
            e.preventDefault();
            jsPayment.getData();
        }).on('keyup','#ps_filter_term', function(e) {
            e.preventDefault();
            if (e.keyCode == 13) {
                jsPayment.getData();
            }
        }).on('click','#ps_filter_status > a', function(e){
            e.preventDefault();
            if(!$(this).hasClass('active')){
                $(this).closest('div').find('a').removeClass('active');
                $(this).addClass('active');
                $(this).closest('div').find('input').val($(this).data('value'));
                jsPayment.getData();
            }
        }).on('change','#ps_filter_per_page', function(e){
            e.preventDefault();
            jsPayment.getData();
        }).on('change', '#ps_filter_date', function(e){
            e.preventDefault();
            jsPayment.getData();
        }).on('change','#ps_filter_cycle', function(e) {
            e.preventDefault();
            jsPayment.getData();
        });
        //on sorting
        dc.on('click', 'th.sorting', function(e){
            e.preventDefault();
            var colName = $(this).data('colname');
            if($(this).hasClass('sorting_asc')){
                $(this).removeClass('sorting_asc');
                $(this).addClass('sorting_desc');
                df.find("input[name='ps_filter[sorting]']").val(colName+'_desc');
            } else {
                $(this).removeClass('sorting_desc');
                $(this).addClass('sorting_asc');
                df.find("input[name='ps_filter[sorting]']").val(colName+'_asc');
            }
            jsPayment.getData();
        });
        jsCommon.suggestion($("#url_payment_status_suggestion").val(), {userType: $("input[name='ps_filter[userType]']").val()});
    },
    getData: function(){
        successCallback = function (res) {
            $("div.content-table.ps-container" ).html(res);
        };
        errorCallback = function (xhr, ajaxOptions, thrownError) {
        };
        jsDataService.callAPI($("#url_payment_status_filter").val(), $('#frm-ps-filter').serialize(), "POST", successCallback, errorCallback, null, 'html');
    },
    psOnePicked: function(){
        var mup = $("#modal-update-payment");
        $(document).on('click', ".btn-modal-update-payment", function(e){
            e.preventDefault();
            var self_tr = $(this).closest('tr');
            var params = {userId: self_tr.data('userid'), userType: self_tr.data('usertype'), datePaid: self_tr.data('datepaid'), multi: 0};
            successCallback = function (res) {
                mup.find("div.content-views").html(res);
                mup.find(".msg-container").html("");
                mup.modal();
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_payment_status_detail").val(), params, "POST", successCallback, errorCallback, null, 'html');
        });
        $(document).on('click', ".btn-modal-update-ps-pharmacy", function(e){
            e.preventDefault();
            var self_tr = $(this).closest('tr');
            var params = {id: self_tr.data('id'), userType: self_tr.data('usertype'), multi: 0};
            successCallback = function (res) {
                mup.find("div.content-views").html(res);
                mup.find(".msg-container").html("");
                mup.modal();
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_payment_status_detail").val(), params, "POST", successCallback, errorCallback, null, 'html');
        });
        $(document).on('click', ".btn-modal-update-ps-4", function(e){
            e.preventDefault();
            var self_tr = $(this).closest('tr');
            var params = {id: self_tr.data('id'), userType: self_tr.data('usertype'), multi: 0};
            successCallback = function (res) {
                mup.find("div.content-views").html(res);
                mup.find(".msg-container").html("");
                mup.modal();
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_payment_status_detail").val(), params, "POST", successCallback, errorCallback, null, 'html');
        });
        //on input
        mup.on('focus', 'input.on-money-format', function(){
            var self = $(this);
            self.val(self.val().replace(',',''));
        }).on('blur', 'input.on-money-format', function(){
            var self = $(this);
            var v = (self.val() != "")? self.val().replace(/[^0-9\.]/g, ''): 0;
            self.val(jsCommon.currencyFormat(v));
        }).on('input', 'input.on-money-format', function(e) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((e.which != 46 || self.val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)){
                e.preventDefault();
            }
        });
        jsPayment.psOneUpdate(mup);
    },
    psMultiPicked: function(){
        var df = $("#frm-ps-filter");
        var dc = $("div.content-table.ps-container");
        dc.on('click', '.checkedAll', function() {
            var checked = $(this).prop('checked');
            dc.find('input.checkboxes').not(":disabled").prop('checked', checked);
            var searchIDs = dc.find('input:checked').map(function(){
                if($(this).val() != 'on')
                    return $(this).val();
            });
            countItem = searchIDs.get().length;
            if(countItem > 1) {
                dc.find("span.items_picked").html(countItem);
                dc.find("input[name=items_picked]").val(searchIDs.get());
                dc.find(".items-selected-info").show();
                $('.btn-modal-update-multi').attr('data-usertype', $(this).data('usertype'));
            } else {
                dc.find("input[name=items_picked]").val("");
                dc.find(".items-selected-info").hide();
            }
        });
        dc.on('click', 'input.checkboxes', function(e){
            var checked = $(this).prop('checked');
            //dc.find('input.checkboxes').prop('checked', checked);
            var searchIDs = dc.find('input:checked').map(function(){
                if($(this).val() != 'on')
                    return $(this).val();
            });
            //console.log(searchIDs.get());
            countItem = searchIDs.get().length;
            if(countItem > 1) {
                dc.find("span.items_picked").html(countItem);
                dc.find("input[name=items_picked]").val(searchIDs.get());
                dc.find(".items-selected-info").show();

                var userType = $(this).closest('tr').data('usertype');
                $('.btn-modal-update-multi').attr('data-usertype', userType);
            } else {
                dc.find("input[name=items_picked]").val("");
                dc.find(".items-selected-info").hide();
            }
        });
        dc.on('click', '.btn-clear-all-checkbox', function(e){
            dc.find('input:checked').prop('checked', false);
            $(this).closest("div.items-selected-info").hide();
        });
        var mup_multi = $("#modal-update-payment-multi");
        $(document).on('click', ".btn-modal-update-multi", function(e){
            e.preventDefault();
            mup_multi.find(".msg-container").html("");
            var params = {items: dc.find("input[name=items_picked]").val(), multi: 1};
            params.userType = $(this).data('usertype');
            successCallback = function (res) {
                mup_multi.find(".update-info-carousel").html(res)
                    .on('focus', 'input.on-money-format', function(){
                    var self = $(this);
                    self.val(self.val().replace(',',''));
                }).on('blur', 'input.on-money-format', function(){
                    var self = $(this);
                    var v = (self.val() != "")? self.val().replace(/[^0-9\.]/g, ''): 0;
                    self.val(jsCommon.currencyFormat(v));
                }).on('input', 'input.on-money-format', function(e) {
                    var self = $(this);
                    self.val(self.val().replace(/[^0-9\.]/g, ''));
                    if ((e.which != 46 || self.val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)){
                        e.preventDefault();
                    }
                });
                mup_multi.find(".msg-container").html("");
                mup_multi.on('shown.bs.modal', function (e) {
                    if ($('.update-info-carousel').hasClass('slick-initialized')) {
                        $('.update-info-carousel').slick('destroy');
                    }

                    (function(factory) {
                        'use strict';
                        factory(jQuery);
                        $('.update-info-carousel').slick({
                            infinite: true,
                            initialSlide: 0,
                            centerMode: true,
                            centerPadding: '90px',
                            slidesToShow: 1,
                            prevArrow: '<button type="button" data-role="none" class="slick-prev" aria-label="Previous" tabindex="0" role="button"><i class="fa fa-angle-left"></i></button>',
                            nextArrow: '<button type="button" data-role="none" class="slick-next" aria-label="Next" tabindex="0" role="button"><i class="fa fa-angle-right"></i></button>',
                            responsive: [{
                                    breakpoint: 991,
                                    settings: {
                                        centerPadding: '0px'
                                    }
                                }]
                        });
                    });
                    $('.update-info-carousel').removeClass('slick-adapt-modal');
                }).modal();

                var totalItems = params.items.split(',');
                //var totalItems = $('.update-info-carousel > div').length;
                $('.slick-items .total').html(totalItems.length);

                // On after slide change
                $('.update-info-carousel').on('afterChange', function(event, slick, currentSlide, nextSlide){
                    $('.slick-items .current').html(currentSlide + 1);
                });
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_payment_status_detail").val(), params, "POST", successCallback, errorCallback, null, 'html');
        });
        jsPayment.psOneUpdate(mup_multi);
    },
    psOneUpdate: function(element) {
        $(element).on('click', ".btn-ps-update", function(e){
            e.preventDefault();
            var self = $(this);
            successCallback = function (res) {
                var msgText = '';
                if($.isEmptyObject(res)){
                    msgText = '<div class="alert alert-danger"><button class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button> '+ msg.msgCannotEdited.replace('%s', 'Payment') +'</div>';
                } else {
                    msgText = '<div class="alert alert-success"><button class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button> '+ msg.msgUpdatedSuccess.replace('%s', 'Payment') +'</div>';
                    jsPayment.getData();
                }
                self.closest('form').find(".msg-container").html(msgText);
            };
            errorCallback = function (xhr, ajaxOptions, thrownError) {
            };
            jsDataService.callAPI($("#url_payment_status_update").val(), self.closest('form').serialize() , "POST", successCallback, errorCallback, null, 'json');
        });
    }
};

$(document).ready(function() {
    jsPayment.init();
});