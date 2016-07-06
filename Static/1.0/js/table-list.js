/**
 * Created with JetBrains PhpStorm.
 * User: Administrator
 * Date: 16-6-17
 * Time: 下午4:03
 * To change this template use File | Settings | File Templates.
 */
//右边导航高度适应屏幕
var stop;
var wH=$(window).height();
var tph=$(".yzc-sell-head").outerHeight(true)
var slfH=wH-tph;
var cgh;
$(window).ready(function(){
    $("#rg-nav").css({"height":slfH});
})
$(window).on("resize",function(){
    $("#rg-nav").css({"height":slfH});
})
$(window).scroll(function(){
    stop=$(document).scrollTop();
    cgh=tph-stop;
    $("#rg-nav").css({"top":cgh,"height":wH-cgh});
    if($(document).scrollTop()>=tph){
        $("#rg-nav").css({"height":wH});
        $("#rg-nav").css({"top":0});
    }
    else{
        $("#rg-nav").css({"height":wH-cgh});
        $("#rg-nav").css({"top":cgh});
    }
})
//右边导航下拉框
$(".sell-pull-list").find("a.pull-btn").toggle(function(){
    $(".xl-box").css({"display":"block"})
    $(this).find("em").css({"transform":"rotate(180deg)"})
},function(){
    $(".xl-box").css({"display":"none"})
    $(this).find("em").css({"transform":"rotate(0deg)"})
})
//头部进入一只蝉
$("#yzc-port").hover(function(){
    $(".yzc-entr-list").fadeIn("fast");
},function(){
    $(".yzc-entr-list").fadeOut("2000");
})

//下拉列表
var cbtn=$(".all-tp-btn");
var plist= $(".all-tp-list");
cbtn.attr("index","1");
var dinx=cbtn.attr("index");
cbtn.each(function(){
    $(this).click(function(){
        var dinx= $(this).attr("index");
        if(dinx==1){
            $(this).siblings("ul").slideDown();
            $(this).attr("index","2");

            $(this).find("i").css({"transform":"rotate(180deg)"})

        }
        else if(dinx==2){
            $(this).siblings("ul.all-tp-list").slideUp();
            $(this).attr("index","1");

            $(this).find("i").css({"transform":"rotate(0deg)"})
        }
    })
    $(this).siblings("ul.all-tp-list").find("li").each(function(){
        $(this).click(function(){
            $(this).parent().slideUp();
            $(this).parent().siblings("a.all-tp-btn").find("font").text($(this).text());
            $(this).parent().siblings("a.all-tp-btn").attr("index","1");
            $(this).parent().siblings("a.all-tp-btn").find("i").css({"transform":"rotate(0deg)"})
        })
    })
})
//下拉列表点空白的时候消失
$(".pull-list").bind('click',function(e){
    stopPropagation(e);
});
$(document).bind('click',function(){
    $(".all-tp-list").slideUp();
    $(".all-tp-btn").attr("index","1");
    $(".all-tp-btn").find("i").css({"transform":"rotate(0deg)"});
});
function stopPropagation(e) {
    if (e.stopPropagation)
        e.stopPropagation();
    else
        e.cancelBubble = true;
}

//table偶数行变色
var otrs= $(".list-mess").find("tr");
for(var i=0;i<=otrs.length;i++){
    if(i%2==1)
    {
        $(otrs[i]).css({"backgroundColor":"#f5f5f5"})
    }
    else{
        $(otrs[i]).css({"backgroundColor":"#ffffff"})
    }
}
//切换公用函数
function jc(name,curr,n)
{
    for(i=1;i<=n;i++)
    {
        var menu=document.getElementById(name+i);
        var cont=document.getElementById("con_"+name+"_"+i);
        menu.className=i==curr ? "on" : "";
        if(i==curr){
            cont.style.display="block";
        }else{
            cont.style.display="none";
        }
    }
}


$("input").focus(function(){
    $(this).css({"borderColor":"#d5d5d5"})
})

//placeholder兼容IE//placeholder兼容IE
//判断浏览器是否支持placeholder属性
supportPlaceholder = 'placeholder' in document.createElement('input'),
    placeholder = function (input) {
        var text = input.attr('placeholder'),
            defaultValue = input.defaultValue;
        var _val = $.trim(input.val());
        if (!defaultValue && _val == '') {
            input.val(text).addClass("phcolor");
        }
        input.focus(function () {
            if (input.val() == text) {
                $(this).val("");
            }
        });
        input.blur(function () {
            if (input.val() == "") {
                $(this).val(text).addClass("phcolor");
            }
        });
//输入的字符不为灰色
        input.keydown(function () {
            $(this).removeClass("phcolor");
        });
    };
//当浏览器不支持placeholder属性时，调用placeholder函数
if (!supportPlaceholder) {
    $('input').each(function () {
        text = $(this).attr("placeholder");
        if ($(this).attr("type") == "text") {
            placeholder($(this));
        }
    });
}

//placeholder兼容IE
function isPlaceholder() {
    var input = document.createElement('input');
    return 'placeholder' in input;
}

if (!isPlaceholder()) {//不支持placeholder 用jquery来完成
    $(document).ready(function () {
        if (!isPlaceholder()) {
            $("input").not("input[type='password']").each(//把input绑定事件 排除password框
                function () {
                    if ($(this).val() == "" && $(this).attr("placeholder") != "") {
                        $(this).addClass("phcolor").val($(this).attr("placeholder"));
                        $(this).focus(function () {
                            if ($(this).val() == $(this).attr("placeholder")) {
                                $(this).val("");
                            }
                        });
                        $(this).blur(function () {
                            if ($(this).val() == "") {
                                $(this).val($(this).attr("placeholder")).addClass("phcolor");
                            }
                        });
                        $(this).keydown(function () {
                            $(this).removeClass("phcolor");
                        });
                    }
                });
            //对password框的特殊处理1.创建一个text框 2获取焦点和失去焦点的时候切换
            var pwdField = $("input[type=password]");
            var pwdVal = pwdField.attr('placeholder');
            pwdField.after('<input id="pwdPlaceholder" class="cir" type="text" class="phcolor" value=' + pwdVal + ' autocomplete="off" />');
            var pwdPlaceholder = $('#pwdPlaceholder');
            pwdPlaceholder.show();
            pwdField.hide();

            pwdPlaceholder.focus(function () {
                pwdPlaceholder.hide();
                pwdField.show();
                pwdField.focus();
            });

            pwdField.blur(function () {
                if (pwdField.val() == '') {
                    pwdPlaceholder.show();
                    pwdField.hide();
                }
            });
        }
    });
}
