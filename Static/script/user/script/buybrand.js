var ucBuy = {
	//购买商标
	buyAdd : function(data,callback){
		var	ObjJsonp = '';
		var result = '';
		url 		 = ucConfig.setPostUrl(data);
		$.ajax({
			 type		: "post",
			 url		: HOST+"/buybrand/buyBrandAdd/?"+url,
			 dataType	: "jsonp",
			 async 		: false,
			 data		: data,
			 complete	: function(xhr,status){
				buyAddCallback(ObjJsonp,data);
			 },
			 error		: function(msg){
			 },
			 success	: function(json){
				ObjJsonp = json;
			 }
		});
	},
	/**
	* 是否购买商标
	* @param string num			商标号
	* @param string strclass	类别
	* @param string tel			电话
	* @param string datas		数据包
	*/
	isexist : function(num,strclass,tel,datas){
		var	ObjJsonp = '';
		url 		 = ucConfig.setPostUrl();
		$.ajax({
			 type		: "post",
			 url		: HOST+"/buybrand/buyExist/?"+url,
			 dataType	: "jsonp",
			 async 		: false,
			 data		: {"trademark" : num,"class" :　strclass,"tel" : tel},
			 complete	: function(xhr,status){
				isexistCallback(ObjJsonp,datas);
			 },
			 error		: function(msg){},
			 success	: function(json){
				ObjJsonp = json;
			 }
		});
	}
}; 
