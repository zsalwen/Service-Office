<?
function dbIN($str){
$str = trim($str);
$str = str_replace('\'','',$str);
$str = strtolower($str);
$str = ucwords($str);
return $str;
}

// ok this one is gonna be tricky
mysql_connect();
mysql_select_db('joomla');
// let's do the contacts first
// select all contacts that have [ password, name, email ]
$r=@mysql_query("select email, name, password from contacts where password <> '' AND name <> '' AND email <> ''");
$update=0;
$skip=0;
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$email=trim($d[email]);
	@mysql_query("update contacts set joomla = '$email' where email='$email' and joomla = ''");
	// check jos_users for email address, if not found import user
	$rTest=@mysql_query("select id from jos_users where email='$email'");
	if(!$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC)){
		$update++;
		// insert into user table
		echo "<li>$d[name] using $d[email]</li>";
		@mysql_query("insert into jos_users (name, username, email, password, usertype, registerDate ) values ('".dbIN($d[name])."','$d[email]','$d[email]','".md5($d[password])."', 'Registered', NOW() ) ");
	}else{
		$skip++;
	}
}
// now do the contractors first
// select all contacts that have [ password, name, email ]
$r=@mysql_query("select email, name, password from ps_users where password <> '' AND name <> '' AND email <> ''");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$email=trim($d[email]);
	@mysql_query("update ps_users set joomla = '$email' where email='$email' and joomla = ''");
	// check jos_users for email address, if not found import user
	$rTest=@mysql_query("select id from jos_users where email='$email'");
	if(!$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC)){
		$update++;
		// insert into user table
		echo "<li>$d[name] using $d[email]</li>";
		@mysql_query("insert into jos_users (name, username, email, password, usertype, registerDate ) values ('".dbIN($d[name])."','$d[email]','$d[email]','".md5($d[password])."', 'Registered', NOW() ) ");
	}else{
		$skip++;
	}
}
echo "$update jos_users created, $skip alerady created.";
//
//
// step 2 - jos_core_acl_aro
//
//
$update=0;
$skip=0;
$r=@mysql_query("select name, id from jos_users");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$rTest=@mysql_query("select id from jos_core_acl_aro where value='$d[id]'");
	if(!$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC)){
		$update++;
		// insert into acl table
		echo "<li>$d[name] # $d[id]</li>";
		@mysql_query("insert into jos_core_acl_aro (section_value, value, order_value, name, hidden ) values ('users','$d[id]','0','$d[name]', '0' ) ");
	}else{
		$skip++;
	}
}
echo "$update acl's created, $skip alerady created.";
//
//
// step 3 - jos_core_acl_groups_aro_map
//
//
$update=0;
$skip=0;
$r=@mysql_query("select id from jos_core_acl_aro");
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$rTest=@mysql_query("select aro_id from jos_core_acl_groups_aro_map where aro_id='$d[id]'");
	if(!$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC)){
		$update++;
		// insert into acl table
		echo "<li>#$d[id]</li>";
		@mysql_query("insert into jos_core_acl_groups_aro_map (group_id, section_value, aro_id ) values ('18','','$d[id]' ) ");
	}else{
		$skip++;
	}
}
echo "$update acl maps created, $skip alerady created.";


?>







