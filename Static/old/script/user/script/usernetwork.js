var APIURL		= '';
var dataArray	= new Array();
var ucNetwork = {
	//数据提交到分配系统
	submitData : function(data){
		remindArray = verifyPost(data);
		if( remindArray['code'] != 1 ){
			return remindArray;
		}
		url 		= ucConfig.setPostUrl(data);
		var	ObjJsonp = '';
		$.ajax({
			 type		: "post",
			 url		: HOST+"/login/networkLogin/?"+url,
			 dataType	: "jsonp",
			 async 		: false,
			 data		: {},
			 complete	: function(xhr,status){
				submitDataCallback(ObjJsonp);
			 },
			 error		: function(msg){},
			 success	: function(json){
				ObjJsonp = json;
			 }
		});
	},
	//用户登录(用密码登录)
	userLog	 : function(account,password,data){
		if(!account){
			return false;	
		}
		if(!password){
			return false;
		}
		cateId 		 = getUserType(account);
		if(cateId==0){
			return false;	
		}
		var	ObjJsonp 	= ucCode = '';
		url 			= ucConfig.setPostUrl();
		$.ajax({
			 type		: "post",
			 url		: HOST+"/login/login/?"+url,
			 dataType	: "jsonp",
			 data		: { 'account' : account,'password' : password,'cateId' : cateId},
			 complete	: function(xhr,status){
				userLogCallback(ObjJsonp,data);
				if( ucCode == 1 ){
					delayRefresh();
				}
			 },
			 error		: function(msg){},
			 success	: function(json){
				//getError(code);
				$.each(json,function(i,n){
					msg = getError(n.code);
					if(n.ukey){
						addUserCook(n.ukey,n.nickname,n.usermobile);
					}
					ucCode = n.code;
				});
				ObjJsonp = json;
			 }
		});
	},
	//发送验证码
	sendCode : function(account,obj,title){
		if(!account){
			return false;	
		}
		if(isSendCode == false){
			return false;	
		}
		var	ObjJsonp = '';
		url 			= ucConfig.setPostUrl();
		$.ajax({
			 type		: "post",
			 url		: HOST+"/login/sendCode/?"+url,
			 dataType	: "jsonp",
			 data		: { 'account' : account,'cateId' : 2},
			 complete	: function(xhr,status){
				sendCodeCallback(ObjJsonp,obj,title);
			 },
			 error		: function(msg){},
			 success	: function(json){
				ObjJsonp = json;
			 }
		});
	},
	//验证验证码是否合法
	verifyCode : function(account,code){
		url 			= ucConfig.setPostUrl();
		var	ObjJsonp = '';
		$.ajax({
			 type		: "post",
			 url		: HOST+"/login/verifyCode/?"+url,
			 dataType	: "jsonp",
			 data		: { 'account' : account,'password' : code,'cateId' : 2},
			 complete	: function(xhr,status){
				verifyCodeCallback(ObjJsonp,account,code);
			 },
			 error		: function(msg){},
			 success	: function(json){
				ObjJsonp = json;
			 }
		});
	},
	//用验证码登录
	logCode : function(account,password){
		cateId 		 = getUserType(account);
		if(cateId==0){
			return false;	
		}
		url 			= ucConfig.setPostUrl();
		var	ObjJsonp = '';
		$.ajax({  
			type 		: "POST",  
			url 		: HOST+"/login/remoteUser/?"+url,  
			dataType 	: "jsonp",
			data		: {"account" : account,"password" : password,"cateId" : cateId},
			jsonp		: 'callback',
			complete	: function(xhr,status){
				//logCodeCallback(ObjJsonp);
			},
			success 	: function(json){  
				$.each(json,function(i,n){
					if(n.code==1 && n.ukey){
						addUserCook(n.ukey,n.nickname,n.usermobile);
						window.location.reload();
					}
				});
				ObjJsonp = json;
			}
		});	
	},
	//验证是否登录
	verifyLog : function(){
		ukey  		= getCookie(uckeystr);
		nickname  	= getCookie(ucnamestr);
		url 			= ucConfig.setPostUrl();
		var	ObjJsonp 	= '';
		$.ajax({  
			type 		: "GET",  
			url 		: HOST+"/login/verifyLog/?"+url,  
			dataType 	: "jsonp",  
			jsonp		: 'callback',
			complete	: function(xhr,status){
				verifyLogCallback(ObjJsonp);
			},
			error		: function(msg){},
			success 	: function(json){  
				$.each(json,function(i,n){
					if(n.ukey){
						addUserCook(n.ukey,n.nickname,n.usermobile);
					}else{
						delteUserCook();					
					}
				});
				ObjJsonp = json;
			}  
		});
	},
	//验证用户是否存在
	verifyUser : function(account){
		url 			= ucConfig.setPostUrl();
		var	ObjJsonp = '';
		$.ajax({
			 type		: "post",
			 url		: HOST+"/login/verifyUser/"+url,
			 dataType	: "jsonp",
			 data		: { 'account' : account,'cateId' : 2},
			 complete	: function(xhr,status){
				verifyUserCallback(ObjJsonp);
			 },
			 error		: function(msg){},
			 success	: function(json){
				ObjJsonp = json;
			 }
		});
	},
	//设置登录
	setLogin : function(){
		logHtml 	= ucConfig.setLogInHtml();
		ucConfig.setLogIn(logHtml);
	},
	//修改密码
	editPassword : function(account){
		url 			= ucConfig.setPostUrl();
		$.ajax({  
			type 		: "GET",  
			url 		: HOST+"/login/backPassword/?"+url,  
			dataType 	: "jsonp",  
			jsonp		: 'callback',
			data		: { 'account' : account},
			complete	: function(xhr,status){
				//logexitCallback(ObjJsonp);
			},
			error		: function(msg){},
			success 	: function(json){ 
				ObjJsonp = json;
			} 
		});
	},
}; 

//验证提交来的数据是否包含相应数据
function verifyPost(data){
	configArray	= new Array('tel','name','subject','remarks','trademark','class','tid','pttype','type','ptype');
	remindArray	= new Array();
	for( var k in data ){
		isKey = $.inArray(k,configArray);
		if( isKey < 0 ){
			remindArray['code'] = 0;
			remindArray['msg'] 	= '缺少关键词'+k;
			break;
		}else{
			remindArray['code'] = 1;
			remindArray['msg'] 	= '验证通过';
		}
	}
	return remindArray;
}
//获取相关配置（提交验证用）
function getJsApiCode(){
	$.ajax({
		 type		: "post",
		 url		: HOST+"/login/getJsApiCode/",
		 dataType	: "jsonp",
		 error		: function(msg){
				
		 },
		 success	: function(json){
			 $.each(json,function(i,n){
				if(n.code==1){
					config['timestamp'] = n.data['timestamp'];
					config['nonceStr'] 	= n.data['nonceStr'];
					config['signature'] = n.data['signature'];
				}	
			 });
		 }
	});
}
