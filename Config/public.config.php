<?
/**
 * 定义应用所需常量
 */
$checkhost 	= include(ConfigDir.'/checkhost.config.php');
$define = array(
	'qq_num' => 2950506044,
	'searchapi'	=> array(
		'key'	=> '89eb637c610f94b9d281c458bca42421',
		'url'	=> 'http://searchapi.chofn.net/trademark/search/',
	),
	'proposerApi'	=> array(
		'key'	=> '89eb637c610f94b9d281c458bca42421',
		'url'	=> 'http://tmsearch.chofn.api/proposer/search/',
	),
	'noticeApi'	=> array(
		'key'	=> '89eb637c610f94b9d281c458bca42421',
		'url'	=> 'http://tmsearch.chofn.api/notice/search/',
	),
	//1-45分类 显示
	'CLASSNEW' => array(
		'1'   => '01',
		'2'   => '02',
		'3'   => '03',
		'4'   => '04',
		'5'   => '05',
		'6'   => '06',
		'7'   => '07',
		'8'   => '08',
		'9'   => '09',
		'10'  => '10',
		'11'  => '11',
		'12'  => '12',
		'13'  => '13',
		'14'  => '14',
		'15'  => '15',
		'16'  => '16',
		'17'  => '17',
		'18'  => '18',
		'19'  => '19',
		'20'  => '20',
		'21'  => '21',
		'22'  => '22',
		'23'  => '23',
		'24'  => '24',
		'25'  => '25',
		'26'  => '26',
		'27'  => '27',
		'28'  => '28',
		'29'  => '29',
		'30'  => '30',
		'31'  => '31',
		'32'  => '32',
		'33'  => '33',
		'34'  => '34',
		'35'  => '35',
		'36'  => '36',
		'37'  => '37',
		'38'  => '38',
		'39'  => '39',
		'40'  => '40',
		'41'  => '41',
		'42'  => '42',
		'43'  => '43',
		'44'  => '44',
		'45'  => '45',
	),
	
    'SecondStatus' => array (
        1     => '申请中',
        2     => '已注册',
        3     => '商标已无效',
        4     => '驳回中',
        5     => '驳回复审中',
        6     => '部分驳回',
        7     => '公告中',
        8     => '异议中',
        9     => '异议复审中',
        10    => '需续展',
        11    => '续展中',
        12    => '开具优先权证明中',
        13    => '开具注册证明中',
        14    => '撤销中',
        15    => '撤销复审中',
        16    => '撤回撤销中',
        17    => '变更中',
        18    => '变更代理人中',
        19    => '补证中',
        20    => '补变转续证中',
        21    => '转让中',
        22    => '更正中',
        23    => '许可备案中',
        24    => '许可备案变更中',
        25    => '删减商品中',
        26    => '冻结中',
        27    => '注销中',
        28    => '无效宣告中',
        ),

	'TRADEMARK_NAME'	=> '商标查询',
	'WEBSITE_URL'		=> 'http://t.chofn.net/', //一只蝉地址
	'MANAGER_URL'		=> 'http://i.chofn.net/', //商标管家
	'SEARCH_URL'		=> 'http://shansoo.net/', //近似查询
    'YIZHCHAN_URL'		=> 'http://t.chofn.net/', //交易平台

	'PUBLIC_USER'		=> 'chaofancookid',//公用用户登录信息标识
	'PUBLIC_TIME'		=> '36000',//登录过期时间(秒)
	'CODE_TIME'			=> '600',//验证码过期时间(秒)
	'MSG_TEMPLATE' => array(
		'valid'     	=> "验证码：%s，有效期为10分钟，请尽快使用。退订回N",
		'register'  	=> "%s（登录密码），系统已为您开通手机账户，登陆可查看求购进展，工作人员不会向你索要，请勿向任何人泄露。退订回N",
		'newValid'  	=> "%s（动态登录密码），仅本次有效，请在10分钟内使用。工作人员不会向你索要，请勿向任何人泄露。退订回N",
		'validBind' 	=> "%s（手机绑定校验码），仅本次有效请在10分钟内使用。工作人员不会向你索要，请勿向任何人泄露。如非本人操作，请忽略。退订回N",
		'weixin'     	=> "%s（微信解绑校验码），有效期为5分钟，请尽快使用。退订回N",
	),
	'MSG_TEMPLATEID' => array(
		'valid'     	=> "848",
		'register'  	=> "849",
		'newValid'  	=> "850",
		'validBind' 	=> "851",
		'weixin'     	=> "852",
	),
	'FOLLOW'		=> array(1=>'商标' , 2=>'专利', 3=>'版权',4=>'其他'),
	'CRMTYPE'		=> array(
					1 => '国内商标',
					2 => '国际商标', 
					3 => '国内专利', 
					4 => '国际专利', 
					//5 => '商标转让', 
					6 => '版权信息', 
					7 => '专利转让', 
					8 => '法律信息', 
					9 => '高新科技',
					10 => '贯标',
					),
	'CRMSTEP'		=> array(
					1 => '已提交',
					2 => '已受理', 
					3 => '已分配', 
					4 => '已处理', 
					),
	'CRMSTATE'		=> array(
					1 => '洽谈中',
					2 => '已匹配', 
					3 => '已成交', 
					4 => '已立案', 
					5 => '交易关闭', 
					), 

);
if(is_array($checkhost)){
	$define = array_merge($define,$checkhost);	
}

return $define;

?>