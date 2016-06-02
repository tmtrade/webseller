<?
$prefix		= 'cdb_';
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
$tbl['message'] = array(
    'name'        => $prefix.'user_message',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['template'] = array(
    'name'        => $prefix.'user_template',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);

$tbl['relation'] = array(
    'name'        => $prefix.'user_relation',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['verify'] = array(
    'name'        => $prefix.'user_verify',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['collect'] = array(
    'name'        => $prefix.'user_collect',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);

$tbl['usercollect'] = array(
    'name'        => 'userCollect',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['userproposer'] = array(
    'name'        => $prefix.'proposer',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);

$tbl['newtrade'] = array(
    'name'        => $prefix.'new_trademark',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);


$tbl['collectstatus'] = array(
    'name'        => $user.'CollectStatus',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['collectstatus'] = array(
    'name'        => $user.'CollectStatus',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['mytrademark'] = array(
    'name'        => $user.'MyTrademark',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['mytradestatus'] = array(
    'name'        => $user.'MytradeStatus',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);

$tbl['document'] = array(
    'name'        => $prefix.'document',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['staffjudge'] = array(
    'name'        => $prefix.'staff_judge',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['changeprice'] = array(
    'name'        => $prefix.'change_price',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['browse'] = array(
    'name'        => $prefix.'user_browse',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['interface'] = array(
    'name'        => $prefix.'interface',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['buybrand'] = array(
    'name'        => $prefix.'buy_trademark',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);
$tbl['userdeal'] = array(
    'name'        => $prefix.'user_deal',
    'dbId'        => $dbId, 
    'configFile'=> $configFile,
);

?>