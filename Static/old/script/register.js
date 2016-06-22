$(document).ready(function(e) {
	//登录
    $('#reg').click(function(){
		ueaVal	= $.trim($('#username').val());
		uepVal	= $.trim($('#upass').val());
		if( ueaVal == '' ){
			error1('请输入邮箱或者手机号码','eUserTips');
			return false;
		}
		uType	= getUserType(ueaVal);
		if(uType == 0){
			error1('请输入正确邮箱或者手机号码','eUserTips');
			return false;
		}
		success1('eUserTips');
		if( uepVal == '' ){
			error1('请输入密码','ePassTips');
			return false;
		}else{
			pMsg = verifyPwd(uepVal);
			if( pMsg ){
				error1(pMsg,'ePassTips');
				return false;
			}
		}
		success1('ePassTips');
		istongyi = $('.tongyi').attr('checked');
		if( !istongyi ){
			alert('请勾选我已阅读并同意');
			return false;
		}
		userReg(ueaVal,uepVal,uType);
	});
	//显示或隐藏密码
	$('.g-nav_input_a').click(function(){
		paType = $('#upass').attr('type');
		if(paType=='password'){
			$(this).html('隐藏密码');
			passType = 'text';
			//$('#upass')[0].type = "text";
		}else{
			$(this).html('显示密码');
			passType = 'password';
			//$('#upass')[0].type = "password";
		}
		passString = $('#upass').val();
		$('#passwordstr').html('<input type="'+passType+'" placeholder="请输入密码（6-20位数字，字母或符号）" id="upass" value="'+passString+'"/>');
	});
});

//错误提示1
function error1(msg,classid){
	$('.'+classid).find('img').attr('src','/Static/style/slice/mj-icon14.png');
	$('.'+classid).find('span').html(msg);
	$('.'+classid).show();
}
//成功提示1
function success1(classid){
	$('.'+classid).find('img').attr('src','/Static/style/slice/mj-icon13.png');
	$('.'+classid).find('span').hide();
}
//验证密码
function verifyPass(string){
	if( string.length < 6 ){
		msg = '密码不能小于6位数';
	}else if( string.length > 20 ){
		msg = '密码不能大于20位数';
	}else{
		msg = '';
	}
	return msg;
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
//验证手机
function verifyMobile(){
	umobVal	= $.trim($('#umobile').val());
	ucodeVal= $.trim($('#ucode').val());
	if( umobVal == '' ){
		error2('请输入手机号码');
		return false;
	}
	ismob	= isMobile(umobVal);
	if( ismob == false ){
		error2('手机号码格式错误');
		return false;
	}
	success2();
	return true;
}

//注册
function userReg(account,pword,utype){
	$.ajax({
			 type		: "post",
			 url		: "/register/regUser/",
			 dataType	: "json",
			 data		: {"account" : account,"password" : pword,"cateId" : utype},
			 error		: function(msg){
				
			 },
			 success	: function(data){
				if(data){
					if(data.code==1){
						window.location = '/user/main/';
					}else{
						alert(data.msg);	
					}
				}
			 }
	});
}
