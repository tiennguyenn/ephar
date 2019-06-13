var DataTable = $.extend(Base, {
    tableLength: 10,
    tableData: '',
    currentPage: 1,
    ajaxUrl: $("#ajax-url").val(),
    bodyTag: '#table-body-doctor',
    pagingTag: '#list-table-pagin',
    infoTag: "#list-tables-info",
    tableLenthTag: '#table-length',
    tableSearchTag: '#table-search-data',
    tableId: '#list-mpa-doctor',
    currentDeleteDelivery: '',
    dataSort: {},
    dataPost: {},
    currentSearch: false,
    maxPage : 0 ,
    autoClass: '.on-suggestion',
    attrSort:'data-colum-sort',
    doctorLoginStatus: '2',
    disable: false,
    deleteId: '',
    init: function () {

        $(this.tableSearchTag).keyup(function (event) {
//            setTimeout( function(){
//                if(!DataTable.currentSearch){
//                    $("#sample_1_filter .icon-magnifier").click();
//                    DataTable.currentSearch = true;
//                }
//            }, 200 );
            if (event.keyCode == 13) {
                $("#sample_1_filter .icon-magnifier").click();
            }
        });

        $("#sample_1_filter .icon-magnifier").on('click', function () {
            DataTable.changeSearchData($("#table-search-data").val());

        });

        $(this.tableId + ' th').on('click', function () {
            var attr = $(this).attr(DataTable.attrSort);
            if(typeof attr !== typeof undefined && attr !== false) {
                DataTable.changeSort($(this));
                DataTable.getData();
            }
        });
        $('.filter-changing a').on('click', function () {
            $(this).parent().first().find('a').removeClass('active');
            $(this).addClass('active');
            DataTable.doctorLoginStatus = $(this).attr('data');
            DataTable.currentPage = 1;
            DataTable.getData();
        });


    },

    getData: function () {
        if(this.disable){
            return;
        }
        this.dataPost = {
            'search': this.tableData,
            'length': this.tableLength,
            'page': this.currentPage,
            'sort': this.dataSort,
            'status': this.doctorLoginStatus,
            'type':10,
            'id' : $("#doctor-id").val()
        };
        $.ajax({
            type: "POST",
            url: this.ajaxUrl,
            data: this.dataPost,
            beforeSend: function () {
                $(DataTable.bodyTag).html('<tr role="row"><td colspan="5" style="text-align: center;"> Loading...</td></tr>');
            },
            success: function (data, textStatus, jqXHR) {
                var result = data['result']['data'];
                var total = data['result']['total'];
                DataTable.generateView(result);
                DataTable.generateInfo(total);
                DataTable.generatePagin(total);
                DataTable.initPaging();
                DataTable.initEventTable();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            }

        });
    },

    initEventTable: function () {
    },

    generateView: function (data)
    {
        var result = '';
        $.each(data, function () {
            result += DataTable.renderLine(this);
        });
        if (result == '') {
            result = '<tr role="row"><td colspan="7">No Records</td> </tr>';
        }
        $(this.bodyTag).html(result);
    },
    renderLine:function(line){
        var str = '<tr role="row" class="row-item rowItem">'
            + '<td>' + line.email + '</td>'
            + '<td>' + line.name + '</td>'
            + '<td> ' + line.registerDate + '</td>'
            + '<td> ' + line.lastLogin + '</td>'
            + '</tr>'

        return str;
    },



    changeSort: function (e) {
        var sort = e.attr('data-colum-sort');

        $.each($(this.tableId).find('th'), function () {
            if ($(this).attr('data-colum-sort') != sort) {
                $(this).removeClass('sorting_asc');
                $(this).removeClass('sorting_desc');
            }
        });

        DataTable.dataSort = {};

        if (typeof sort != 'undefined') {
            if (e.hasClass('sorting_asc')) {
                e.removeClass('sorting_asc');
                e.addClass('sorting_desc');
                DataTable.dataSort[sort] = 'DESC';
            } else if (e.hasClass('sorting_desc')) {
                e.removeClass('sorting_desc');
                e.addClass('sorting_asc');
                DataTable.dataSort[sort] = 'ASC';
            } else {
                e.addClass('sorting_asc');
                DataTable.dataSort[sort] = 'ASC';

            }
        }


    },

    initPaging: function () {

        $(this.pagingTag + " li").on('click', function (e) {
            e.preventDefault();
            if ($(this).is(':first-child'))
            {
                page = 'des'

            } else if ($(this).is(':last-child')) {

                page = 'inc'
            } else {
                page = $(this).find('a').html();
            }
            DataTable.changePageData(page);

        });
    },

    changeTableLenth: function (length) {
        this.currentPage = 1;
        this.tableLength = length;
        DataTable.getData();
    },

    changePageData: function (page) {
        var valid = true;
        if (page == 'inc') {
            if(this.currentPage == this.maxPage) {
                valid = false;
            } else {
                this.currentPage++;
            }

        } else if (page == 'des') {
            if(this.currentPage == 1) {
                valid = false;
            } else {
                this.currentPage--;
            }

        } else {
            this.currentPage = page;

        }
        if(valid) {
            DataTable.getData();
        }

    },

    changeSearchData: function (dataSearch) {
        this.tableData = dataSearch;
        this.currentPage = 1;
        DataTable.getData();
    },

    generateInfo: function (sum) {
        var total = sum | 0;
        if(total == 0) {
            $(this.infoTag).html("");
            return;
        }
        var start = (this.currentPage - 1) * this.tableLength + 1;
        var end = this.currentPage * this.tableLength;

        if (end > total) {
            end = total;
        }
        if (total == 0) {
            start = 0;
        }
        $(this.infoTag).html("Showing " + start + " to " + end + " of " + total + " entries")
    },

});
function number_format(number, decimals, dec_point, thousands_sep) {
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
$(document).ready(function () {

    DataTable.init();
    DataTable.getData();
});
