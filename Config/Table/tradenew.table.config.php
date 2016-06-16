<?
$prefix		= 't_';
$dbId		= 'tradenew';
$configFile	= array( ConfigDir.'/Db/tradenew.master.config.php' );

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
$tbl['salehistory'] = array(
	'name'		=> $prefix.'user_sale_history',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);
$tbl['messege'] = array(
	'name'		=> $prefix.'messege',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['messegeMonitor'] = array(
	'name'		=> $prefix.'messege_monitor',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);

$tbl['messegeUser'] = array(
	'name'		=> $prefix.'messege_user',
	'dbId'		=> $dbId,
	'configFile'=> $configFile,
);
?>