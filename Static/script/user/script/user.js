/**********************************************************
 * 登录相关的全局设置
 * author：haydn
 * 
 **********************************************************/
//var HOSTCRM 	= 'http://crm:81/';
var HOSTCRM 	= 'http://demo.chofn.com:88/';
var crmkey		= '11afghaxbnPZ4gOxSO7lcLLsPu';
var CRMNAME		= 'crm_name';
var CRMSESSID	= 'crm_sessid';
//------------------------------------------------------------- 
//var HOST 	 	= 'http://www.uc.net';//处理域名
var HOST 	 	= 'http://seller.chofn.net';//处理域名
var configUrl	= '';//配置信息
var classid	 	= 'chaofan-login';
var config	 	= new Array();
var uckeystr 	= 'uc_ukey';
var ucnamestr	= 'uc_nickname';
var ucmobile 	= 'uc_mobile';
var validTime	= 36000;
var isDelay	 	= false;
var ucConfig 	= {
	//设置配置
	setConfig	   : function(time,key1,key2,islag){
		exturl 	 = '';
		isfun 	 = isExitsFunction('zzGetCookie');
		if(isfun){
			stsid  = zzGetCookie('sid');
			starea = zzGetCookie('area');
			exturl = '&sid='+stsid+'&area='+starea;
		}
		cUrl 	  = '&timestamp='+time+'&nonceStr='+key1+'&signature='+key2+exturl;
		configUrl = cUrl;
		isDelay	  = islag ? true : false;
		crm.verifyCrmLog();
	},
	//获取配置
	getConfigUrl : function(){
		return configUrl;
	},
	//把需要提交的数据设置成URL连接
	setPostUrl   : function(data){
		var url	 = '';
		newArray = data;
		for( var k in newArray ){
			url+='&'+k+'='+newArray[k];
		}
		crm_ucname 	= getCookie(CRMNAME);
		coUrl		= crm_ucname ? '' : ucConfig.getConfigUrl();
		return url+coUrl;
	},//设置登录信息
	setLogInHtml : function(){
		var logHtml = '<a href="javascript:void(0);" id="chaofan-mj-login">登录</a>';
		crm_ucname 	= getCookie(CRMNAME);//检查crm是否登录
		if(crm_ucname){
			ucUser.logexit();//退出用户中心
			var logHtml = '<a href="javascript:void(0);" id="chaofan-mj-nickname" class="chaofan-mj-crmuc">'+crm_ucname+'</a>&nbsp;&nbsp;';
		}else{
			nickname  	= getCookie(ucnamestr);
			if( ukey ){
				var logHtml = '<a href="javascript:void(0);" id="chaofan-mj-nickname">'+nickname+'</a>&nbsp;&nbsp;<a href="javascript:;" id="chaofan-mj-logexit">退出</a>';
			}
		}
		return logHtml;
	},
	setLogIn :function(string){
		$('.'+classid).html(string);
	}
	
};
var ucUser = {
	//验证是否登录
	verifyLog : function(){
		ukey  			= getCookie(uckeystr);
		nickname  		= getCookie(ucnamestr);
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
						ucLoginTemp.setLoginTemp();
					}else{
						delteUserCook();					
					}
				});
				ObjJsonp = json;
			}  
		});
	},
	/**
	* 验证用户是否存在
	* @author	haydn
	* @since	2016/3/01
	*
	* @param    array||string	$data  	 数据包或账户(数据包必须包含tel字段)
	* @return	void
	*/
	exist : function(data){
		account			= isArray(data) ? data['tel'] : data;
		url 			= ucConfig.setPostUrl();
		var	ObjJsonp 	= '';
		$.ajax({  
			type 		: "GET",  
			url 		: HOST+"/login/verifyUser/?"+url,  
			dataType 	: "jsonp",  
			jsonp		: 'callback',
			data		: {'account' : account}	,
			complete	: function(xhr,status){
				existCallback(ObjJsonp,data);
			},
			error		: function(msg){},
			success 	: function(json){
				ObjJsonp = json;
			}  
		});
	},
	//退出
	logexit:function(isload){
		isload	= isload ? 1 : 0;
		var	ObjJsonp 	= '';
		url 			= ucConfig.setPostUrl();
		$.ajax({  
			type 		: "GET",  
			url 		: HOST+"/login/logout/?"+url,
			dataType 	: "jsonp",  
			jsonp		: 'callback',
			complete	: function(xhr,status){
				logexitCallback(ObjJsonp);
			},
			error		: function(msg){
				
			},
			success 	: function(json){ 
				delteUserCook();
				if( isload == 1 ){
					window.location.reload();
				}
				ObjJsonp = json;
			}  
		});
	}
};
$(document).ready(function() {
	ucUser.verifyLog();
	//退出
	$(document).on('click', "#chaofan-mj-logexit", function(){
		ucUser.logexit(1);
		return false;
    });
});

document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/tool.js\"></scr" + "ipt>");//工具函数
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/md5.js\"></scr" + "ipt>");//工具函数

document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/crm.js\"></scr" + "ipt>");//crm系统
//登录一
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/layer/layer.js\"></scr" + "ipt>");
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/userlog.js\"></scr" + "ipt>");

document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/login.js\"></scr" + "ipt>");
document.write("<link rel=\"stylesheet\" href=\""+HOST+"/Static/script/user/style/mj-login.css\"\/>");

//登录二
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/usernetwork.js\"></scr" + "ipt>");
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/usercallback.js\"></scr" + "ipt>");
//商标购买
document.write("<scr" + "ipt type=\"text/javascript\" charset=\"utf-8\" src=\""+HOST+"/Static/script/user/script/buybrand.js\"></scr" + "ipt>");
