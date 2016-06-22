/***option string***/
 $(function(){
	   $(".styled option").each(function(i){
		if($(this).text().length>1){
			$(this).attr("title",$(this).text());
			var text=$(this).text().substring(0,14);
			$(this).text(text);
		}
	})
  });