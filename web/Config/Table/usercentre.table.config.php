<?
$prefix		= 'my_';
$user		= 'user';

$dbId		= 'usercentre';
$configFile	= array( ConfigDir.'/Db/usercentre.master.config.php' );

$tbl['sessions'] = array(
	'name'		=> $prefix.'user_sessions',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);
$tbl['user'] = array(
	'name'		=> $prefix.'user',
	'dbId'		=> $dbId, 
	'configFile'=> $configFile,
);
$tbl['changelogs'] = array(
    'name'        => $prefix.'user_changelogs',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['loginlogs'] = array(
    'name'        => $prefix.'user_loginlogs',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);

$tbl['verify'] = array(
    'name'        => $prefix.'user_verify',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);


?>