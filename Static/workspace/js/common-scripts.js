$(document).ready(function(){
	// $(".progressbar").each(function(){
	// 	$(this).children("ul").children("li").last().css({"background":"url(./img/progressbar-foot.png) left 50px no-repeat"},{"width":"80px"},{"padding":"80px 0 0 0"},{"textAlign":"right"});
	// });
	//$(".progressbar ul li:last").css({"background":"url(./img/progressbar-foot.png) left 50px no-repeat"},{"minWidth":"80px"},{"padding":"80px 0 0 0"},{"textAlign":"right"});
	$(".counselors").hover(function(){
		$(this).addClass("open");
	},function(){
		$(this).removeClass("open");
	});
    $(".progressbar,.progressbar2").each(function(){
        var width = $(this).width();
        var length = $(this).children("ul").children("li").length;
        $(this).children("ul").children("li").width(width/length);
        // $(this).children("ul").children("li").first().width(100);
    });
});



var Script = function () {

//    sidebar dropdown menu

    jQuery('#sidebar .sub-menu > a').click(function () {
        var last = jQuery('.sub-menu.open', $('#sidebar'));
        last.removeClass("open");
        jQuery('.arrow', last).removeClass("open");
        jQuery('.sub', last).slideUp(200);
        var sub = jQuery(this).next();
        if (sub.is(":visible")) {
            jQuery('.arrow', jQuery(this)).removeClass("open");
            jQuery(this).parent().removeClass("open");
            sub.slideUp(200);
        } else {
            jQuery('.arrow', jQuery(this)).addClass("open");
            jQuery(this).parent().addClass("open");
            sub.slideDown(200);
        }
        var o = ($(this).offset());
        diff = 200 - o.top;
        if(diff>0)
            $("#sidebar").scrollTo("-="+Math.abs(diff),500);
        else
            $("#sidebar").scrollTo("+="+Math.abs(diff),500);
    });

//    sidebar toggle


    $(function() {
        function responsiveView() {
            var wSize = $(window).width();
            if (wSize <= 768) {
                $('#container').addClass('sidebar-close');
                $('#sidebar > ul').hide();
            }

            if (wSize > 768) {
                $('#container').removeClass('sidebar-close');
                $('#sidebar > ul').show();
            }
        }
        $(window).on('load', responsiveView);
        $(window).on('resize', responsiveView);
    });

    $('.icon-reorder').click(function () {
        if ($('#sidebar > ul').is(":visible") === true) {
            $('#main-content').css({
                'margin-left': '0px'
            });
            $('#sidebar').css({
                'margin-left': '-180px'
            });
            $('#sidebar > ul').hide();
            $("#container").addClass("sidebar-closed");
        } else {
            $('#main-content').css({
                'margin-left': '180px'
            });
            $('#sidebar > ul').show();
            $('#sidebar').css({
                'margin-left': '0'
            });
            $("#container").removeClass("sidebar-closed");
        }
    });

// custom scrollbar
    //$('#sidebar').niceScroll({styler:"fb",cursorcolor:"#e8403f", cursorwidth: '3', cursorborderradius: '10px', background: '#404040', cursorborder: ''});

    //$('html').niceScroll({styler:"fb",cursorcolor:"#e8403f", cursorwidth: '6', cursorborderradius: '10px', background: '#404040', cursorborder: '', zindex: '1000'});

// widget tools

    jQuery('.widget .tools .icon-chevron-down').click(function () {
        var el = jQuery(this).parents(".widget").children(".widget-body");
        if (jQuery(this).hasClass("icon-chevron-down")) {
            jQuery(this).removeClass("icon-chevron-down").addClass("icon-chevron-up");
            el.slideUp(200);
        } else {
            jQuery(this).removeClass("icon-chevron-up").addClass("icon-chevron-down");
            el.slideDown(200);
        }
    });

    jQuery('.widget .tools .icon-remove').click(function () {
        jQuery(this).parents(".widget").parent().remove();
    });

//    tool tips

    $('.tooltips').tooltip();

//    popovers

    $('.popovers').popover();



// custom bar chart

    if ($(".custom-bar-chart")) {
        $(".bar").each(function () {
            var i = $(this).find(".value").html();
            $(this).find(".value").html("");
            $(this).find(".value").animate({
                height: i
            }, 2000)
        })
    }


//custom select box

//    $(function(){
//
//        $('select.styled').customSelect();
//
//    });
	// $('.qg_tdp p:gt(2)').hide();
	// $('.qg_tdover p:gt(2)').hide();
	// $('.qg_sq').hide();
	// $('.qg_all').click(function(){
	// 	$('.qg_tdp p,.qg_tdover p,.qg_sq').show();
	// 	$('.qg_all').hide();
		
	// });
	// $('.qg_sq').click(function(){
	// 	$('.qg_tdp p:gt(2),.qg_tdover p:gt(2),.qg_sq').hide();
	// 	$('.qg_all').show();
		
	// });
	
	// $('.qg_div2 tr:lt(3)').hide();
	// $('.qg_div2 .qg_sq').hide();
	// $('.qg_div2 .qg_all').click(function(){
	// 	$('.qg_div2 tr:gt(0)').show();
	// 	$('.qg_div2 .qg_all').hide();
	// });
	// $('.qg_div2 .qg_sq').click(function(){
	// 	$('.qg_div2 tr:lt(3)').show();
	// 	$('.qg_div2 tr:gt(2)').hide();
	// 	$('.qg_div2 .qg_all').show();
		
	// });
    var ulheight = $(".qg_cont ul").height();
    if(ulheight < 35){
        $(".qg_hide").hide();
        $(".qg_show").hide();
    }
    $(".qg_show").click(function() {
            $(this).hide();
            $(".qg_cont").css('height', 'auto');
            $(".qg_hide").show();
    });
    $(".qg_hide").click(function(event) {
            $(this).hide();
            $(".qg_cont").css('height', '41px');
            $(".qg_show").show();
    })

}()