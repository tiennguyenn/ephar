var Base = {
    check: true,
    generatePagin(total){
        var html = '';
        var startPage = parseInt(this.currentPage);
        var max = Math.ceil(total / this.tableLength);
        if (startPage > 1)
        {
            html += '<li class="prev "><a href="#" title="Prev"><i class="fa fa-angle-left"></i></a></li>';
        } else {
            html += '<li class="prev disabled"><a href="#" title="Prev"><i class="fa fa-angle-left"></i></a></li>';
        }
        var end =  0;
        if(startPage > 1){
            end = startPage + 3;
        } else {
            end = startPage + 4;
        }

        if (end > max)
        {
            end = max;
        }
        var start = 1;
        if (startPage > 2 && end > 5) {
            start = end - 4;
        }
        for (var i = start; i <= end; i++) {
            if (i == startPage) {
                html += '<li class="active"><a href="#">' + i + '</a></li>';
            } else {
                html += '<li ><a href="#">' + i + '</a></li>';
            }
        }

        if (startPage == max) {
            html += '<li class="next disabled"><a href="#" title="Next"><i class="fa fa-angle-right"></i></a></li>'
        } else {
            html += '<li class="next"><a href="#" title="Next"><i class="fa fa-angle-right"></i></a></li>'
        }
        if (total > 0 && max > 1) {
            $(this.pagingTag).html(html);
        } else {
            $(this.pagingTag).html('');
        }
        this.maxPage = max;


    },
    removeAlert(){
        setTimeout(function(){
            $(".alert").remove();
        }, 5000);
    }
};