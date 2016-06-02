<?
//Memcache数据缓存
$configs[] = array(
'id'        => 'mem',
'enable'    => true,
'source'    => LibDir.'/Util/Cache/MmCache.php',
'className' => 'MmCache',
'import'    => array(LibDir.'/Util/Cache/ICache.php'),
'property'  => array(
    'expire'     => 1800,
	'configFile' => ConfigDir.'/memcache.config.php',
	'objRef'	 => array('encoding' => 'encoding'),
));

//全文索引客户端代理
$configs[] = array(
'id'        => 'sphinx',
'enable'    => true,
'source'    => LibDir.'/Util/Tool/SphinxClientProxy.php',
'className' => 'SphinxClientProxy',
'property'  => array(
	'objRef' => array('sphinxClient' => 'sphinxClient'),
));

//全文索引客户端
$configs[] = array(
'id'        => 'sphinxClient',
'enable'    => true,
'source'    => LibDir.'/Util/Tool/SphinxClient.php',
'className' => 'SphinxClient',
'property'  => array(
	'_host'  => '127.0.0.1',
	'_port'  => 9312,
	'_arrayresult' => true,
	'_timeout' => 1,
));
?>