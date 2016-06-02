<?
//内部查询配置
return array(

	//内部查询接口
	'workers'    => array(
		'1'  => 'FullMatchWorker'       ,
		'2'  => 'ReplaceAnyCharWorker'  ,
		'3'  => 'TranslateWorker'       ,
		'4'  => 'SubStringWorker'       ,
		'5'  => 'PinyinWorker'          ,
		'6'  => 'AdjacentCombineWorker' ,
		'9'  => 'PrefixSuffixWorker'    ,
		'10' => 'AnyCombineWorker'      ,
		),

	//接口返回数据最大条数
	'maxNum' => array(
		'FullMatchWorker'       => 200,
		'ReplaceAnyCharWorker'  => 4000,
		'TranslateWorker'       => 20,
		'SubStringWorker'       => 1000,
		'PinyinWorker'          => 1000,
		'AdjacentCombineWorker' => 1000,
		'PrefixSuffixWorker'    => 500,
		'AnyCombineWorker'      => 500,
		),
	);
?>