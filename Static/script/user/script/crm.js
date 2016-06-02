var crm = {
	verifyCrmLog : function(){
		var	ObjJsonp 	= ucCode = '';
		crmid  = '100001';
		md5key = md5(crmkey+crmid);
		$.ajax({
			 type		: "post",
			 url		: HOSTCRM+"/Api/usercenter.php?id="+crmid+"&api_type=ucCrmCookie&api_user=usercenter&api_key="+md5key+configUrl,
			 dataType	: "jsonp",
			 async 		: false,
			 data		: {},
			 complete	: function(xhr,status){
				verifyCrmLogCallback(ObjJsonp);
			 },
			 error		: function(msg){},
			 success	: function(json){
				ObjJsonp = json;
			 }
		});
	},
	crmexit : function(){
		var	ObjJsonp 	= ucCode = ''; 
		crmexiturl 		= crmkey + "&isexit=1&crmseid="+getCookie(CRMSESSID);
		$.ajax({
			 type		: "post",
			 url		: HOSTCRM+"/Api/usercenter.php?api_type=ucCrmCookie&api_user=usercenter&api_key="+crmexiturl+configUrl,
			 dataType	: "jsonp",
			 async 		: false,
			 data		: {},
			 complete	: function(xhr,status){
				crmexitCallback(ObjJsonp);
			 },
			 error		: function(msg){},
			 success	: function(json){
				ObjJsonp = json;
			 }
		});
	}
}
$(document).ready(function(e) {
	
	var crmNum = 0;
	/*
	letop = setInterval(function(){
		//window.clearInterval(letop);
		//console.log(crmNum);
	},1);*/
	$(document).on('click','#chaofan-mj-logcrmexit',function(){
		crm.crmexit();
	});
});
function setCrmLogIn(){
	letop = setInterval(function(){
		crmUcNum 	= $('.chaofan-mj-crmuc').length;
		logInNum 	= $('#chaofan-mj-login').length;
		//console.log(crmUcNum);
		if(crmUcNum > 0 || logInNum > 0 ){
			window.clearInterval(letop);	
		}
		logHtml 	= ucConfig.setLogInHtml();
		ucConfig.setLogIn(logHtml);
		ucLoginTemp.setLoginTemp();
	},500);
}
function verifyCrmLogCallback(obj){
	//console.log(obj);
	$.each(obj,function(i,n){
		if(n['code'] == 1 && n['data']['crm_name']){
			addCrmCookie(n['data']['crm_name'],n['data']['crm_sessid']);
		}else{
			deledCrmCookie();
		}
	});
	setCrmLogIn();
}
function crmexitCallback(obj){
	$.each(obj,function(i,n){
		if(n['code'] == 1){
			deledCrmCookie();
			window.location.reload();
		}else{
			
		}
	});	
}

function addCrmCookie(str1,str2){
	addCookie(CRMNAME,str1,validTime);
	addCookie(CRMSESSID,str2,validTime);
}
function deledCrmCookie(){
	delCookie(CRMNAME);
	delCookie(CRMSESSID);
}