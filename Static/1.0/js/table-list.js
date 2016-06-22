/**
 * Created with JetBrains PhpStorm.
 * User: Administrator
 * Date: 16-6-17
 * Time: 下午4:03
 * To change this template use File | Settings | File Templates.
 */
//商品列表修改价格
var ylnr;
var cgval
$(".revise").live("click",function(){
    $(this).html('确定');
    $(this).siblings("label").hide();
    $(this).parent().css({"marginLeft":"18px"});
    $(this).siblings("a").html('取消');
    $(this).siblings("a").addClass("cir qx-btn qx");
    $(this).addClass("cir sear-btn srue");
    $(this).removeClass("revise");
    $(this).siblings("a").removeClass("abo");
    ylnr=$.trim($(this).parent().parent().prev("td").find("span").text());
    $(this).parent().parent().prev("td").html('<input type="text" class="cg-pir" value=""/>')
    $(".cg-pir").val(ylnr);

//    $(".cg-pir").blur(function(){
//        cgval=$(this).val()
//    })

})

$(".srue").live("click",function(){
    cgval=$(".cg-pir").val();
    $(this).parent().parent().prev("td").html('<span>'+cgval+'</span>');
    $(this).html('修改价格');
    $(this).addClass("revise");
    $(this).siblings("label").show();
    $(this).removeClass("cir sear-btn srue");
    $(this).siblings("a").html('取消报价');
    $(this).siblings("a").removeClass("cir qx-btn qx");
    $(this).parent().css({"marginLeft":"-10px"});
})
$(".qx").live("click",function(){
    $(this).parent().parent().prev("td").html('<span>'+ylnr+'</span>');
    $(this).html('取消报价');
    $(this).addClass("abo");
    $(this).siblings("label").show();
    $(this).removeClass("cir qx-btn qx");
    $(this).siblings("a").html('修改价格');
    $(this).siblings("a").addClass("revise");
    $(this).siblings("a").removeClass("cir sear-btn srue");
    $(this).parent().css({"marginLeft":"-10px"});
    return false;
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


