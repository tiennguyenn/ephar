
var Menu = {
    curentRoute: $("#current-route-data").val(),
    data: {},

    initStatus: function () {
        this.activeRoute(this.curentRoute);
        if ($('.page-sidebar-menu').find('li  .active').size() == 0) {
            var route = this.getActiveRouteDefault();
            this.activeRoute(route);
        }
		$("#doctor-terms-service .btn").on('click', function(e) {
			e.preventDefault();
			$("#doctor-terms-service").modal("hide");
		});

		$("#doctor-subcriber-agreement").on('click', function(e) {
			e.preventDefault();

            $.ajax({
                url: $('#doctor-agreement-get-url').val(),
                type: "GET",
                data: {is_plain: true},
                success: function (response) {
                    if (response.success) {
                        $('#doctor-agreement-container').html(response.data);

                        $('head').append('<style id="printarea">@media print{.modal-scrollable,.page-container{display:none}.printarea{display:block}}</style>');

                        $("#doctor-terms-service").modal("show");

                        $('body').bind('cut copy paste', function (e) {
                            alert("You may not copy this document.");
                            e.preventDefault();
                        });

                        $("body").on("contextmenu",function(e){
                            alert("You may not copy this document.");
                            return false;
                        });
                    } else {
                        $('#doctor-agreement-container').html("Get doctor's subscriber agreement failed!");
                    }
                }
            });
		});

		$('#doctor-subscriber-agreement-close').on('click', function () {
            $('body').unbind('cut copy paste');
            $("body").off("contextmenu");
            $('#printarea').remove();
        });
    },
    activeRoute: function (route) {
        
        $.each($('.page-sidebar-menu').find('li a'), function (index, element) {
            if (route == $(element).attr('href')) {
          
                var curLi = $(element).parent();
                curLi.find('.arrow').first().addClass('open');
                if (curLi.parent().hasClass('sub-menu')) {
                    curLi.addClass('active open');
                    var subMenu = curLi.parent().parent();
                    subMenu.addClass('active open');
                    subMenu.find('.arrow').first().addClass('open');
                    subMenuParent = subMenu.parent();
                    if (subMenuParent.length > 0) {
                        subMenuParent.show();
                        subMenuParent.parent().addClass('open');
                    }
                } else {
                    curLi.addClass('active open');
                }
            }
        });
    },
    getActiveRouteDefault: function () {
        if(typeof this.curentRoute != "undefined" ){
            var routeArray = this.curentRoute.split('/');
            var length = routeArray.length;
            var activeRoute = [];
            var check = false;
            for (var i = 0; i < length; i++) {
                activeRoute.push(routeArray[i]);            
                if (check) {
                    break;
                }
                if (routeArray[i] == 'admin') {
                    check = true;
                }
            }
            return activeRoute.join('/');
        }
        return '';
    }
};

$(document).ready(function () {
    Menu.initStatus();
    jsCommon.checkSingleSession();
});

