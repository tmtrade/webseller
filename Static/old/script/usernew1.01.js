var downtime  	= 60;
var isSendCode	= true;
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\"/Static/script/user/script/tool.js\"></scr" + "ipt>");
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\"/Static/script/user/script/public.js\"></scr" + "ipt>");

$(document).ready(function(e) {
	//回车事件
	$(document).keypress(function(e) { 
		if(e.which == 13) {
			if($(".home_tcdl_01").is(":hidden")){
				doLogin();
			}else if($(".home_tc_bg").is(":hidden")){
				doLoginMobile();
			}
		} 
	});
	//登录
    $('#logver1').click(function(){
		doLogin();
	});
	
	//验证码注册/登录(提交)
    $('#logver2').click(function(){
		doLoginMobile();
	});
	//获取验证码
	$('#getcode').on('click', function(){
		isMob = verifyMobile();
		if( isMob==1 ){
			$('#usertxt').removeClass('usertxt').hide();
			sendCode();
			isMob = '';
		}else{
			prompts(isMob,$('#usertxt'));
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
//提示
function prompts(msg,obj,ishide){
	if(ishide){
		obj.hide().html(msg).addClass('usertxt');	
	}else{
		obj.show().html(msg).addClass('usertxt');
	}
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
		return '请输入手机号码';
	}
	if( umobVal == '' || umobVal == '手机号' ){
		return '请输入手机号';
	}else{
		ismob	= isMobile(umobVal);
		if( ismob == false ){
			return '手机号码格式错误';
		}
	}
	success2();
	return 1;
}
//倒计时
function countDown(count, obj){
     window.setTimeout (function () {
		count --;
		obj.text(count + "s后重新获取");
		if(count > -1){
			obj.css('background-color','#ddd');
			obj.css('color','#333');
			
			countDown(count, obj);
		}else{
			isSendCode = true;
			obj.css('background-color','#deffd4');
			obj.css('color','#4e9b37'); 
			obj.text('重新获取');
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
	var	ObjJsonp = '';
	$.ajax({
			 type		: "post",
			 url		: "/login/login/",
			 dataType	: "json",
			 data		: {"account" : account,"password" : pword,"cateId" : utype,"expire" : uauto},
			 complete	: function(xhr,status){
				ajaxLoginCallback(ObjJsonp);
				
			 },
			 error		: function(msg){
				prompts('服务器繁忙请稍后再试',$('#usertxt'));
			 },
			 success	: function(data){
				if(data){
					ObjJsonp = data;
					if(data.code == 1){
						window.location = '/user/main/';
					}else if(data.code == 2){
						prompts('账号不存在',$('#usertxt'));
					}else if(data.code == 3){//
						prompts('密码错误',$('#usertxt'));
					}else if(data.code == 4){
						prompts('其它错误',$('#usertxt'));
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
			prompts(data.mess,$('.usertxt'));
		},
        success: function(data){
				if (data.code == 1){
					logCode(umobVal,ucodeVal,utype,uauto);
					//window.location.reload();
				}else{
					prompts(data.mess,$('.usertxt'));
				}
        }
    });	
}
//验证通过后登录
function logCode(account,pword,utype,uauto){
	sturl = getStCode();
	$.ajax({  
		type 		: "POST",  
		url 		: "/login/remoteUser/?"+sturl,
		dataType 	: "json",
		data		: {"account" : account,"password" : pword,"cateId" : utype,"expire" : uauto},
		error		: function(msg){
			prompts('服务器繁忙请稍后再试',$('#usertxt'));
		},
		success 	: function(data){  
			if(data.code==1){
				//window.location.reload();
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
				prompts('服务器繁忙请稍后再试',$('#usertxt'));
			},
			success 	: function(data){
					if (data.code == 1){
						isSendCode = false;//是否发送验证码
						_this = $('#getcode');
						countDown(downtime, _this);
						prompts('验证码已发送',$('#usertxt'));
					}else if (data.code == 2){
						prompts('手机号不正确',$('#usertxt'));
					}else{
						prompts('发送失败',$('#usertxt'));
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
function ajaxLoginCallback(obj){
	if(obj.code != 1){
		$('#logver1').attr('disabled',false).val('点击登录');
	}
}
//普通登录
function doLogin(){
	ueaVal	= $.trim($('#uemail').val());
	uepVal	= $.trim($('#upass').val());
	if( ueaVal == '' ){
		prompts('请输入邮箱或者手机号码',$('#usertxt'));
		return false;
	}
	uType	= getUserType(ueaVal);
	if(uType == 0){
		prompts('请输入正确邮箱或者手机号码',$('#usertxt'));
		return false;
	}
	if( uepVal == '' ){
		prompts('请输入密码',$('#usertxt'));
		return false;
	}else{
		pMsg = verifyPwd(uepVal);
		if( pMsg ){
			prompts(pMsg,$('#usertxt'));
			return false;
		}
	}
	prompts('',$('#usertxt'),1);
	isAccord = $('.accord').attr('checked');
	uAuto	 = isAccord ? 10 : 0;
	$('#logver1').attr('disabled',true).val('登录中...');
	ajaxLogin(ueaVal,uepVal,uType,uAuto);
}
//验证码登录
function doLoginMobile(){
	isMob = verifyMobile();
	if(isMob==1){
		ucodeVal = $.trim($('#ucode').val());
		if( ucodeVal == '' ){
			prompts('请输入验证码',$('#usertxt'));
			return false;
		}
		ajaxCode();
	}else{
		prompts(isMob,$('#usertxt'));
	}
	return false;
}