var downtime  	= 60;
var isSendCode	= true;
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\"/Static/script/user/script/tool.js\"></scr" + "ipt>");
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\"/Static/script/user/script/public.js\"></scr" + "ipt>");

$(document).ready(function(e) {
	//登录
    $('#logver1').click(function(){
		ueaVal	= $.trim($('#uemail').val());
		uepVal	= $.trim($('#upass').val());
		if( ueaVal == '' ){
			error1('请输入邮箱或者手机号码');
			$('.usertxt').html('请输入邮箱或者手机号码');
			return false;
		}
		uType	= getUserType(ueaVal);
		if(uType == 0){
			error1('请输入正确邮箱或者手机号码');
			$('.usertxt').html('请输入正确邮箱或者手机号码');
			return false;
		}
		if( uepVal == '' ){
			error1('请输入密码');
			$('.pwdtxt').html('请输入密码');
			return false;
		}else{
			pMsg = verifyPwd(uepVal);
			if( pMsg ){
				error1(pMsg);
				return false;
			}
		}
		$('.usertxt').html('');
		$('.pwdtxt').html('');
		checkbox = document.getElementById('accord');//
		uAuto	 = checkbox.checked ? 10 : 0;
		ajaxLogin(ueaVal,uepVal,uType,uAuto);
	});
	
	//验证码注册/登录(提交)
    $('#logver2').click(function(){
		isMob = verifyMobile();
		if(isMob){
			ucodeVal = $.trim($('#ucode').val());
			if( ucodeVal == '' ){
				error2('请输入验证码');
				return false;
			}
			ajaxCode();
		}
	});
	//获取验证码
	$(document).on('click', "#getcode", function(){
		isMob = verifyMobile();
		if( isMob ){
			sendCode();	
		}
		return false;
    });
	getDefaultMobile();
});

//错误提示1
function error1(msg){
	$('#eTips').show();
	$('#eTips').find('span').html(msg);
}
//成功提示1
function success1(){
	$('#eTips').hide();
}
//提示2
function error2(msg,type){
	if(type==1){
		$('#mTips').find('img').attr('src','/Static/style/slice/mj-icon13.png');
		$('#mTips').find('span').css({'color' : '#0fce9f'});
	}else{
		$('#mTips').find('img').attr('src','/Static/style/slice/mj-icon14.png');
		$('#mTips').find('span').css({'color' : '#eb5152'});
	}
	$('#mTips').find('span').html(msg);
	$('#mTips').show();
}
//成功提示2
function success2(){
	$('#mTips').hide();
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
//倒计时
function countDown(count, obj){
     window.setTimeout (function () {
		count --;
		obj.text(count + "秒后重新获取");
		if(count > -1){
			obj.css('background-color','#ddd');
			obj.css('color','#333');
			
			countDown(count, obj);
		}else{
			isSendCode = true;
			obj.css('background-color','#deffd4');
			obj.css('color','#4e9b37'); 
			obj.text('重新获取验证码');
		}
     },1000);
}
/**
 * 登录
 * @param	string	account	账号
 * @param	string	pword	密码
 * @param	string	utype	账号类型(1:邮件 2：手机)
 * @param	string	uauto	是否自动登录(=0:非自动 >0：过期时间)
 * @return	void
 */
function ajaxLogin(account,pword,utype,uauto){
	$.ajax({
			 type		: "post",
			 url		: "/login/login/",
			 dataType	: "json",
			 data		: {"account" : account,"password" : pword,"cateId" : utype,"expire" : uauto},
			 error		: function(msg){
				error1('服务器繁忙请稍后再试');
			 },
			 success	: function(data){
				if(data){
					if(data.code == 1){
						window.location = '/user/main/';
					}else if(data.code == 2){
						error1('账号不存在');
						$('.pwdtxt').html('账号不存在');						
					}else if(data.code == 3){//
						error1('密码错误');
						$('.pwdtxt').html('密码错误');
					}else if(data.code == 4){
						error1('其它错误');
						$('.pwdtxt').html('其它错误');
					}
				}
			 }
	});
}
//手机验证码登录
function ajaxCode(){
	umobVal		= $.trim($('#umobile').val());
	ucodeVal	= $.trim($('#ucode').val());
	utype		= 2;
	isAmob 		= $('input[name="automobile"]').attr('checked');
	uauto	 	= isAmob ? 10 : 0;
	if( isAmob ){
		addDefaultMobile(umobVal);
	}else{
		delDefaultMobile();
	}
	$.ajax({
        type	: "post",
        url		: "/login/verifyCode/",
		async	: false,
		data	: {"account" : umobVal, "password":ucodeVal,"cateId" : utype,"expire" : uauto},
        dataType: "json",
		error	: function(msg){
			error2('服务器繁忙请稍后再试');
		},
        success: function(data){
				if (data.code == 1){
					logCode(umobVal,ucodeVal,utype,uauto);
					//window.location.reload();
				}else{
					error2(data.mess);
				}
        }
    });	
}
//验证通过后登录
function logCode(account,pword,utype,uauto){
	$.ajax({  
		type 		: "POST",  
		url 		: "/login/remoteUser/",  
		dataType 	: "json",
		data		: {"account" : account,"password" : pword,"cateId" : utype,"expire" : uauto},
		error		: function(msg){
			error2('服务器繁忙请稍后再试');
		},
		success 	: function(data){  
			if(data.code==1){
				window.location = '/user/main/';
			}
		}  
	});
}
//发送验证码
function sendCode(){
	if( isSendCode == true ){
		umobVal		= $.trim($('#umobile').val());
		$.ajax({  
			type 		: "POST",  
			url 		: "/login/sendCode/",
			dataType 	: "json",
			data		: {"account" : umobVal,"cateId" : 2},
			error		: function(msg){
				error1('服务器繁忙请稍后再试');
			},
			success 	: function(data){
					if (data.code == 1){
						isSendCode = false;//是否发送验证码
						_this = $('#getcode');
						countDown(downtime, _this);
						error2('验证码已发送',1);
					}else if (data.code == 2){
						error2('手机号不正确');
					}else{
						error2('发送失败');
					}
			}
		});
	}
}
function addDefaultMobile(string){
	addCookie('mobile',string,1000);
}
function delDefaultMobile(){
	delCookie('mobile');
}
function getDefaultMobile(){
	mobile = getCookie('mobile');
	if(mobile){
		$('#umobile').focus();
		$('#umobile').val(mobile);
		$('input[name="automobile"]').attr('checked',true);
	}
}