//获取配置信息（跨域用）
function getConfigUrl(cUrl){
	//configUrl 	= getCookie('configUrl');
	//configUrl	= unescape(configUrl);
	configUrl	= cUrl;
	return configUrl;
}
//是否数组
function isArray(obj){  
  return Object.prototype.toString.call(obj) === '[object Array]';   
}
//验证是否手机
function isMobile(mobile){
	var mobilereg = /^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d\d\d\d\d\d\d\d$/i;
	if(!mobilereg.test(mobile) || mobile.length!=11){
		return false;	
	}else{
	    return true;	
	}
}
//验证邮箱
function isEmail(email){
	if( email && /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email) == false ){
		return false;
	}
	return true;	
}
//获取账号类型
function getUserType(string){
	if(isMobile(string)){
		return 2;	
	}
	if(isEmail(string)){
		return 1;	
	}
	return 0;
}
//验证class是否存在
function checkUcClass(string){
	num = $('.'.string).length;
}
//是否存在指定函数 
function isExitsFunction(funcName) {
    try {
        if (typeof(eval(funcName)) == "function") {
            return true;
        }
    } catch(e) {}
    return false;
}
function getStCode(){
	exturl	 = '';
	isfun 	 = isExitsFunction('zzGetCookie');
	if(isfun){
		stsid  = zzGetCookie('sid');
		starea = zzGetCookie('area');
		exturl = '&sid='+stsid+'&area='+starea;
	}
	return exturl;
}
function verifyPwd(password){
	if ( password.length < 1 ) {
		return '密码不能为空';
	}
	pattern = /[^\x00-\xff]/;
		if ( pattern.test(password) ) {
		return '请不要输入中文';
	}
    pattern = /^\d+$/;//验证纯数字
	if(pattern.test(password)){
		return '请输入6~20位非纯数字的密码';
	}
	pattern = /^(?!\d{1,8}$)[\S]{6,20}$/;
	//pattern = /[\w?`~!@#$%^&\*\(\)-=\+\{\}\[\]:;"',\.\/\\<>\?\|]{6,20}$/;
	if(!pattern.test(password)){
		return '请输入6~20位不包含空格的密码';
	}else{
		return '';
	}
}

//把需要提交的数据设置成URL连接(弃用)
function setPostUrl(data){
	var url	 = '';
	newArray = data;
	for( var k in newArray ){
		url+='&'+k+'='+newArray[k];
	}
	configUrl = getConfigUrl();

	return url+configUrl;
}
//发送验证码倒计时
var isSendCode = true;
function timer(count, obj, title){
	window.setTimeout (function () {
		count --;
		tyep = obj.attr('input');
		obj.text('已发送'+count + "秒后可重新获取");
		obj.val('已发送'+count + "秒后可重新获取");
		if(count > -1){
			isSendCode = false;
			obj.removeClass('mj_wxcodew').addClass('mj_wxcodef');
			timer(count, obj, title);
		}else{
			obj.text(title);
			obj.val(title);
			isSendCode = true;
			obj.removeClass('mj_wxcodef').addClass('mj_wxcodew');
		}
	},1000);
}
//延迟刷新
function delayRefresh(){
	if( isDelay ){
		time 	= 3000;
		cfClass = '.chaofan-mj-logBtn';
		$(cfClass).css({'background':'#dcdada'});
		$(cfClass).html('登录中...');
		$(cfClass).click(function(){
			return false;
		});
	}else{
		time = 0;
	}
	setTimeout('window.location.reload();',time);	
}
//获取验证提示
function getError(code){
	switch(code){
		case 0:
		  	msgJs = '账号格式不正确';
		  break;
		case 1:
		  	msgJs = '成功';
		  break;  
		case 2:
		  	msgJs = '账号不存在';
		  break;
		case 3:
		  	msgJs = '密码错误';
		  break;
		case 4:
		  	msgJs = '验证码错误';
		  break;  
		default:
			msgJs = '登录失败，请稍后登录';
	}
	return msgJs;	
}
//设置用户信息cook
function addUserCook(key,name,utel,times){
	times = times ? times : validTime;
	addCookie(uckeystr,key,times);
	if(name){
		addCookie(ucnamestr,name,times);
	}
	if(utel){
		addCookie(ucmobile,utel,times);
	}
}
//删除用户信息cook
function delteUserCook(){
	delCookie(uckeystr);
	delCookie(ucnamestr);
	delCookie(ucmobile);
}
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