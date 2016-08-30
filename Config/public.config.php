<?

/**
 * 定义应用所需常量
 */
$checkhost = include(ConfigDir . '/checkhost.config.php');
$define = array(
    'HTMLTOPDF' 	=> '/usr/bin/wkhtmltopdf',//pdf生成软件位置
    'HTMLTOIMG' 	=> '/usr/bin/wkhtmltoimage',//image生成软件位置
    'qq_num' => 3406987180,
 //    'searchapi' => array(
    // 'key' => '89eb637c610f94b9d281c458bca42421',
    // 'url' => 'http://searchapi.chofn.net/trademark/search/',
 //    ),
 //    'proposerApi' => array(
    // 'key' => '89eb637c610f94b9d281c458bca42421',
    // 'url' => 'http://tmsearch.chofn.api/proposer/search/',
 //    ),
 //    'noticeApi' => array(
    // 'key' => '89eb637c610f94b9d281c458bca42421',
    // 'url' => 'http://tmsearch.chofn.api/notice/search/',
 //    ),
    //1-45分类 显示
    'CLASSNEW' => array(
        '1' => '01',
        '2' => '02',
        '3' => '03',
        '4' => '04',
        '5' => '05',
        '6' => '06',
        '7' => '07',
        '8' => '08',
        '9' => '09',
        '10' => '10',
        '11' => '11',
        '12' => '12',
        '13' => '13',
        '14' => '14',
        '15' => '15',
        '16' => '16',
        '17' => '17',
        '18' => '18',
        '19' => '19',
        '20' => '20',
        '21' => '21',
        '22' => '22',
        '23' => '23',
        '24' => '24',
        '25' => '25',
        '26' => '26',
        '27' => '27',
        '28' => '28',
        '29' => '29',
        '30' => '30',
        '31' => '31',
        '32' => '32',
        '33' => '33',
        '34' => '34',
        '35' => '35',
        '36' => '36',
        '37' => '37',
        '38' => '38',
        '39' => '39',
        '40' => '40',
        '41' => '41',
        '42' => '42',
        '43' => '43',
        '44' => '44',
        '45' => '45',
    ),
    'SecondStatus' => array(
        1 => '申请中',
        2 => '已注册',
        3 => '商标已无效',
        4 => '驳回中',
        5 => '驳回复审中',
        6 => '部分驳回',
        7 => '公告中',
        8 => '异议中',
        9 => '异议复审中',
        10 => '需续展',
        11 => '续展中',
        12 => '开具优先权证明中',
        13 => '开具注册证明中',
        14 => '撤销中',
        15 => '撤销复审中',
        16 => '撤回撤销中',
        17 => '变更中',
        18 => '变更代理人中',
        19 => '补证中',
        20 => '补变转续证中',
        21 => '转让中',
        22 => '更正中',
        23 => '许可备案中',
        24 => '许可备案变更中',
        25 => '删减商品中',
        26 => '冻结中',
        27 => '注销中',
        28 => '无效宣告中',
    ),
    // 'TRADEMARK_NAME' => '商标查询',
    // 'WEBSITE_URL' => 'http://t.chofn.net/', //一只蝉地址
    // 'MANAGER_URL' => 'http://i.chofn.net/', //商标管家
    // 'SEARCH_URL' => 'http://shansoo.net/', //近似查询
    // 'YIZHCHAN_URL' => 'http://t.chofn.net/', //交易平台
    'PUBLIC_USER' => 'chaofancookid', //公用用户登录信息标识
    'PUBLIC_TIME' => '36000', //登录过期时间(秒)
    'CODE_TIME' => '600', //验证码过期时间(秒)
    'MSG_TEMPLATE' => array(
        'valid' => "验证码：%s，有效期为10分钟，请尽快使用。退订回N",
        'register' => "%s（登录密码），系统已为您开通手机账户，登陆可查看求购进展，工作人员不会向你索要，请勿向任何人泄露。退订回N",
        'newValid' => "%s（动态登录密码），仅本次有效，请在10分钟内使用。工作人员不会向你索要，请勿向任何人泄露。退订回N",
        'validBind' => "%s（手机绑定校验码），仅本次有效请在10分钟内使用。工作人员不会向你索要，请勿向任何人泄露。如非本人操作，请忽略。退订回N",
        'weixin' => "%s（微信解绑校验码），有效期为5分钟，请尽快使用。退订回N",
    ),
    'MSG_TEMPLATEID' => array(
        'valid' => "848",
        'register' => "849",
        'newValid' => "850",
        'validBind' => "851",
        'weixin' => "852",
    ),
    'FOLLOW' => array(1 => '商标', 2 => '专利', 3 => '版权', 4 => '其他'),
    'CRMTYPE' => array(
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
    'CRMSTEP' => array(
        1 => '已提交',
        2 => '已受理',
        3 => '已分配',
        4 => '已处理',
    ),
    'CRMSTATE' => array(
        1 => '洽谈中',
        2 => '已匹配',
        3 => '已成交',
        4 => '已立案',
        5 => '交易关闭',
    ),
    'ADCONFIG' => array(//广告配置项
    '1' => array(
        "name" => "首页轮播广告",
        "count" => "10",
        "amount" => "40",
        "pic" => "exp-pic.png",
        "note" => "首页展示，一目了然。有好的商标需要快速出售么？放上来吧 :)",
    ),
    '2' => array(
        "name" => "通用分类广告",
        "count" => "36",
        "amount" => "30",
        "pic" => "exp-pic1.png",
        "note" => "通用下拉菜单分类处广告，买家在一只蝉所有页面都有机会看到 ^_^",
    ),
    '3' => array(
        "name" => "列表页右侧浮动广告",
        "count" => "20",
        "amount" => "20",
        "pic" => "exp-pic2.png",
        "note" => "买家在列表页筛选商品时，一眼就可以看到右侧的广告噢 :)",
    ),
    '4' => array(
        "name" => "商标美化",
        "count" => "50",
        "amount" => "10",
        "pic" => "exp-pic3.png",
        "note" => "想提升商标“颜值”，一眼抓住买家的心？快申请，我帮您 ^_^",
    ),
    '5' => array(
        "name" => "专题聚合页",
        "count" => "2",
        "amount" => "50",
        "pic" => "exp-pic4.png",
        "note" => "多个商标，打包推荐！还赠送美美哒展示页面 :)",
    ),
    ),
    //来源渠道
    'SOURCE' => array(
        '1' => '管家',
        '2' => '顾问',
        '4' => '展示页',
        '5' => '引导页',
        '6' => '400',
        '7' => '乐语',
        '8' => '百度商桥',
        '3' => '其他',
        '9' => '同行',
        '10' => '一只蝉',
        '11' => '用户中心',
        '12' => '出售者平台',
    ),
);
if (is_array($checkhost)) {
    $define = array_merge($define, $checkhost);
}

return $define;
?>