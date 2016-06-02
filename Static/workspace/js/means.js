var loginTime	= 36000;	
var means	= {
	//忘记密码发送验证码
	sendLostCode : function(account,callback){
		utype		 = getUserType(account);
		$.ajax({
			type	: "post",
			url		: "/lostpassword/sendResetCode/",
			async	: false,
			data		: {"account" : account,"cateId" : utype},
			dataType: "json",
			complete	: function(xhr,status){
				
			},
			error	: function(msg){
				alert('服务器繁忙请稍后再试');
			},
			success: function(data){
				//console.log(data);
				callback(data);
			}
		});
		return false;
	},
	//发送验证码
	sendCode	: function(account){
		var	ObjJsonp = '';
		$.ajax({
			type	: "post",
			url		: "/login/sendCode/",
			async	: false,
			data	: {"account" : account},
			dataType: "json",
			complete: function(xhr,status){
				sendCodeCallback(ObjJsonp);
			},
			error	: function(msg){
				alert('服务器繁忙请稍后再试');
			},
			success: function(data){
				ObjJsonp = data;
			}
		});
		return false;
	},
	//验证手机验证码
	verifyCode  : function(mobile,code,password){
		utype		 = getUserType(mobile);
		var	ObjJsonp = '';
		$.ajax({
			type	: "post",
			url		: "/login/verifyCode/",
			async	: false,
			data	: {"account" : mobile, "password":code},
			dataType: "json",
			complete	: function(xhr,status){
				verifyCodeCallback(ObjJsonp,mobile,password);
			},
			error	: function(msg){
				alert('服务器繁忙请稍后再试');
			},
			success: function(data){
				ObjJsonp = data;
			}
    	});
		return false;
	},
	//验证码登录
	loginCode : function(account,pword){
		utype = getUserType(mobile);
		$.ajax({  
			type 		: "POST",  
			url 		: "/login/remoteUser/",  
			dataType 	: "json",
			data		: {"account" : account,"password" : pword,"expire" : loginTime},
			complete	: function(xhr,status){
				
			},
			error		: function(msg){
				alert('服务器繁忙请稍后再试');
			},
			success 	: function(data){  
				if(data.code==1){
					window.location.reload();
				}
			}
		});
	},
	//忘记密码
	uplostpassword : function(m1,m2,account){
		var	ObjJsonp = '';
		$.ajax({  
			type 		: "POST",  
			url 		: "/lostpassword/upResetpwd/",  
			dataType 	: "json",
			data		: {"mobile" : account,"m1" : m1,"m2" : m2},
			complete	: function(xhr,status){
				uplostpasswordCallback(ObjJsonp,account);
			},
			error		: function(msg){
				alert('服务器繁忙请稍后再试');
			},
			success 	: function(data){  
				ObjJsonp = data;
			}
		});
		return false;
	},
	//验证用户是否存在
	verifyUser : function(account){
		utype 		 = getUserType(account);
		var	ObjJsonp = '';
		$.ajax({
			 type		: "post",
			 url		: "/login/verifyUser/",
			 dataType 	: "json",
			 async 		: false,
			 data		: { 'account' : account},
			 complete	: function(xhr,status){
				verifyUserCallback(ObjJsonp);
			 },
			 error		: function(msg){
				 alert('服务器繁忙请稍后再试');
			 },
			 success	: function(data){
				ObjJsonp = data;
			 }
		});
		return ObjJsonp;
	},
	//用户注册
	userReg	: function(account,pword){
		var ObjJsonp = '';
		$.ajax({
			 type		: "post",
			 url		: "/register/regUser/",
			 dataType	: "json",
			 data		: {"account" : account,"password" : pword},
			 complete	: function(xhr,status){
				userRegCallback(ObjJsonp);
			 },
			 error		: function(msg){
				alert('服务器繁忙请稍后再试');
			 },
			 success	: function(data){
				 ObjJsonp = data;
			 }
		});
	},
	//验证图形验证码
	verifyImgCode	: function(account,pword){
		var ObjJsonp = '';
		$.ajax({
			 type		: "post",
			 url		: "/lostpassword/verifyImgCode/",
			 dataType	: "json",
			 data		: {"account" : account,"password" : pword},
			 complete	: function(xhr,status){
				verifyImgCodeCallback(ObjJsonp,account);
			 },
			 error		: function(msg){
				alert('服务器繁忙请稍后再试');
			 },
			 success	: function(data){
				 ObjJsonp = data;
			 }
		});
	}
};