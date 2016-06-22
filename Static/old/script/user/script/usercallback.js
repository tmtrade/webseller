/**
* 用户相关的回调函数(设置页面自定义样式)
* 在方法里面做处理,参数不可改
* @author   haydn
* @since    2016-02-29
*/
//数据提交到分配系统
function submitDataCallback(Obj){
	//console.log(Obj); 
	$.each(Obj,function(i,n){
		alert(n.msg);
	});	
}

//发送验证码
function sendCodeCallback(Obj,htmlobj,title){
	$.each(Obj,function(i,n){
		if(n.code > 0 ){//发送成功
			timer(60, htmlobj, title);//倒计时
		}else{
			alert(n.msg);
		}
	});
}
//验证验证码是否合法
function verifyCodeCallback(Obj,account,code){
	console.log(Obj); 
	$.each(Obj,function(i,n){
		if(n.code==1){
			ucNetwork.logCode(account,code);
		}else{
			alert(n.mess);
		}
	});	
}

//验证是否登录
function verifyLogCallback(Obj){
	$.each(Obj,function(i,n){
		
	});	
}

//验证用户是否存在
function verifyUserCallback(Obj){
	$.each(Obj,function(i,n){
		
	});	
}

//购买商标
function buyAddCallback(Obj,data){
	var str = '';
	$.each(Obj,function(i,n){
		str =  n.code;
	});
	console.log(data);
	return str;
}
//验证是否购买过商标
function isexistCallback(Obj,data){
	$.each(Obj,function(i,n){
		//alert(n.msg);
	});
}
//验证账号是否存在
function existCallback(Obj,data){
	$.each(Obj,function(i,n){
		//alert(n.msg);
	});
}
//用户登录回调（直接传入用户和密码）
function userLogCallback(Obj,data){
	$.each(Obj,function(i,n){
		//alert(n.msg);
	});
}
//用验证码登录
function uc_logcodeCallback(Obj){
	$.each(Obj,function(i,n){
		if(n.code==1 && n.ukey){//登陆成功
			
		}
	});
}
//登录
function uc_loginfoCallback(Obj){
	$.each(Obj,function(i,n){
		if(n.ukey){//登陆成功
			
		}
	});
}
//退出
function logexitCallback(Obj){
	
}