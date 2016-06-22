$(function(){
	$(".close").click(function(){
		$('.sid').val(0);
	});
	$(".evaluate").click(function(){
		sidVal = $(this).data('sid');
		$('.gwsid').html(sidVal);
	});
	var isPost = true;
	$('.yjpost').click(function(){
		if( isPost == false ){
			return false;	
		}
		contentVal 	= $('.content').val();
		istrade		= $('.istrade').attr('checked');
		sid			= $.trim($('.gwsid').html());
		if(!contentVal){
			layter_msg('请输入评价内容');
			return false;	
		}
		istradeVal	= istrade ? 1 : 0;
		$.ajax({
			 type		: "post",
			 url		: "/user/myStaffSubmit/",
			 dataType	: "json",
			 data		: {"content" : contentVal,"istrade" : istradeVal,"sid" : sid},
			 error		: function(msg){
				layter_msg('系统繁忙请稍后在试');
			 },
			 success	: function(data){
				isPost = false;
				layter_msg(data.msg);
				setTimeout(function(){
					$('.content').val('');
					isPost = true;
				},3000);
			 }
		});
	});
	//跳转到更多页面
	$('.counselorsurl').on('click',function(){
		window.location = '/user/mystaffmore/';	
	});
});
