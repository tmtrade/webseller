/**
 * Created by dower on 2016/7/13 0013.
 */
var p_tel = '';
var p_name = '';
$(function(){
    //输入框获得焦点隐藏提示
    $('.amend-cont-all').on('focus','input',function(){
        $('.inc-ts').hide();
    });
    //编辑联系人
    $('.amend-cont-all').on('click','.xg_mobile',function(){
        edit_mobile(1);
        $(".amend-cont label").css({"marginTop":"6px"})
    });
    $('.amend-cont-all').on('click','.cg-qx',function(){
        edit_mobile(2);
        $(".amend-cont label").css({"marginTop":"0px"})
    });
    $('.amend-cont-all').on('click','.cg-sear',function(){
        edit_mobile(3);
    });
    //验证价格
    $('#list_body').on('blur','.tr_price',function(){
        var price = $(this).val();
        if(checkPrice(price)==false){
            layer.msg('请填写正确的价格', {
                time: 1500
            });
        }
        return false;
    });
    //删除列表框
    $('#list_body').on("click",".del-lis",function(){
        var _this = this;
        layer.confirm('您确定删除该商标吗?',function() {
            $(_this).parent().parent().remove();
            $('#goods_num').text(function (i, v) {
                return v - 1;
            });
            layer.closeAll();
            check_table();
        });
    });
});
//验证是否手机
function isMobile(mobile){
    var mobilereg = /^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d\d\d\d\d\d\d\d$/i;
    return (!mobilereg.test(mobile) || mobile.length!=11);
}
//编辑联系人
function edit_mobile(type){
    var show1 = $('#nicaicai .cc1');
    var hide1 = $('#nicaicai .cc2');
    if(type==1){ //修改
        //保存原值
        p_tel = $('.amend-cont-all .contact_mobile').text();
        p_name = $('.amend-cont-all .contact_name').text();
        //赋值
        hide1.find('.c_tel').attr('value',p_tel);
        hide1.find('.c_name').attr('value',p_name)
        $('.amend-cont').html(hide1.html());
    }else if(type==2){//还原
        show1.find('.contact_mobile').text(p_tel);
        show1.find('.contact_name').text(p_name);
        $('.amend-cont').html(show1.html());
    }else{//确定
        var now_tel = $('.amend-cont-all .cg-cont-tel input').val();
        var now_name = $('.amend-cont-all .cg-cont-name input').val();
        if(isMobile(now_tel)){
            if(isName(now_name)){
                //到服务器去修改信息
                $.post('/sell/changeContact',{'name':now_name},function(data){
                    if(data.code==0){
                        show1.find('.contact_mobile').text(now_tel);
                        show1.find('.contact_name').text(now_name);
                        $('.amend-cont').html(show1.html());
                        $(".amend-cont label").css({"marginTop":"0px"})
                    }else{
                        $('.inc-ts').html(data.msg).show();
                    }
                },'json');
            }else{
                $('.inc-ts').html('请输入正确的联系人').css({"display":"inline-block"});
            }
        }else{
            $('.inc-ts').html('请输入正确的联系电话').css({"display":"inline-block"});
        }
    }
}
//验证联系人
function isName(name){
    if(name.length>30){
        return false;
    }
    var reg = /^([\u4E00-\u9FA5]|[A-Za-z])+$/i;
    return reg.test(name);
}
//验证价格
function checkPrice(price){
    return (/^\d+$/.test(price));
}