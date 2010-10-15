<?
function attorneyCustomLang($att,$str){
	$r=@mysql_query("SELECT * FROM ps_str_replace where attorneys_id = '$att'");
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)){
		if ($d['str_search'] && $d['str_replace'] && $str && $att){
			$str = str_replace($d['str_search'], strtoupper($d['str_replace']), $str);
			$str = str_replace(strtoupper($d['str_search']), strtoupper($d['str_replace']), $str);
			//echo "<script>alert('Replacing ".strtoupper($d['str_search'])." with ".strtoupper($d['str_replace']).".');< /script>";
		}
	}
	return $str;
}


function initals($str){
	$str = explode(' ',$str);
	return strtoupper(substr($str[0],0,1).substr($str[1],0,1).substr($str[2],0,1).substr($str[3],0,1));
}
function id2server($id){
	$q=@mysql_query("SELECT name from ps_users where id='$id'") or die(mysql_error());
	$d=mysql_fetch_array($q, MYSQL_ASSOC);
	return initals($d[name]);
}
function print4100($string){
/*
	$fh = fopen("printer.tmp", 'w') or die("can't open file");
	fwrite($fh, $string);
	fclose($fh);
	system("lp -d LaserJet printer.tmp");*/
}
function psActivity($field){
	@mysql_query("insert into psActivity (today) values (NOW())");
	$r=@mysql_query("select * from psActivity where today='".date('Y-m-d')."'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$count=$d[$field]+1;
	@mysql_query("update psActivity set $field = '$count' where today='".date('Y-m-d')."'");
}

function db_connect($host,$database,$user,$password){ 
	@mysql_connect();
	mysql_select_db ('core');
	return mysql_error();
}
function getPageTitle($template){
	$q="select title from help_templates where name='$template' order by id desc";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return stripslashes($d[title]);
}

function monitor($str){
if ($_COOKIE[psdata][elephant]){
	$str = '<li>'.date('h:i').' '.addslashes($str).'</li>';
 echo '<script>window.open(\'monitor.php?str='.$str.'\',\'monitor\',\'width=400,height=800,toolbar=no,statusbar=no,location=no\');</script>';
 //echo '<script>alert(\''.$str.'\');< /script>';
}	
}
function attorneysList($current){
	$q = "select * from attorneys where attorneys_id = '$current'";
	$r = @mysql_query($q) or die(mysql_error());
	$d = mysql_fetch_array($r, MYSQL_ASSOC);
		$option = "<option value='$d[attorneys_id]'>$d[display_name]</option>";
	$q = "select * from attorneys order by display_name";
	$r = @mysql_query($q) or die(mysql_error());
	while ($choice = mysql_fetch_array($r, MYSQL_ASSOC)){
		$option .= "<option value='$choice[attorneys_id]'>$choice[display_name]</option>";
	}
	return $option;
}

function id2name($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}
function id2name2($id){
	$q="SELECT name FROM users WHERE user_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}
function id2phone($id){
	$q="SELECT phone FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[phone];
}
function id2csz($id){
	$q="SELECT city, state, zip FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[city].', '.$d[state].' '.$d[zip];
}
function id2zip($id){
	$q="SELECT zip FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[zip];
}
function id2user($id){
	$q="SELECT name FROM users WHERE user_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[name];
}

function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[display_name];
}

function countyList($current){
	$option = "<option>$current</option>";

	$q="SELECT * FROM county";
	$r=@mysql_query($q);
	while($d=mysql_fetch_array($r, MYSQL_ASSOC)){;
		$option .= "<option>$d[name]</option>";
	}
	return $option;
}
// this is important code 
// this is important code 
// this is important code 
// this is important code 
// this is important code 
// this is important code 
function hardLog($str,$type){
	if ($type == "user"){
		$log = "/logs/user.log";
	}
	if ($type == "contractor"){
		$log = "/logs/contractor.log";
	}
	if ($type == "debug"){
		$log = "/logs/debug.log";
	}
	// this is important code 
	// this is important code 
	// this is important code 
	if ($log){
		error_log(date('h:iA n/j/y')." ".$_COOKIE[psdata][name]." ".$_SERVER["REMOTE_ADDR"]." ".trim($str)."\n", 3, $log);
	}
	// this is important code 
}
// this is important code 
// this is important code 
// this is important code 
// this is important code 
// this is important code 
// this is important code 
// this is important code 
// this is important code 


function logAction($userid, $page, $action){
	$q="INSERT into ps_log(user_id, page, action, log_stamp) values ('$userid', '$page', '$action', NOW())";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());

}


function error_out($error){
	@mysql_query("INSERT INTO error_out (page, browser, ip_addy, ip_proxy, error_str, error_date) values ('$_SERVER[PHP_SELF] $_SERVER[QUERY_STRING]', '$_SERVER[HTTP_USER_AGENT]', '$_SERVER[REMOTE_ADDR]', '$_SERVER[HTTP_X_FORWARDED_FOR]', '$error', NOW())");
}

function note($file_id, $note){
	$q="INSERT into ps_notes(file_id, user_id, note_date, note) values ('$file_id', '$_COOKIE[user_id]', NOW(), '$note')";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
}

function hwaLog($id, $note){
	$q="SELECT hwa_log FROM ps_packets";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[hwa_log]){
	$new_note = $d[hwa_log]."<br>".date('m/d/Y g:ia').":".addslashes($note);
	}else{
	$new_note = "<br>".addslashes($note);
	}
	$q="UPDATE ps_packets set hwa_log = '$new_note' where packet_id = '$id'";
	$r=@mysql_query($q);
}

function cleanup($string){
$string = addslashes($string);
$string = strtoupper($string);
return $string;
}

function row_color($i,$bg1,$bg2){
    if ( $i%2 ) {
        return $bg1;
    } else {
        return $bg2;
    }
}

function mkcountylist($current){
	$q="SELECT * FROM county";
	$r=@mysql_query($q);
	if ($current){
		$option = "<option>$current</option>";
	}
	while($d=mysql_fetch_array($r, MYSQL_ASSOC)){;
		$option .= "<option>$d[name]</option>";
	}
	return $option;
}

function image($url,$height,$width){
if ($url){
return "<a href='http://$url' target='_Blank'><img src='http://$url' height='$height' width='$width' border='0' /></a>";
}else{
return "<img src='http://portal.hwestauctions.com/images/nopic.jpg' height='$height' width='$width' />";
}}

function getTemplate($template){
	$q="select template from ps_templates where name='$template' order by id desc";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return stripslashes($d[template]);
}

function getTemplates($template){ // version history ?
$q="select *, DATE_FORMAT(saved, '%W, %M %D at %r') as saved from ps_templates where name='$template' order by id desc";
$r=@mysql_query($q);
$table = "<table>";
while($d=mysql_fetch_array($r, MYSQL_ASSOC)){
$table .= "<tr><td>$d[saved]</td>";

$table .= "<td> By ".id2name($d[user_id])."</td><td><a href='?page=templates&id=$d[id]'>{Load into editor}</a></td></tr>";

}
$table .= "</table>";
return $table;
}
function getTemplateDate($template){ // version history ?
$q="select *, DATE_FORMAT(saved, '%W, %M %D at %r') as saved_f from ps_templates where name='$template' order by id desc";
$r=@mysql_query($q);
$d=mysql_fetch_array($r, MYSQL_ASSOC);
$date[0] = $d[saved];
$date[1] = $d[saved_f];
return $date;
}

function cleanField($str){
	$str = explode('_',$str);
	$str1 = ucwords($str[0]);
	$str2 = ucwords($str[1]);
return $str1.' '.$str2;
}

function ps2county($str){
	$q="SELECT county_name FROM ps_county WHERE ps_name = '$str'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[county_name];
}

function county2ps($str){
	$q="SELECT ps_name FROM ps_county WHERE county_name = '$str'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[ps_name];
}

function serverList($county){
	$q= "select * from ps_users where contract = 'YES' AND $county > '0' AND (level = 'Green Member' OR level = 'Gold Member' OR level = 'Platinum Member')";
	$r=@mysql_query($q);
	while ($d=mysql_fetch_array($r, MYSQL_ASSOC)) {
       	if($d[company]){
	   		$option .= "<option value='$d[id]'>$d[name] with $d[company]</option>";
	   	}else{
	   		$option .= "<option value='$d[id]'>$d[name]</option>";
   		}
	} 
	return $option;
}

function getPage($url, $referer, $timeout, $header){
 
	if(!isset($timeout))
        $timeout=30;
    $curl = curl_init();
    if(strstr($referer,"://")){
        curl_setopt ($curl, CURLOPT_REFERER, $referer);
    }
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
    curl_setopt ($curl, CURLOPT_HEADER, (int)$header);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $html = curl_exec ($curl);
    curl_close ($curl);
    return $html;
	
}

if (getenv(HTTP_X_FORWARDED_FOR)) {							
    $ip = getenv(HTTP_X_FORWARDED_FOR); 
} else { 
    $ip = getenv(REMOTE_ADDR);
}	

/*
function smtpMail($t,$subject,$html){
	define('DISPLAY_XPM4_ERRORS', true); 
	require_once '/opt/lampp/htdocs/smtp/SMTP.php'; 
	$f = "service@hwestauctions.com";
	$m = 'From: '.$f."\r\n".
		 'To: '.$t."\r\n".
		 'Subject: '.$subject."\r\n".
		 'Content-Type: text/html'."\r\n\r\n".
		 '<body>'.$html.'</body>';
	//$h = explode('@', $t); 
	$c = SMTP::MXconnect('mail.hwestauctions.com'); 
	$s = SMTP::Send($c, array($t), $m, $f); 
}
*/

function smtpMail($t,$subject,$html){
//	error_reporting(E_ALL); 
	define('DISPLAY_XPM4_ERRORS', false); 
	require_once '/opt/lampp/htdocs/smtp/SMTP.php';
	$f = 'service@hwestauctions.com';
	$user = 'pmcguire@hwestauctions.com';
	$p = 'patrick';
	$m = 'From: '.$f."\r\n".
		 'To: '.$t."\r\n".
		 'Subject: '.$subject."\r\n".
		 'Content-Type: text/html'."\r\n\r\n".
		 '<body>'.$html.'</body>';
	$c = fsockopen('mail.hwestauctions.com', 25, $errno, $errstr, 20) or die($errstr);
	if (!SMTP::recv($c, 220)) die(print_r($_RESULT));
	if (!SMTP::ehlo($c, 'delta.mdwestserve.com')) SMTP::helo($c, 'delta.mdwestserve.com') or die(print_r($_RESULT));
	if (!SMTP::auth($c, $user, $p, 'login')) SMTP::auth($c, $user, $p, 'plain') or die(print_r($_RESULT));
	SMTP::from($c, $f) or die(print_r($_RESULT));
	SMTP::to($c, $t) or die(print_r($_RESULT));
	SMTP::data($c, $m) or die(print_r($_RESULT));
	SMTP::quit($c);
	@fclose($c);
}
function color(){
	$color[0] = "00";
	$color[1] = "33";
	$color[2] = "66";
	$color[3] = "99";
	$color[4] = "cc";
	$color[5] = "ff";
	$a = rand(2,5);
	$b = rand(1,5);
	$c = rand(1,5);
	$color = $color[$a].$color[$b].$color[$c];
	return $color;
}

function mouseover(){ return "onmouseover=\"style.backgroundColor='#cc0000';\" onmouseout=\"style.backgroundColor='#3C3C3C';\"  bgcolor=\"#3C3C3C\"";}

function getLnL($address){
/*$address = str_replace(' ','+',$address);
$key = "ABQIAAAA8yH4sz3KTLMIhZ9V81HVqBQso08lYJ1q7ZFMltqpfDEr9X0BYxR_WOQKemPMetn4D8Tb4vFgyMtEjA";
   $curl = curl_init();
   curl_setopt ($curl, CURLOPT_URL, "http://maps.google.com/maps/geo?q=$address&output=csv&key=$key");
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   $result = curl_exec ($curl);
   curl_close ($curl);
   $data = explode(',',$result);
   return $data;*/
}
function workStatus($server){
$today=date('Y-m-d');
$r=@mysql_query("select * from ps_users where id='$server' and workRequestDate='$today'");
$d=mysql_fetch_array($r, MYSQL_ASSOC);
if ($d[workRequestDate]){
return "<div style='font-size:18px; background-color:#FFFFFF; text-align:center; border-bottom:solid 1px;'><strong>".id2name($server)."</strong>'s active files will be completed in <strong>$d[workRequestDone] days</strong>. Currently accepting up to <strong>$d[workRequestMax] new files</strong>.</div>";
}
if ($server == $_COOKIE[psdata][user_id] && !$d[workRequestDate] && $_COOKIE[psdata][level] != "Operations"){
return "<div style='font-size:18px; background-color:#FF0000; text-align:center;'>You are <strong>NOT</strong> requesting any new work. Click <a href='status.php' style='color:FFFFFF'>HERE</a> to request work.</div>";
}
}
function isServer($server,$packet){
	$r=@mysql_query("select server_id from ps_packets where packet_id='$packet' and (server_id='$server' OR server_ida='$server' OR server_idb='$server' OR server_idc='$server' OR server_idd='$server' OR server_ide='$server')");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d){
		return 1;
	}else{
		return 0;
	}
}
function ev_isServer($server,$packet){
	$r=@mysql_query("select server_id from ps_packets where packet_id='$packet' and (server_id='$server' OR server_ida='$server' OR server_idb='$server' OR server_idc='$server' OR server_idd='$server' OR server_ide='$server')");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d){
		return 1;
	}else{
		return 0;
	}
}


?>