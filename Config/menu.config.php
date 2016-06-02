<?
return array(
	0=> array(
			'name'	=>'用户中心',
			'icon'	=>'icon-user',
			'child'	=> array(
				array(
					'title'		=>'我的账户', 
					'url'		=>'/user/main/',
					'child'		=> array(
						'/user/main/',
						'/user/changePwd/',
						'/user/changeEmail/',
						'/user/changePhone/',
					)
				),
				array(
					'title'=>'消息中心', 
					'url'=>'/message/index/',
					'child'		=> array(
						'/message/views/',
					)
				),
			),
	),	
);
?>