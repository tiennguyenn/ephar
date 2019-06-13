var drugs = {
    id: "",
    field: "",
    value: "",
    init: function() {
        var sugParams = {};
        if($('#group_id').length > 0) {
          sugParams = { gid: $('#group_id').val() };
        }
        jsCommon.suggestion($("#name_url").val(), sugParams, 'name');
        
        $("#name").bind("change", drugs.search);
        $("#name").on("keyup", function(e){
            var key = e.keyCode || e.which;
            if (key == 13) {
                drugs.search();
            }
        });
        
        $("a.status").on("click", drugs.search);
        
        $("#limit").on("change", drugs.search);
        
        $("th.sorting").on("click", drugs.search);
        
        $(".pagination > li > a").bind("click", drugs.search);
        
        $("a.approve").bind("click", drugs.approve);
        
        $("a.reject").bind("click", drugs.reject);

        $('.editablePopup').editable({
          type: 'text',
          success: function(response, newValue) {
              drugs.confirmPrices(this, newValue);
          }
        });
        
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
        date.setDate(date.getDate() + 1);
        $('.date-pickers').datepicker({
            orientation: "auto",
            autoclose: true,
            format: 'd M yy',
            startDate: date
        });
    },
    confirmPrices: function (el, newValue) {
        var field = $(el).attr("data-field");
        var value = $(el).attr("data-value");
        var tag = '<a href="javascript:;" class="editablePopup mr-10 editable editable-click" data-action="update_prices" data-field="' + field + '" data-value="' + value + '" data-original-title="Change Percentage">' + value + '%</a>';
        var parent = $(el).parent();
        $(el).remove();
        $(parent).prepend(tag);
        $(parent).find("a.editablePopup").editable({
          type: 'text',
          success: function(response, newValue) {
              drugs.confirmPrices(this, newValue);
          }
        });
        
        $(".alert").remove();
        var regex = /^\d+(\.\d+)?$/;
        if (regex.test(newValue)) {
            drugs.value = newValue;
            drugs.field = $(el).attr("data-field");
            $("#old-prices").html(value + "%");
            $("#new-prices").html(newValue + "%");
            $("#prices-date").val("");
            if (field == 'list_price_international') {
                $("#prices-title").html("GMEDS Overseas Selling Price");
                $("#prices-description").html("This will recalculate overseas selling prices for all medications, including prices of medications that were updated individually. This action cannot be undone.");
            } else {
               $("#prices-title").html("GMEDS Local Selling Price"); 
               $("#prices-description").html("This will recalculate local selling prices for all medications, including prices of medications that were updated individually. This action cannot be undone.");
            }
            
            $('#modal-product-GMEDS').modal({
                backdrop: "static"
            });  
        } else {
            $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> Invalid percent value.</div>');
            $('html,body').animate({scrollTop: 0}, 'slow');
        }
    },
    changePrices: function() {
        var url = $("#update_url").val();
        var date = $("#prices-date").val();
        if (date == '') {
            $("#prices-date").parent().css({"border": "1px solid #e73d4a"});
            $("#prices-form").find("p.error").html('<span style="color: #e73d4a">Please choose an effect date.</span>');
        } else {
            $("#prices-date").parent().css({"border": "0"});
            $("#prices-form").find("p.error").html('');
            $('#modal-product-GMEDS').modal("hide"); 
            $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
            $.post(url, {"action": "update_prices", "field": drugs.field, "value": drugs.value, "date": date}, function (response) {
                $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
                $(".alert").remove();
                if (response.status == 200) {
                    $("#wrapper").before('<div class="alert alert-success"><strong>Success!</strong> ' + response.message + '</div>');
                    $("#" + drugs.field).find("a").removeClass("mr-10");
                    $("#" + drugs.field).find("a.icon-tooltip").remove();
                    $("#" + drugs.field).find("a").after(response.info);
                    $("#" + drugs.field).find("a.icon-tooltip").tooltip(); 
                } else {
                    $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> ' + response.error + '</div>');
                }

                $('html,body').animate({scrollTop: 0}, 'slow');
            }, 'json');  
        }
    },
    confirmPrice: function(el, newValue) {
        var id = $(el).attr("data-id");
        var name = $(el).attr("data-name");
        var field = $(el).attr("data-field");
        var value = $(el).attr("data-value");
        var costPrice = $('#cost_price_'+id).data('val');
        var tag = '<a href="javascript:;" class="editableInline mr-10 editable editable-click" data-action="update_price" data-id="' + id + '" data-field="' + field + '" data-name="' + name + '" data-value="' + value + '">' + value + '</a>';
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
              $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> Sorry, the selling price you entered is lower than the cost price. Please make sure the selling price is above the cost price.</div>');
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
              $("#price-description").html("This will change the cost price of " + name + ".");
              
              $('#modal-GMEDS').modal({
                  backdrop: "static"
              });  
            }
        } else {
            $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> Invalid percent value [' + newValue + '].</div>');
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
                    $("#" + drugs.field + "_" + drugs.id).append(response.info);
                    $("#" + drugs.field + "_" + drugs.id).find("a.icon-tooltip").tooltip();
                } else {
                    $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> ' + response.error + '</div>');
                }
                $('html,body').animate({scrollTop: 0}, 'slow');
            }, 'json');
        }
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
    approve: function() {
        var self = this;
        var url = $("#update_url").val();
        var id = $(self).attr("data-id");
        var date = $(self).closest(".price-changing").find("input[type=text]").val().trim();
        $(".alert").remove();
        $(self).closest(".price-changing").find("input[type=text]").css("border", "1px solid #c2cad8");
        if (date == '') {
            $(self).closest(".price-changing").find("input[type=text]").css("border", "1px solid red");
            $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> Please choose a date.</div>');
            $('html,body').animate({scrollTop: 0}, 'slow');
        } else {
            $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
            $.post(url, {"action": "approve_price", "id": id, "date": date}, function(response) {
               $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
               if (response.status == 200) {
                   var status = $(".filter-changing a.active").attr("data-value");
                   if (status == 'pending') {
                       $("#wrapper").html(response.data);
                       drugs.init();
                   } else {
                       var tr = $(self).closest("tr");
                       var prev = $(tr).prev();
                       $(tr).remove();
                       $(prev).find("span.value span").remove();
                       $(prev).find("div.action").remove();
                       var pending = $("a.status span.badge").html();
                       pending = pending != '' ? (parseInt(pending) < 2 ? '' : parseInt(pending) - 1) : '';
                       $("a.status span.badge").html(pending);
                       $("#cost_price_" + id).append(response.info);
                       $("#cost_price_" + id).find("a.icon-tooltip").tooltip();
                       $("#wrapper").before('<div class="alert alert-success"><strong>Success!</strong> Approved successfully.</div>');
                   }
               } else {
                   $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> ' + response.error + '</div>');
                   $(self).closest(".price-changing").find("input[type=text]").css("border", "1px solid red");
               }
               $('html,body').animate({scrollTop: 0}, 'slow');
            }, 'json'); 
        }
    }, 
    reject: function() {
        var self = this;
        var url = $("#update_url").val();
        var id = $(this).attr("data-id");
        var field = $(this).attr("data-field");
        $(".alert").remove();
        $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
        $.post(url, {"action": "reject_price", "id": id}, function(response) {
           $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
           if (response.status == 200) {
               var status = $(".filter-changing a.active").attr("data-value");
               if (status == 'pending') {
                   $("#wrapper").html(response.data);
                   drugs.init();
               } else {
                   var tr = $(self).closest("tr");
                   var prev = $(tr).prev();
                   $(tr).remove();
                   $(prev).find("span.value span").remove();
                   $(prev).find("div.action").remove();
                   var pending = $("a.status span.badge").html();
                   pending = pending != '' ? (parseInt(pending) < 2 ? '' : parseInt(pending) - 1) : '';
                   $("a.status span.badge").html(pending);
                   $("#wrapper").before('<div class="alert alert-success"><strong>Success!</strong> Rejected successfully.</div>');
               }
           } else {
               $("#wrapper").before('<div class="alert alert-danger"><strong>Alert!</strong> ' + response.error + '</div>');
           }

           $('html,body').animate({scrollTop: 0}, 'slow');
        }, 'json');
    },
    viewLogs: function() {
        var url = $("#logs_url").val();
        $(".blink-loader").css({"opacity": 0.7, "visibility": "visible"});
        $.post(url, {}, function(response) {
           $(".blink-loader").css({"opacity": 0, "visibility": "hidden"});
            $("#list-logs").html(response);
            $('#modal-view-logs').modal();  
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
   $("#prices-yes").bind("click",drugs.changePrices);
});