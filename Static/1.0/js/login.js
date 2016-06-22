var downtime  	= 60;
var home_url = '/index/index';
var isSendCode	= true;
$(document).ready(function(e) {
	//表单获得焦点隐藏提示
	$('#log1_account,#log1_password,#umobile,#ucode').focus(function(){
		success();
	});

	//密码登录
    $('#log1').click(function(){
		layer.load(1, {
			shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
		var ueaVal	= $.trim($('#log1_account').val());
		var uepVal	= $.trim($('#log1_password').val());
		if( ueaVal == '' ){
			error('请输入邮箱或者手机号码',1);
			$('.usertxt').html('请输入邮箱或者手机号码');
			layer.closeAll('loading');
			return false;
		}
		var uType	= getUserType(ueaVal);
		if(uType == 0){
			error('请输入正确邮箱或者手机号码',1);
			layer.closeAll('loading');
			return false;
		}
		if( uepVal == '' ){
			error('请输入密码',1);
			layer.closeAll('loading');
			return false;
		}else{
			var pMsg = verifyPwd(uepVal);
			if( pMsg ){
				error(pMsg,1);
				layer.closeAll('loading');
				return false;
			}
		}
		var checkbox = document.getElementById('accord');
		var uAuto	 = checkbox.checked ? 36000 : 0;
		ajaxLogin(ueaVal,uepVal,uType,uAuto);
	});
	
	//验证码登录
    $('#log2').click(function(){
		layer.load(1, {
			shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
		var umobVal	= $.trim($('#umobile').val());
		var ucodeVal= $.trim($('#ucode').val());
		if( umobVal == '' ){
			error('请输入手机号码',2);
			layer.closeAll('loading');
			return false;
		}
		var ismob	= isMobile(umobVal);
		if( ismob == false ){
			error('手机号码格式错误',2);
			layer.closeAll('loading');
			return false;
		}
		if( ucodeVal == '' ){
			error('请输入验证码',2);
			layer.closeAll('loading');
			return false;
		}
		success();
		//处理记住手机问题
		var isAmob = $('#remindM').attr('checked');
		var uauto = isAmob ? 36000 : 0;
		if( isAmob ){
			addDefaultMobile(umobVal);
		}else{
			delDefaultMobile();
		}
		ajaxCode(umobVal,ucodeVal,uauto);
	});

	//获取验证码
	$(document).on('click', "#getcode", function(){
		layer.load(1, {
			shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
		var umobVal	= $.trim($('#umobile').val());
		if( umobVal == '' ){
			error('请输入手机号码',2);
			layer.closeAll('loading');
			return false;
		}
		var ismob	= isMobile(umobVal);
		if( ismob == false ){
			error('手机号码格式错误',2);
			layer.closeAll('loading');
			return false;
		}
		success();
		sendCode(umobVal);
		return false;
    });

	//用户是否记住手机号码
	getDefaultMobile();

	//enter键事件
	$(document).keypress(function(e) {
		if(e.which == 13) {
			if($('#lg1').hasClass('on')){
				$('#log1').click();
			}else{
				$('#log2').click();
			}
			return false;
		}
	});
});

//错误提示
function error(msg,i){
	var obj = $('.e'+i);
	obj.show();
	obj.find('span').html(msg);
}
//成功提示
function success(){
	var obj = $('.eTips');
	obj.hide();
	obj.find('span').html('');
}

//验证密码内容
function verifyPwd(password){
	if ( password.length < 1 ) {
		return '密码不能为空';
	}
	var pattern = /[^\x00-\xff]/;
	if ( pattern.test(password) ) {
		return '请不要输入中文';
	}
    pattern = /^\d+$/;//验证纯数字
	if(pattern.test(password)){
		return '请输入6~20位非纯数字的密码';
	}
	pattern = /^(?!\d{1,8}$)[\S]{6,20}$/;
	if(!pattern.test(password)){
		return '请输入6~20位不包含空格的密码';
	}else{
		return '';
	}
}

//倒计时
function countDown(count, obj){
     window.setTimeout (function () {
		count --;
		obj.text(count);
		if(count > -1){
			countDown(count, obj);
		}else{
			isSendCode = true;
			$('.ms2').hide();
			$('.ms1').show();
		}
     },1000);
}

/**
 * 密码登录
 * @param account string	账号
 * @param pword string	    密码
 * @param utype int	        账号类型(1:邮件 2：手机)
 * @param uauto int	        是否自动登录(=0:非自动 >0：过期时间)
 */
function ajaxLogin(account,pword,utype,uauto){
	$.ajax({
		 type		: "post",
		 url		: "/login/login/",
		 dataType	: "json",
		 data		: {"account" : account,"password" : pword,"cateId" : utype,"expire" : uauto},
		 error		: function(msg){
			 layer.closeAll('loading');
			 error('服务器繁忙请稍后再试',1);
		 },
		 success	: function(data){
			if(data){
				if(data.code == 1){
					window.location = home_url;
				}else if(data.code == 2){
					error('账号不存在',1);
				}else if(data.code == 3){
					error('密码错误',1);
				}else if(data.code == 4){
					error('其它错误',1);
				}
			}
			 layer.closeAll('loading');
		 }
	});
}

//手机验证码登录
function ajaxCode(umobVal,ucodeVal,uauto){
	$.ajax({
        type	: "post",
        url		: "/login/verifyCode/",
		async	: false,
		data	: {"account":umobVal, "password":ucodeVal, "cateId":2, "expire":uauto},
        dataType: "json",
		error	: function(msg){
			layer.closeAll('loading');
			error('服务器繁忙请稍后再试',2);
		},
        success: function(data){
			if (data.code == 1){
				logCode(umobVal,ucodeVal,utype,uauto);
				//window.location.reload();
			}else{
				error(data.mess,2);
			}
			layer.closeAll('loading');
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
			error('服务器繁忙请稍后再试',2);
		},
		success 	: function(data){  
			if(data.code==1){
				window.location = home_url;
			}
		}  
	});
}

//发送验证码
function sendCode(umobVal){
	if( isSendCode == true ){
		$.ajax({
			type 		: "POST",  
			url 		: "/login/sendCode/",
			dataType 	: "json",
			data		: {"account" : umobVal,"cateId" : 2},
			error		: function(msg){
				layer.closeAll('loading');
				error('服务器繁忙请稍后再试',2);
			},
			success 	: function(data){
				layer.closeAll('loading');
				if (data.code == 1){
					isSendCode = false;//是否发送验证码
					//页面显示
					$('.ms2').show();
					$('.ms1').hide();
					countDown(downtime,$('#ms_n'));
					error('验证码已发送',2);
				}else if (data.code == 2){
					error('手机号不正确',2);
				}else{
					error('发送失败',2);
				}
			}
		});
	}else{
		layer.closeAll('loading');
	}
}

//记住手机号码相关

function addDefaultMobile(string){
	addCookie('mobile',string,1000);
}

function delDefaultMobile(){
	delCookie('mobile');
}

function getDefaultMobile(){
	var mobile = getCookie('mobile');
	if(mobile){
		$('#umobile').focus().val(mobile);
		$('#remindM').attr('checked',true);
	}
}