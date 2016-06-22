var isSendCode = true;
$(document).ready(function(e) {
	//回车事件
	$(document).keypress(function(e) { 
		if(e.which == 13) {
			doReg();
		} 
	});
	//登录
	$(document).on('click', "#reg", function(){
		doReg();
	});
	$(document).on('click', "#getcode", function(){
		isreg 	= regVic(0);
		if(isreg == true){
			if( isSendCode == false ){
				return false;	
			}
			ueaVal	= $.trim($('input[name="usertel"]').val());
			means.sendCode(ueaVal);
		}
	});
});
//用户、密码
function regVic(isExist){
	ueaVal	= $.trim($('input[name="usertel"]').val());
	uepVal	= $.trim($('input[name="userpwd"]').val());
	//验证账号
	if( ueaVal == '' ){
		regprompt('请输入手机号码',$('#red1'),0);
		return false;
	}
	uType	= isMobile(ueaVal);
	if(uType == 0){
		regprompt('请输入正确手机号码',$('#red1'),0);
		return false;
	}else{
		if(isExist == 1){
			objExist = means.verifyUser(ueaVal);
			if( objExist.code == 1 ){
				regprompt('账号存在',$('#red1'),0);
				return false;	
			}
		}
	}
	regprompt('',$('#red1'),1);
	//验证密码
	if( uepVal == '' ){
		regprompt('请输入密码',$('#red2'),0);
		return false;
	}else{
		pMsg = verifyPwd(uepVal);
		if( pMsg ){
			regprompt(pMsg,$('#red2'),0);
			return false;
		}
	}
	regprompt('',$('#red2'),1);
	return true;
}

//验证码
function regVicCode(){
	uecVal  = $.trim($('input[name="code"]').val());
	if( uecVal == '' ){
		regprompt('请输入验证码',$('#red3'),0);
		return false;
	}
	regprompt('',$('#red3'),1);
	return true;
}
//提示
function regprompt(msg,obj,type){
	if( type == 0 ){
		$(obj).show().next().hide();
	}else{
		$(obj).hide().next().show();
	}
	obj.html(msg);
}
//注册回调
function userRegCallback(obj){
	if(obj.code==1){
		window.location = '/user/main/';
	}else{
		regprompt(obj.msg,$('#red1'),0);
	}
}
//发送短信回调
function sendCodeCallback(obj){
	if(obj.code==1){
		isSendCode = false;
		regprompt('',$('#red3'),1);
		regCountDown(60,$('#getcode'));
	}else{
		regprompt(obj.msg,$('#red3'),0);
	}
}
function verifyCodeCallback(obj,account,password){
	if(obj.code==1){
		means.userReg(account,password);
	}else{
		$('#reg').attr('disabled',false).text('确认提交');
		regprompt(obj.mess,$('#red3'),0);
	}
}
//注册
function doReg(){
	isreg = regVic();
	if(isreg == true){
		ueaVal	= $.trim($('input[name="usertel"]').val());
		uepVal	= $.trim($('input[name="userpwd"]').val());
		uecVal  = $.trim($('input[name="code"]').val());
		var isExist  = 0;
		iscode  = regVicCode();
		if( iscode == false ){
			return false;	
		}
		$('#reg').attr('disabled',true).text('处理中...');
		means.verifyCode(ueaVal,uecVal,uepVal);
		return false;
	}
}