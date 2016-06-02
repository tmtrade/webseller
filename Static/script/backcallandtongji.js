/*统计与回呼 头部*/

/*百度回呼*/
//document.write('<script type="text/javascript"  data-lxb-uid="1268165" data-lxb-gid="139337" src="http://lxbjs.baidu.com/api/asset/api.js?t=' + new Date().getTime() + '" charset="utf-8"></scr' + 'ipt>' );



/*点击免费通话提示框*/
$(function(){
	$("#telInput").focus(function(){
		$(".lxb-cb-tip").css({
			"display" : "block"
		});
	}).blur(function(){
		$(".lxb-cb-tip").css({
			"display" : "none"
		});
	});	
	//侧边与回呼
	$("#callBtn").on("click",function(){
		lxb.call(document.getElementById("telInput"));
	})
});


function consultation(){
    window.open("http://p.qiao.baidu.com/im/index?siteid=7918603&ucid=1268165");
}