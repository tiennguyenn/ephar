var MedicineList = {
    loading: false,
    openedPrice: '',
    init: function () {
        MedicineList.reloadLetterNavigator();
        MedicineList.reloadPriceToggler();
        MedicineList.initMedicineFavourite();
        $('#medicine-search').keyup(function () {
            setTimeout(function () {
                if (!MedicineList.loading) {
                    var keyword = $('#medicine-search').val();
                    MedicineList.loading = true;
                    MedicineList.getMedicineAjax(keyword);
                }
            }, 300);
        });
    },
    initMedicineFavourite: function () {
        $('.favoriteLink').click(function() {
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
    },
    getMedicineAjax: function (keyword) {
        $.ajax({
            url: $('#get-medicine-url').val(),
            data: { 'keyword': keyword },
            success: function(response) {
                $('#table-medicine').html(response);
                MedicineList.reloadLetterNavigator();
                MedicineList.reloadPriceToggler();
                MedicineList.initMedicineFavourite();
                MedicineList.loading = false;
            }
        });
    },
    reloadLetterNavigator: function () {
        $('.letter-nav').click(function () {
            var letter = $(this).data('letter');
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#letter-" + letter).offset().top - 75
            }, 500);
        });
    },
    reloadPriceToggler: function () {
        $('.show-international-price').click(function () {
            var openedPriceParent = $('#medicine-list-table').find('#drugrow_'+ MedicineList.openedPrice);
            $(openedPriceParent).find('.price-toggler').addClass('hidden');
            $(openedPriceParent).find('.drug-price-toggler').removeClass('hidden');

            var parent = $(this).parents('tr').first();
            $(parent).find('.show-international-price').addClass('hidden');
            $(parent).find('.drug-international-price').removeClass('hidden');
            MedicineList.openedPrice = parent.data('drugid');
        });

        $('.show-local-price').click(function () {
            var openedPriceParent = $('#medicine-list-table').find('#drugrow_'+ MedicineList.openedPrice);
            $(openedPriceParent).find('.price-toggler').addClass('hidden');
            $(openedPriceParent).find('.drug-price-toggler').removeClass('hidden');

            var parent = $(this).parents('tr').first();
            $(parent).find('.show-local-price').addClass('hidden');
            $(parent).find('.drug-local-price').removeClass('hidden');
            MedicineList.openedPrice = parent.data('drugid');
        });
    }
};

$(document).ready(function () {
    MedicineList.init();
});