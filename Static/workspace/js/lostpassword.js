//第一步
var isSendCode 	= true;
//回车事件
$(document).keypress(function(e) { 
	if(e.which == 13){
		num1 = $('#loststeps1').length;
		num2 = $('#loststeps2').length;
		num3 = $('#loststeps3').length;
		if(num1==1){
			doLostcode1();
		}
		if(num2==1){
			doLostcode2();
		}
		if(num3==1){
			doLostcode3();
		}
		return false;
	} 
}); 
//发送验证码
$(document).on('click', "#lostcode", function(){
	umobile = $('#umobile').val();
	isMob 	= getUserType(umobile);
	if( isMob == 1 || isMob == 2 ){
		$('#red1').hide();
		if( isSendCode == false ){
			return false;	
		}
		means.verifyUser(umobile);
		var isCode  = 0;
		var msg		= '';
		means.sendLostCode(umobile,function(Obj){
			isSendCode = false;
			isCode 	= Obj.code;
			msg		= Obj.msg;
		});
		if( isCode == 1 ){
			$('#red2').hide().next().show();	
			lostCountDown(60, $(this));
		}else{
			$('#red2').show().html(msg).next().hide();
		}
	}else{
		$('#red1').show();
	}
	return false;
});
//验证验证码第一步
$(document).on('click', "#loststeps1", function(){
	doLostcode1();
});
//END
//第二步
$(document).on('click', "#btnSendCode", function(){
	if( isSendCode == false ){
		return false;	
	}
	umobile 	= getQueryString('mobile');
	isMob 		= getUserType(umobile);
	string		= isMob == 2 ? '手机号码' : '邮箱';
	$('.actype').html(string);
	$('#msgfang').show();
	var isCode  = 0;
	var msg		= '';
	means.sendLostCode(umobile,function(Obj){
		isSendCode = false;
		isCode 	= Obj.code;
		msg		= Obj.msg;
	});
	if( isCode == 1 ){
		$('#red2').hide();	
		lostCountDown(60, $(this));
	}else{
		$('#red2').show().html(msg);
	}
	return false;
});
$(document).on('click', "#loststeps2", function(){
	doLostcode2();
});


function lostCountDown(count, obj){
     setTimeout (function () {
		count --;
		obj.text(count + "秒后重新获取");
		if(count > -1){
			obj.removeClass('btn-success').addClass('btn-default');
			lostCountDown(count, obj);
		}else{
			isSendCode = true;
			obj.removeClass('btn-default').addClass('btn-success');
			obj.text('重新获取验证码');
		}
     },1000);
}
function setSteps(num,account){
	//console.log('/lostpassword/steps'+num+'/?mobile='+account);
	if(num > 0 && num < 5){
		window.location = '/lostpassword/steps'+num+'/?mobile='+account;
	}	
}
function getQueryString(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return decodeURI(r[2]); return null;
}
//验证手机验证码回调
function verifyCodeCallback(obj,account){
	if( obj.code == 1 ){
		steps = $('.steps').val();
		setSteps(steps,account);//开启第二步
	}else{
		$('#red2').show().html(obj.mess);	
	}
}
function verifyUserCallback(obj){
	if( obj.code == 1 ){
		
	}else{
		$('#red1').show().html(obj.msg).next().hide();
	}
}
function verifyImgCodeCallback(obj,account){
	if( obj.code == 1 ){
		steps = $('.steps').val();
		setSteps(steps,account);//开启第二步
	}else{
		refreshimgcode();
		string = obj.code == 3 ? 'red2' : 'red1';
		$('#'+string).show().html(obj.msg).next().hide();	
	}
}
function refreshimgcode(){
	url = '/authcode/?'+Math.random();
	$('#authimgcode').attr('src',url);
}
//忘记密码第一步
function doLostcode1(){
	umobile = $('#umobile').val();
	ucode 	= $('#ucode').val();
	if( !umobile && !ucode ){
		$('#red1,#red2').show();
		return false;
	}
	if( !umobile ){
		$('#red1').show();
		return false;
	}else{
		isMob 	= getUserType(umobile);
		if( isMob == 0 ){
			$('#red1').show();
			return false;
		}else{
			$('#red1').hide();
		}
	}
	if( !ucode ){
		$('#red2').show();
		return false;
	}
	means.verifyUser(umobile);
	means.verifyImgCode(umobile,ucode);
	return false;
}
//忘记密码第一步
function doLostcode2(){
	ucode = $('.form-control').val();
	if( !ucode ){
		$('#red2').show();
		return false;
	}
	umobile = getQueryString('mobile');
	means.verifyCode(umobile,ucode);
	return false;
}