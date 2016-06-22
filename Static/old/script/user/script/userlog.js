/**********************************************************
 * 登录前设置
 * author：haydn
 * 
 **********************************************************/
var downtime  	= 60;//倒计时(单位s)
var issend		= 0;
var isLoginhtml	= '';//是否加载过登录页面
var isDomain 	= true;
var newcopy		= new Array();
var copyArr		= new Array();
var ukey		= '';
var popupObj	= '';
copyArr['dl_account'] 	= '账号登录';
copyArr['dl_kaccount'] 	= '快捷登录';
copyArr['dl_uname'] 	= '请输入邮箱或手机号';
copyArr['dl_upass'] 	= '请输入密码（6-20位数字，字母或符号）';
copyArr['dl_msg'] 		= '验证即注册，未注册将自动创建账号';
copyArr['dl_mobile'] 	= '请输入手机号码';
copyArr['dl_mcode'] 	= '请输入手机验证码';
//有登陆页面
var ucLoginTemp = {
	//设置登录
	setLoginTemp : function(copy){
		url 		= ucConfig.setPostUrl();
		newcopy		= copy;
		ukey  		= getCookie(uckeystr);
		nickname  	= getCookie(ucnamestr);//alert(nickname);
		isLoad 		= $('#chaofan-mj-login').length;
		logHtml 	= ucConfig.setLogInHtml();
		if( !ukey ){
			ucLoginTemp.getLogHtml(copy);
		}
		ucConfig.setLogIn(logHtml);
	},
	//获取登录页面
	getLogHtml : function(copy){
		url = ucConfig.setPostUrl();
		if(!isLoginhtml){
			$.ajax({  
				type 		: "GET",  
				url 		: HOST+"/login/getLogHtml/?"+url,  
				dataType 	: "jsonp",  
				jsonp		: 'callback',
				complete	: function(xhr,status){
					popUp(popupObj,true);
				},
				error		: function(msg){
					//alert('验证失败');	
				},
				success 	: function(json){  
					$.each(json,function(i,n){
						if(n.contents){
							$('body').append(n.contents);
							isLoginhtml = n.contents;
							setInitCopy(copy);
							popupObj	= $('.chaofan-mj-loginM');						
						}	
					});
				}
			});
		}else{
			logNum = $('.chaofan-mj-loginM').length;
			if( logNum == 0 ){
				$('body').append(isLoginhtml);
			}
		}
	},
	//验证中心是否登录
	verifyLog : function(){
		ukey  		= getCookie(uckeystr);
		nickname  	= getCookie(ucnamestr);
		url 		= ucConfig.setPostUrl();
		$.ajax({  
			type 		: "GET",  
			url 		: HOST+"/login/verifyLog/?"+url,  
			dataType 	: "jsonp",  
			jsonp		: 'callback',
			error		: function(msg){
				//alert('验证失败');
			},
			success 	: function(json){  
				$.each(json,function(i,n){
					if(n.ukey){
						addUserCook(n.ukey,n.nickname,n.usermobile);
						ucLoginTemp.setLoginTemp();
					}else{
						delteUserCook();					
					}
				});
			}  
		});
	}
};

//设置文案
function setInitCopy(newcopy){
	if(isArray(newcopy)){
		for(var key in newcopy){
    		strkey = copyArr.hasOwnProperty(key);
			if(strkey==true){
				$('#chaofn_'+key).text(newcopy[key]);
			}else{
				$('#chaofn_'+key).text(copyArr[key]);
			}
		}
	}else{
		for(var key in copyArr){
			$('#chaofn_'+key).text(copyArr[key]);
		}
	}
}
//鼠标点击设置文案
function setCopyKey(keyid){
	if(keyid){
		keys 	= keyid.replace('chaofn_','');
		if(newcopy){
			strkey 	= newcopy.hasOwnProperty(keys);
			if(strkey==true){
				$('#chaofn_'+keys).html(newcopy[keys]);
			}else{
				$('#chaofn_'+keys).html(copyArr[keys]);
			}
		}else{
			$('#chaofn_'+keys).html(copyArr[keys]);
		}
	}
}
//验证用户
function verifyUser(){
	userstr = $.trim($('#uemail').val());
	type    = getUserType(userstr);
	if(type==0){
		$('#eTips').val()
	}
}
//倒计时(发送密码)
function setSendPowtime(val){
	if (!val) {
		countdown = downtime; 
	} else { 
		countdown--; 
	}
	if( countdown > 0 ){
		issend = 1;	
		$('#chaofan-ms-sent').html(countdown+'秒后重新获取');
		setTimeout(function() { 
			setSendPowtime(countdown);
		},1000);
	}else{
		issend 		= 0;
		$('#chaofan-ms-sent').html('发送密码');
	}
	//console.log(countdown);
}
/****
* n ：商标号
* c ：商标类别
* source ：来源类别 1:交易；2：竞手
*/
function collectTrademark(number, source, callback){

	var isLogin = popUp(popupObj);
	if(isLogin == false){ 
		$('#chaofan_js_code').val("cfjsCT('"+number+"', '"+source+"')");
		return false;
	}
	var _data ;
	$.ajax({
		type : "GET",
		url  : HOST + '/collect/addtrademark/?'+configUrl,
		dataType: 'jsonp',
		jsonp: 'jsoncallback',
		data:{'number':number, 'source' : source, 'ukey':ukey},
		success: function (data) { 
			callback(eval("("+data+")"));
		},
		error: function(){
			callback( {'type':2,'mess':'关注失败！'} );
		}
	})
	return true;
}
/****
* n ：商标号
* c ：商标类别
* source ：来源类别 1:交易；2：竞手
*/
function deleteCollectTrademark(number, source, callback){
	var isLogin = popUp(popupObj);
	if(isLogin == false){ return false; }
	var _data ;
	$.ajax({
		type : "GET",
		url  : HOST + '/collect/removetrademark/?'+configUrl,
		dataType: 'jsonp',
		jsonp: 'jsoncallback',
		data:{'number':number, 'source' : source, 'ukey':ukey},
		success: function (data) { 
			callback(data);
		},
		error: function(){
			callback( 0 );
		}
	})
	return true;
}

/****
* id ：申请人编号
* source ：来源类别 1:交易；2：竞手
*/
function collectProposer(id, source, callback){
	var isLogin = popUp(popupObj);

	if(isLogin == false){ return false; }
	$.ajax({
		type : "GET",
		url  : HOST + '/collect/addproposer/?'+configUrl,
		dataType: 'jsonp',
		async : true, 
		jsonp: 'jsoncallback',
		data:{'id': id, 'source' : source, 'ukey':ukey},
		success: function (data) {
			data = eval("("+data+")");
			callback(data);
		}
	})
}

/****
*获取关注的商标列表
* num :条数
* source ：来源类别 1:交易；2：竞手
*/
function userCollectList(num,callback){
	
	var isLogin = popUp(popupObj);
	var source = 1;
	if(isLogin == false){ return false; }
	$.ajax({
		type : "GET",
		url  : HOST + '/collect/userCollectList/?'+configUrl,
		dataType: 'jsonp',
		async : true, 
		jsonp: 'jsoncallback',
		data:{'num': num, 'source' : source, 'ukey': ukey},
		success: function (data) {
			callback( data );
		}
	})
}
/****
* 是否弹出层
*/
function popUp(obj,isup){
	if(isup){
		return true;	
	}
	if( !ukey ){
		$('#favorite_tid').val('');
		$('#proposer_id').val('');
		layer.open({
			type	: 1,
			title	: false,
			closeBtn: false,
			skin	: 'yourclass',
			offset	: ['32%', '32%'],
			content	: obj
		});
		$(".chaofan-mj-close").bind("click", function () {
			layer.closeAll();
		});
		return false;
	}	
	return true;
}