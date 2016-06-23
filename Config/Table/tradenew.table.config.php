<?
$prefix		= 't_';
$prefix2	= 's_';
$dbId		= 'tradenew';
$configFile	= array( ConfigDir.'/Db/tradenew.master.config.php' );

$tbl['income'] = array(
	'name'		=> $prefix.'income',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
$tbl['sale'] = array(
	'name'		=> $prefix.'sale',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);
$tbl['salecontact'] = array(
	'name'		=> $prefix.'sale_contact',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);
$tbl['saleTminfo'] = array(
	'name'		=> $prefix.'sale_tminfo',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);
$tbl['salehistory'] = array(
	'name'		=> $prefix.'sale_history',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

$tbl['userSaleHistory'] = array(
	'name'		=> $prefix.'user_sale_history',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patent'] = array(
	'name'		=> $prefix.'patent',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patentContact'] = array(
	'name'		=> $prefix.'patent_contact',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patentInfo'] = array(
	'name'		=> $prefix.'patent_info',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['patentList'] = array(
	'name'		=> $prefix.'patent_list',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['messege'] = array(
	'name'		=> $prefix2.'messege',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['messegeMonitor'] = array(
	'name'		=> $prefix2.'messege_monitor',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['messegeUser'] = array(
	'name'		=> $prefix2.'messege_user',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
?>