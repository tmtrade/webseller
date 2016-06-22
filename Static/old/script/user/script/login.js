/**********************************************************
 * 登录js
 * author：haydn
 * 
 **********************************************************/
var sendOnce 	= true;
var _iconE 		= '<i class="us-icon us-icon19"></i>';
$(document).ready(function(){
    $('#mTips').hide();
    $('#eTips').hide();	
	$(document).on('click', ".chaofan-mj-logBtn", function(){
        var _this = $(this);
        if (_this.attr('ctype') == 'm'){
            uc_checkMoblieForm();
        }else if (_this.attr('ctype') == 'e'){
            uc_checkEmailForm();
        }
        return false;
    });

    $(document).on('click', "#chaofan-ms-sent", function(){
        var _this 		= $(this);
        var umobile  	= $.trim($("#umobile").val());
        if (umobile == ''){
            $('#mTips').html(_iconE+'请填写手机号');
            $('#mTips').show();
            return false;
        }
        if (umobile.length != 11){
            $('#mTips').html(_iconE+'手机号不正确');
            $('#mTips').show();
            return false;
        }
        if ( !sendOnce ) return false;
		utype	= getUserType(umobile);
		if( utype != 2 ){
			$('#mTips').html(_iconE+'手机号不正确');
            $('#mTips').show();
            return false;
		}
		$.ajax({  
			type 		: "POST",  
			url 		: HOST+"/login/sendCode/?"+configUrl,
			dataType 	: "jsonp",
			data		: {"account" : umobile,"cateId" : utype},
			jsonp		: 'callback',  
			success 	: function(json){  
				$.each(json,function(i,n){
					if (n.code == 1){
						sendOnce = false;
						timer(downtime, _this);
						$('#mTips').html(_iconE+'验证码已发送');
						$('#mTips').show();
					}else if (n.code == 2){
						$('#mTips').html(_iconE+'手机号不正确');
						$('#mTips').show();
					}else{
						$('#mTips').html(_iconE+'发送失败');
						$('#mTips').show();
					}
				});
			}
		});
		return false;
    });
	
	$(document).on('click', "#chaofan-mj-login", function(){
        getDefaultMobile();
    });
});

function loginout()
{
	$.ajax({
        type: "post",
        url: "/passport/loginout/?"+configUrl,
        dataType: "json",
        success: function(data){
            if (data.code == 1){
				window.location = oldUrl;
            }
        }
	});
}

//倒计时
function timer(count, obj)
{
     window.setTimeout (function () {
		count --;
		obj.text(count + "秒后重新获取");
		if(count > -1){
			obj.css('background-color','#ddd');
			obj.css('color','#333');
			
			timer(count, obj);
		}else{
			sendOnce = true;
			obj.css('background-color','#deffd4');
			obj.css('color','#4e9b37'); 
			obj.text('重新获取验证码');
		}
     },1000);
}
function uc_checkEmailForm()
{
    var uemail  = $.trim($("#uemail").val());
    var upass   = $("#upass").val();
    if (uemail == '' || uemail == '请填写邮箱或手机号'){
        $('#eTips').html(_iconE+'请填写邮箱或手机号');
        $('#eTips').show();
        return false;
    }
    if (upass == '' || upass == '请填写密码'){
        $('#eTips').html(_iconE+'请填写密码');
        $('#eTips').show();
        return false;
    }
	var favorite_tid 	= $('#favorite_tid').val();
	var proposer_id 	= $('#proposer_id').val();
	var remindA 		= $('#remindA').val();
	var remindXA 		= $('#remindA').attr("checked");
	utype 				= getUserType(uemail);
	uc_loginfo(uemail,upass,remindXA);
	return true;
}
//验证验证码是否正确
function uc_checkMoblieForm()
{
    var umobile = $.trim($("#umobile").val());
    var ucode   = $.trim($("#ucode").val());
	var utype 	= getUserType(umobile);
    if (umobile == ''){
        $('#mTips').html(_iconE+'请填写手机号');
        $('#mTips').show();
        return false;
    }
    if (umobile.length != 11 || utype != 2){
        $('#mTips').html(_iconE+'手机号不正确');
        $('#mTips').show();
        return false;
    }
    if (ucode == ''){
        $('#mTips').html(_iconE+'请填写验证码');
        $('#mTips').show();
        return false;
    }
    if (ucode.length != 4){
        $('#mTips').html(_iconE+'验证码不正确');
        $('#mTips').show();
        return false;
    }
	
	var favorite_tid 	= $('#favorite_tid').val();
	var proposer_id 	= $('#proposer_id').val();
	//记住手机好嘛
	var remind 			= $('#remindM').attr('checked');
	if(remind){
		addDefaultMobile(umobile);	
	}else{
		delDefaultMobile();
	}
    $.ajax({
        type	: "post",
        url		: HOST+"/login/verifyCode/?"+configUrl,
		async	: false,
		data	: {"account" : umobile, "password":ucode,"cateId" : utype},
        dataType: "jsonp",
        success: function(json){
			$.each(json,function(i,n){
				if (n.code == 1){
					$('#mTips').hide();
					uc_logcode(umobile,ucode);
					//window.location.reload();
				}else{
					$('#mTips').html(_iconE+n.mess);
					$('#mTips').show();
				}
			});	
        }
    });
}
//用验证码登录
function uc_logcode(account,pword){
	utype 	  		= getUserType(account);
	var	ObjJsonp 	= ucCode = '';
	$.ajax({  
		type 		: "POST",  
		url 		: HOST+"/login/remoteUser/?"+configUrl,
		dataType 	: "jsonp",
		data		: {"account" : account,"password" : pword,"cateId" : utype,"expire" : validTime},
		jsonp		: 'callback',
		complete	: function(xhr,status){
			uc_logcodeCallback(ObjJsonp);
			if( ucCode == 1 ){
				delayRefresh();
			}
		},   
		success 	: function(json){  
			$.each(json,function(i,n){
				if(n.code==1 && n.ukey){
					addUserCook(n.ukey,n.nickname,n.usermobile,validTime);
				}
				ucCode = n.code;
			});
			ObjJsonp = json;
		}  
	});	
}
//登录
function uc_loginfo(account,pword,remind){
	validTime 		= remind ? validTime : 0;
	utype 	  		= getUserType(account);
	var	cfjs_code	= $('#chaofan_js_code').val();
	var	ObjJsonp 	= ucCode = '';
	$.ajax({  
		type 		: "POST",  
		url 		: HOST+"/login/login/?"+configUrl,
		dataType 	: "jsonp",
		data		: {"account" : account,"password" : pword,"cateId" : utype,"expire" : validTime},
		jsonp		: 'callback',
		complete	: function(xhr,status){
			uc_loginfoCallback(ObjJsonp);
			if( ucCode == 1 ){
				delayRefresh();
			}
		}, 
		success 	: function(json){  
			$.each(json,function(i,n){
				if(n.ukey){
					addUserCook(n.ukey,account,n.usermobile,validTime);
				}
				msg = getError(n.code);
				$('#eTips').html(_iconE+msg);
				$('#eTips').show();
				ucCode = n.code;
			});
			if(cfjs_code != ''){
				eval(cfjs_code);
			}
			ObjJsonp = json;
		}  
	});
}

//收藏商标
function cfjsCT (number, source){
	var uc_key =getCookie(uckeystr);
	if(uc_key == undefined || uc_key == null || uc_key == ''){
		return false;
	}
	$.ajax({
		type : "GET",
		url  : HOST + '/collect/addtrademark/',
		dataType: 'jsonp',
		jsonp: 'jsoncallback',
		data:{'number':number, 'source' : source, 'ukey': uc_key},
		success: function (data) { 
		}
	})
	return true;
}

//发送密码
function sendCodePassword(string,utype){
	$.ajax({  
		type 		: "POST",  
		url 		: HOST+"/login/dynamicPassword/?"+configUrl,
		dataType 	: "jsonp",
		data		: {"account" : string,"cateId" : utype},
		jsonp		: 'callback',  
		success 	: function(json){  
			$.each(json,function(i,n){
				
				
			});
		}  
	});
}
function addDefaultMobile(tel){
	addCookie('mobile',tel,1000);	
}
function delDefaultMobile(){
	delCookie('mobile');	
}
function getDefaultMobile(){
	mobile = getCookie('mobile');
	if(mobile){
		$('#umobile').focus();
		$('#umobile').val(mobile);
	}
}