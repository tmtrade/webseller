<?
$prefix		= 'tm_';
$dbId		= 'trademarkonline';
$configFile	= array( ConfigDir.'/Db/tm.master.config.php' );

$tbl['trademark'] = array(
	'name'		=> $prefix.'trademark',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);

?>