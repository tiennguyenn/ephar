var drugs = {
    id: "",
    field: "",
    value: "",
    init: function() {
        $("#name").bind("change", drugs.search);
        $("#name").on("keyup", function(e){
            var key = e.keyCode || e.which;
            if (key == 13) {
                drugs.search();
            }
        });
        
        $("#limit").on("change", drugs.search);
        
        $("th.sorting").on("click", drugs.search);
        
        $(".pagination > li > a").bind("click", drugs.search);
        
        $("a.reset").bind("click", drugs.reset);
        
        $('.editableInline').editable({
          type: 'text',
          mode: 'inline',
          success: function(response, newValue) {
            drugs.confirmPrice(this, newValue);
          }
        });
        
        $('[data-toggle="tooltip"]').tooltip(); 
        
        $("a#logs").bind("click", drugs.viewLogs);
        
        var date = new Date();
        $('.date-pickers').datepicker({
            orientation: "auto",
            autoclose: true,
            format: 'd M yy',
            startDate: date
        });

        $('#bulkUpdateBtn').click(function() {
          $('#medicineListFile').val('');
          $('#takeEffectDate').val('');
        });
    },
    confirmPrice: function(el, newValue) {
        var id = $(el).attr("data-id");
        var name = $(el).attr("data-name");
        var field = $(el).attr("data-field");
        var value = $(el).attr("data-value");
        var costPrice = $(el).attr('data-origin');
        var tag = '<a href="javascript:;" class="editableInline mr-10 editable editable-click" data-action="update_price" data-id="' + id + '" data-field="' + field + '" data-name="' + name + '" data-value="' + value + '" data-origin="'+costPrice+'">' + value + '</a>';
        var parent = $(el).parent();
        $(el).remove();
        $(parent).prepend(tag);
        $(parent).find("a.editableInline").editable({
          type: 'text',
          mode: 'inline',
          success: function(response, newValue) {
            drugs.confirmPrice(this, newValue);
          }
        });
        
        $(".alert").remove();
        var regex = /^\d+(\,\d{3})*(\.\d+)?$/;
        if (regex.test(newValue)) {
            if(parseFloat(costPrice) > parseFloat(newValue) ){
              $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> Sorry, the selling price you entered is lower than the cost to clinic. Please make sure the selling price is above the cost to clinic.</div>');
              $('html,body').animate({scrollTop: 0}, 'slow');
            }else{
              drugs.value = newValue.replace(/\,/gi, '');
              drugs.id = id;
              drugs.field = field;
              $("#old-price").html(value);
              $("#new-price").html(number_format(newValue, 2, '.', ','));
              $("#price-date").val("");
              if (field == 'list_price_international') {
                  $("#price-title").html(name + " GMEDS Overseas Selling Price");
              } else {
                 $("#price-title").html(name + " GMEDS Local Selling Price"); 
              }
              if (field == 'list_price_international') {
                $("#price-description").html("This will update the overseas selling price of this drug.");
              } else {
                $("#price-description").html("This will update the loal selling price of this drug.");
              }

              $('#modal-GMEDS').modal({
                  backdrop: "static"
              }).on('hidden.bs.modal', function (e) {
                  $('.editableInline').removeClass('mr-10');
              });
            }
        } else {
            $('.editableInline').removeClass('mr-10');
            $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> Invalid value [' + newValue + '].</div>');
            $('html,body').animate({scrollTop: 0}, 'slow');
        }
    },
    changePrice: function() {
        var url = $("#update_url").val();
        var date = $("#price-date").val();
        if (date == '') {
            $("#price-date").parent().css({"border": "1px solid #e73d4a"});
            $("#price-form").find("p.error").html('<span style="color: #e73d4a">Please choose an effect date.</span>');
        } else {
            $("#price-date").parent().css({"border": "0"});
            $("#price-form").find("p.error").html('');
            $('#modal-GMEDS').modal("hide");
            $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
            $.post(url, {"action": "update_price", "id": drugs.id, "field": drugs.field, "value": drugs.value, "date": date}, function (response) {
                $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
                $(".alert").remove();
                if (response.status == 200) {
                    $("#wrapper").before('<div class="alert alert-success"><strong>Success!</strong> ' + response.message + '</div>');
                    $("#" + drugs.field + "_" + drugs.id).find("a").removeClass("mr-10");
                    $("#" + drugs.field + "_" + drugs.id).find("a.icon-tooltip").remove();
                    $("#" + drugs.field + "_" + drugs.id).html(response.info);
                    $("#" + drugs.field + "_" + drugs.id).find('a.editableInline').editable({
                      type: 'text',
                      mode: 'inline',
                      success: function(response, newValue) {
                        drugs.confirmPrice(this, newValue);
                      }
                    });
                    $("#" + drugs.field + "_" + drugs.id).find("a.icon-tooltip").tooltip();
                    $("#" + drugs.field + "_" + drugs.id).find('.reset').bind('click', drugs.reset);
                } else {
                    $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> ' + response.error + '</div>');
                }
                $('html,body').animate({scrollTop: 0}, 'slow');
            }, 'json');
        }
    },
    reset: function() {
        var url = $("#update_url").val();
        drugs.id = $(this).attr("data-id");
        drugs.field = $(this).attr("data-field");
        $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
        $.post(url, {"action": "reset_price", "id": drugs.id, "field": drugs.field}, function(response) {
            $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
            $(".alert").remove();
            if (response.status == 200) {
               $("#wrapper").before('<div class="alert alert-success"><strong>Success!</strong> ' + response.message + '</div>');
                $("#" + drugs.field + "_" + drugs.id).html(response.info);
                $("#" + drugs.field + "_" + drugs.id).find('a').editable({
                  type: 'text',
                  mode: 'inline',
                  success: function(response, newValue) {
                    drugs.confirmPrice(this, newValue);
                  }
                });
            } else {
               $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> ' + response.error + '</div>');
            }

            $('html,body').animate({scrollTop: 0}, 'slow');
        }, 'json');
    },
    search: function() {
       var url = $("#list_url").val();
       var params = {};
       params.name = $("#name").val();
       if (this.tagName == 'A') {
           var parent = $(this).parent();
           
           if ($(parent).prop("tagName") == 'DIV') {
               $("#name").val("");
               params.name = '';
               params.status = $(this).attr("data-value");
               params.sort = "";
               params.dir = "";  
               params.limit = $("#limit").val();
               params.page = 1;
           } else if ($(parent).prop("tagName") == 'LI') {
               params = $.parseJSON($(this).attr("data-params"));
           }
       } else if (this.tagName == 'SELECT') {
           params.status = $("a.status.active").attr("data-value");
           params.sort = "";
           params.dir = "";    
           params.limit = $(this).val();
           params.page = 1;
       } else if (this.tagName == 'TH') {
           params.status = $("a.status.active").attr("data-value");
           params.sort = $(this).attr("data-sort");
           params.dir = $(this).attr("data-dir");  
           params.dir = params.dir == 'asc' ? 'desc' : 'asc';
           params.limit = $("#limit").val(); 
           params.page = 1;           
       } else {
           params.status = $("a.status.active").attr("data-value");
           params.sort = "";
           params.dir = "";  
           params.limit = $("#limit").val(); 
           params.page = 1;    
       }

       $("#wrapper").find("tbody").html('<tr role="row"><td colspan="6" style="text-align: center;"> Loading...</td></tr>');
       $.get(url, params, function(response) {
           $("#wrapper").html(response);
           drugs.init();
       });
    },
    viewLogs: function() {
        var url = $("#logs_url").val();
        $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
        $.post(url, {}, function(response) {
           $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
            $("#list-logs").html(response);
            $('#modal-view-logs').modal();  
        });
    },
    bulkUpdate: function () {
      $('span.error').remove();
      $(".alert").remove();
      var imgName = $('#medicineListFile').val();
      if(!imgName) {
        $('#medicineListFile').after('<span class="error">File upload is invalid!</span>');
        return false;
      }

      var ext = imgName.substr((imgName.lastIndexOf('.') + 1));
      if (ext != 'xlsx') {
        $('#medicineListFile').after('<span class="error">File upload is invalid!</span>');
        return false;
      }
      var excelFile = $('#medicineListFile')[0].files[0];

      var takeEffectDate = $('#takeEffectDate').val();
      if (!takeEffectDate) {
        return false;
      };

      var formData = new FormData();
      formData.append('excelFile', excelFile);
      formData.append('takeEffectDate', takeEffectDate);

      $.ajax({
        type: "POST",
        url: $('#bulk_update_url').val(),
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          $('#modal-import').modal('hide');
          if (response.status == 200) {
            $("#wrapper").before('<div class="alert alert-success"><strong>Success!</strong> ' + response.message + '</div>');
            drugs.search();
          } else {
            $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> ' + response.message + '</div>');
          }
        }
      });
    }
};

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

$(document).ready(function() {
   drugs.init(); 
   $("#price-yes").bind("click", drugs.changePrice);
   $('#bulkUpdate').bind('click', drugs.bulkUpdate);
});