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
function addUserCook(key,name,times){
	addCookie('uc_ukey',key,times);
	if(name){
		addCookie('uc_nickname',name,times);
	}
}
//删除用户信息cook
function delteUserCook(){
	delCookie('uc_ukey');
	delCookie('uc_nickname');
}
