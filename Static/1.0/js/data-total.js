/*---cook操作---*/
function addCookie(objName,objValue,objHours){
    var str = objName + "=" + escape(objValue);
    if(objHours > 0){//爲時不設定過期時間，浏覽器關閉時cookie自動消失
        var date 	= new Date();
        var ms 		= objHours*3600*1000;
        date.setTime(date.getTime() + ms);
        str += "; expires=" + date.toGMTString();
    }
    var cook=str+";path=/";
    document.cookie = cook;
}
function getCookie(name){
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
    if(arr != null) return unescape(arr[2]); return null;
}
function delCookie(name){
    var date = new Date();
    date.setTime(date.getTime() - 10000);
    document.cookie = name + "=a; expires=" + date.toGMTString()+";path=/";
}
//piwik设置事件
function setEvent(module,name,page){
    if(typeof page == 'undefined'){
        page = ptype;
    }
    page = analyzePage(page);
    _paq.push(['trackEvent', page, module, name]);
}
//绑定超链接事件
$(document).on('click','a',function(e){
    var module = $(this).attr('module');
    if(module){
        var addmsg = $(this).attr('addmsg');//额外信息
        setEvent(module,addmsg);
    }
});
//解析页面
function analyzePage(ptype){
    var str = '';
    switch (ptype){
        case '1':
            str = '首页';break;
        case '2':
            str = '我的商品';break;
        case '3':
            str = '我要出售';break;
        case '4':
            str = '我的收益';break;
        case '5':
            str = '奖励兑换';break;
        case '6':
            str = '我的消息';break;
	case '6':
            str = '登录首页';break;
        default:
            str = '未知';
    }
    return str;
}