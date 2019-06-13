var DataTable = $.extend(Base, {
    tableLength : 5,
    tableData : '',
    currentPage : 1,
    ajaxUrl : $("#ajax-url").val(),
    bodyTag: '#table-body-pharmacy',
    pagingTag: '#list-table-pagin',
    infoTag:"#list-tables-info",
    tableLenthTag: '#table-length',
    tableSearchTag: '#table-search-data',
    dataPost: {},
    init: function() {        
        $(this.tableSearchTag).keyup(function(event) {        
           if(event.keyCode === 13){              
                $("#sample_1_filter .icon-magnifier").click();
            }
        });

        $("#sample_1_filter .icon-magnifier").on('click',function(){
            DataTable.changeSearchData($("#table-search-data").val());
        });
        $(this.tableLenthTag).on('change',function(){
            
           DataTable.changeTableLenth(this.value);
        });
        this.initPaging();
        

    },   
    initPaging: function(){
        $(this.pagingTag+ " li").on('click', function(e){
            
            e.preventDefault();
                  
            if($(this).is(':first-child'))
            {
                page = 'des'
                
            } else if($(this).is(':last-child')){
              
                page = 'inc'
            } else {
                page = $(this).find('a').html();
            }
            DataTable.changePageData(page);
           
        });
    },
    changeTableLenth:function(length){
        this.currentPage = 1;
        this.tableLength = length;        
        DataTable.getData();
    },
    changePageData:function(page){
        if(page === 'inc')
        {
            this.currentPage++;
        }else if(page === 'des')
        {
            this.currentPage--;
        }else{
            this.currentPage = page; 
        }
        DataTable.getData();
    },
    changeSearchData:function(dataSearch){
        this.tableData = dataSearch;
        this.currentPage = 1;        
        DataTable.getData();
    },
    getData:function(){
        this.dataPost = {'search':this.tableData,'length':this.tableLength,'page':this.currentPage };      
        $.ajax({
            type: "POST",
            url: this.ajaxUrl,
            data: this.dataPost,           
            beforeSend: function () {
               $(DataTable.bodyTag).html('<tr role="row"><td colspan="6" style="text-align: center;"> Loading...</td></tr>');
            },
            success: function (data, textStatus, jqXHR) {                
               var result = data['data'];
               var total = data['total'];
               DataTable.generateView(result);
               DataTable.generateInfo(total);
               DataTable.generatePagin(total);
               DataTable.initPaging();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            },
            complete: function (jqXHR, textStatus) {
            },
           
        });       
       
       
    },
    generateView: function(data)
    {
        var arrayLength = data.length;
        var result = '';
        for(var i = 0; i< arrayLength; i ++)
        {
            var productPath = $("#product-list-path").val();
            var editpath = $("#edit-path").val();
            var curPath = $('#url-pharmacy-group-drug').val(); //productPath.replace('id', data[i].id);
            var curEditPath = editpath.replace('id', data[i].id);
            result += '<tr role="row"> <td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="checkboxes" value="1"><span></span></label>';
            result += '</td><td tabindex="0" class="sorting_1">'+data[i].code +'</td><td>'+data[i].name +'</td><td>'+ data[i].num +'</td>';
            result += '<td><a href="'+curPath+'" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-xs btn-icon-right">View Product List <i class="fa fa-file-text-o"></i></a>'
            result += '<a href="'+curEditPath+'" class="btn btn-ahalf-circle text-uppercase green-seagreen btn-xs btn-icon-right">Edit Details <i class="fa fa-edit"></i></a></td></tr>'; 
            
        } 
        if(result == '')
        {
            result = '<tr role="row"><td colspan="5">Have no record in result  </td> </tr>';
        }
        $(this.bodyTag).html(result);
    },
    generateInfo: function(total){
        var start = (this.currentPage - 1)* this.tableLength+1;
        var end = total;
        if(this.tableLength != -1)
        {
            end = this.currentPage * this.tableLength;
        }
        if(end > total)
        {
            end = total;
        }
        if(total == 0)
        {
            start = 0;
        }
        $(this.infoTag).html("Showing "+start+" to "+end+" of "+total+" entries")
    },
    validate: function() {
        $('#pharmacyForm').validate({
            rules: {
                'pharmacy[pharmacyName]': {
                    required: true
                },
                'pharmacy[businessRegisterNumber]': {
                    required: true
                },
                'pharmacy[gst]': {
                    required: true
                },
                'pharmacy[phone]': {
                    required: true
                },
            },
            errorPlacement: function(error, element) {
                
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    }
});

$(document).ready(function() {
    DataTable.init();
    DataTable.getData();
});