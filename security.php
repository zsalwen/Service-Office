<? if (!$_COOKIE[psdata][user_id] || $_COOKIE[psdata][level] != "Operations" ){ 
error_log(date('h:iA j/n/y')." SECURITY PREVENTED ACCESS to ".$_SERVER['SCRIPT_NAME']." by ".$_SERVER["REMOTE_ADDR"]."\n", 3, '/logs/user.log');
header ('Location: http://staff.mdwestserve.com'); } ?>