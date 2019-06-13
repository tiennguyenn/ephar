var PatientRxHistory = {
    init: function() {
        jsCommon.pagingAjax();
        PatientRxHistory.onClickSorting();
        PatientRxHistory.onClickStatusLink();
    },
    onClickSorting: function() {
        $('#tableContainer').on('click', '.sorting', function() {
            var list = [];
            list.push($(this).data('sort'));
            if ($(this).hasClass('sorting_asc')) {
                list.push('desc');
            } else {
                list.push('asc');
            }

            $('#sorting').val(list.join('-'));

            var url = $('#ajaxLoadUrl').val();
            var data = {'sorting': $('#sorting').val()}
            var dataType = 'html';
            var method = 'GET';

            var successCallback = function(data) {
                $("#tableContainer" ).html(data);
                PatientRxHistory._checkSortOrder();
            };
            var errorCallback = function() {};
            var loadingContainer = $("#tableContainer");
            jsDataService.callAPI(url, data, method, successCallback, errorCallback, loadingContainer, dataType);
        });
    },
    onClickStatusLink: function() {
        $('#tableContainer').on('click', '.statusLink', function() {
            $('#dateContainer').text($(this).data('date'));
            $('#modal-refill').find('.statusMessage').hide();
            $('#modal-refill').find('p[data-type="'+$(this).data('type')+'"]').show();
        });
    },
    _checkSortOrder: function() {
        var sorting = $('#sorting').val();
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
    }
}

$(document).ready(function() {
    PatientRxHistory.init();
});