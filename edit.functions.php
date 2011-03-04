<?
// collected from common
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
function normalize_special_characters( $str ){
    # Quotes cleanup
    $str = ereg_replace( chr(ord("`")), "'", $str );        # `
    $str = ereg_replace( chr(ord("�")), "'", $str );        # �
    $str = ereg_replace( chr(ord("�")), ",", $str );        # �
    $str = ereg_replace( chr(ord("`")), "'", $str );        # `
    $str = ereg_replace( chr(ord("�")), "'", $str );        # �
    $str = ereg_replace( chr(ord("�")), "\"", $str );        # �
    $str = ereg_replace( chr(ord("�")), "\"", $str );        # �
    $str = ereg_replace( chr(ord("�")), "'", $str );        # �

    $unwanted_array = array(    '�'=>'S', '�'=>'s', '�'=>'Z', '�'=>'z', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'E',
                                '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I', '�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U',
                                '�'=>'U', '�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'c',
                                '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'i', '�'=>'i', '�'=>'i', '�'=>'i', '�'=>'o', '�'=>'n', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o',
                                '�'=>'o', '�'=>'o', '�'=>'u', '�'=>'u', '�'=>'u', '�'=>'y', '�'=>'y', '�'=>'b', '�'=>'y' );
    $str = strtr( $str, $unwanted_array );

    # Bullets, dashes, and trademarks
    $str = ereg_replace( chr(149), "&#8226;", $str );    # bullet �
    $str = ereg_replace( chr(150), "&ndash;", $str );    # en dash
    $str = ereg_replace( chr(151), "&mdash;", $str );    # em dash
    $str = ereg_replace( chr(153), "&#8482;", $str );    # trademark
    $str = ereg_replace( chr(169), "&copy;", $str );    # copyright mark
    $str = ereg_replace( chr(174), "&reg;", $str );        # registration mark

    return $str;
} 



// collected from order
function webservice($clientFile){
	$select_query = "Select create_id From defendants  Where filenumber = '$clientFile'";
	$result = mysql_query($select_query);
	$data = mysql_fetch_array($result,MYSQL_ASSOC);
	if ($data[create_id]) {
		return true;
	}
}
function byteConvert(&$bytes){
        $b = (int)$bytes;
        $s = array('B', 'kB', 'MB', 'GB', 'TB');
        if($b < 0){
            return "0 ".$s[0];
        }
        $con = 1024;
        $e = (int)(log($b,$con));
        return '<b>'.number_format($b/pow($con,$e),0,',','.').' '.$s[$e].'</b>'; 
}

function testLink($file){ //http://mdwestserve.com/portal/PS_PACKETS/October 06 2008 09:42:08.-08-128315F.pdf/08-128315F.pdf
	$file = str_replace('http://mdwestserve.com/portal/PS_PACKETS/','/data/service/orders/',$file);
	$file = str_replace('http://mdwestserve.com/PS_PACKETS/','/data/service/orders/',$file);
	if(file_exists($file)){
		$size = filesize($file);
		return byteConvert($size);
	}else{
		return "otd not found";
	}
}

function dupCheck($string){
	$r=@mysql_query("select * from ps_packets where client_file LIKE '%$string%'");
	$c=mysql_num_rows($r);
	if ($c == 1){
		$return="class='single'";
		//$return[1]=$c;
	}else{
		$return="class='duplicate'";
		//$return[1]=$c;
	}
	return $return;
}
function dupList($string,$packet){
	if ($string){
		$r=@mysql_query("select * from ps_packets where client_file LIKE '%$string%' and packet_id <> '$packet'");
		$data="<div style='font-size:12px; background-color:#FF0000; border:solid 1px #ffff00;'>Possible Duplicates:";
		while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			$data .= " <a href='order.php?packet=$d[packet_id]' target='_blank'>[$d[packet_id]]</a>";
		}
		$data .= "</div>";
	}else{
		$data="<div style='font-size:12px; background-color:#FF0000; border:solid 1px #ffff00;'>Unable to Determine Possible Duplicates</div>";
	}
	return $data;
}
function stripHours($date){
	$hours = explode(':',$date);
	return $hours[0];
}

function colorCode($hours,$status){
	if ($status == "CANCELLED" || $status == "FILED WITH COURT" || $status == "FILED WITH COURT - FBS"){
		return "00FF00";
	}else{
		if ($hours <= 250){ return "00FF00"; }
		if ($hours > 250 && $hours <= 300){ return "ffFF00"; }
		if ($hours > 300){ return "ff0000"; }
	}
	return "FFFFFF";
}

function dbCleaner($str){
	$str = trim($str);
	$str = addslashes($str);
	$str = strtoupper($str);
	$str = normalize_special_characters($str);
	//$str = ucwords($str);
	return $str;
}

function mkCC($str){
	$q="SELECT * FROM county";
	$r=@mysql_query($q);
	$option = '<option>'.$str.'</option>';
	while($d=mysql_fetch_array($r, MYSQL_ASSOC)){;
		$option .= '<option>'.$d[name].'</option>';
	}
	return $option;
}

function photoCount($packet){
	$count=trim(getPage("http://data.mdwestserve.com/countPhotos.php?packet=$packet", 'MDWS Count Photos', '5', ''));
	if ($count==''){
		$count=0;
	}
	return $count;
}

function findFileCopy($client_file){
	$search_dir='/data/service/fileCopy/';
	$dp=opendir($search_dir);
	while ($item = readdir($dp)){
		if ((is_dir ($item)) AND (substr($item,0,1) != '.')){
			if (strpos($client_file,$item)){
				echo "$item<br>";
			}
		}
	}
	rewinddir ($dp);
}

function dbIN($str){
	$str = trim($str);
	$str = addslashes($str);
	$str = strtolower($str);
	$str = ucwords($str);
	return $str;
}

function dbOUT($str){
	$str = stripslashes($str);
	return $str;
}

function addressRevise($packet,$address,$oldType,$newType){
	$qh="SELECT action_str, serverID, history_id FROM ps_history WHERE packet_id='$packet' AND action_str LIKE '%$address%'";
	$rh=@mysql_query($qh) or die (mysql_error());
	while ($dh=mysql_fetch_array($rh,MYSQL_ASSOC)){
		if ($dh != ''){
			$newStr=str_replace($oldType,$newType,stripslashes($dh[action_str]));
			$list .= "<tr><td>".$dh[history_id]."</td><td>$oldType</td><td>$newType</td></tr>'";
			@mysql_query("UPDATE ps_history SET action_str='".addslashes($newStr)."' WHERE history_id='".$dh[history_id]."'");
		}
	}
	return $list;
}

function searchForm($packet,$def){
	$start=date('Y');
	$start="01/01/".($start-1);
	$end=date('m/d/Y');
	$q1="SELECT status, packetID, firstName, lastName, county, company from watchDog where packetID='$packet' AND defID='$def'";
	$r1=@mysql_query($q1);
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	if ($d1[packetID] != ''){
		$link = "<form style='display:inline;' name='$packet-$def' action='http://casesearch.courts.state.md.us/inquiry/inquirySearch.jis' target='preview'>";
		$link .= "<input type='hidden' name='disclaimer' value='Y'>";
		$link .= "<input type='hidden' name='lastName' value='".$d1[lastName]."'>";
		$link .= "<input type='hidden' name='firstName' value='".$d1[firstName]."'>";
		$link .= "<input type='hidden' name='middleName' value=''>";
		$link .= "<input type='hidden' name='partytype' value=''>";
		$link .= "<input type='hidden' name='site' value='CIVIL'>";
		$link .= "<input type='hidden' name='courtSystem' value='C'>";
		$link .= "<input type='hidden' name='countyName' value='".$d1[county]."'>";
		$link .= "<input type='hidden' name='filingStart' value='$start'>";
		$link .= "<input type='hidden' name='filingEnd' value='$end'>";
		$link .= "<input type='hidden' name='filingDate' value=''>";
		$link .= "<input type='hidden' name='company' value='".$d1[company]."'>";
		$link .= "<input type='hidden' name='action' value='Search'>";
		if ($d1[packetID] && $d1[status] == 'New Case Found'){
			$link .= "<input type='submit' style='background-color: green; display:inline; width:20; height:20;' value='$def'>";
		}elseif ($d1[packetID] && $d1[status] != 'Search Complete'){
			$link .= "<input type='submit' style='background-color: red; display:inline; width:20; height:20;' value='$def'>";
		}
		$link .= "</form>";
	}
	return $link;
}

function searchList($packet){
	$q1="SELECT name1, name2, name3, name4, name5, name6 from ps_packets where packet_id='$packet'";
	$r1=@mysql_query($q1);
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	$i=0;
	while ($i < 6){$i++;
		if ($d1["name$i"]){
			$list .= searchForm($packet,$i);
		}
	}
	return $list;
}

function getFolder($otd){
	$path=explode("/",$otd);
	$count=(count($path)-2);
	$folder=$path[$count];
	return $folder;
}
function id2email($id){
	$q=@mysql_query("SELECT email from ps_users where id='$id'") or die(mysql_error());
	$d=mysql_fetch_array($q, MYSQL_ASSOC);
	return $d[email];
}
function id2company($id){
	$q=@mysql_query("SELECT company from ps_users where id='$id'") or die(mysql_error());
	$d=mysql_fetch_array($q, MYSQL_ASSOC);
	return strtoupper($d[company]);
}
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
function historyList($packet,$attorneys_id){
		$qn="SELECT * FROM ps_history WHERE packet_id = '$packet' order by defendant_id, history_id ASC";
		$rn=@mysql_query($qn) or die ("Query: $qn<br>".mysql_error());
		$counter=0;
		while ($dn=mysql_fetch_array($rn, MYSQL_ASSOC)){$counter++;
			$action_str=str_replace('<LI>','',strtoupper($dn[action_str]));
			$action_str=str_replace('</LI>','',$action_str);
			$list .=  "<hr><li>#$dn[history_id] : ".id2server($dn[serverID]).' '.$dn[wizard].'<br>'.stripslashes(attorneyCustomLang($attorneys_id,$action_str));
			if ($dn[wizard] == 'BORROWER' || $dn[wizard] == 'NOT BORROWER'){
				$list .=  '<br>'.attorneyCustomLang($attorneys_id,$dn[residentDesc]);
			}
			$list .= "</li>";
		}
		return $list;
}
function attachmentList($packet,$type){
	$list = "<fieldset><legend>Electronic File Storage</legend>";
	mysql_select_db('core');
	if ($type == 'EV'){
		$packet='EV'.$packet;
	}
	$r=@mysql_query("select * from ps_affidavits where packetID = '$packet' order by defendantID");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$affidavit=$d[affidavit];
		$affidavit=str_replace('http://mdwestserve.com/ps/affidavits/','http://mdwestserve.com/affidavits/',$affidavit);
		$list .= "<li><a href='$affidavit'>$d[method]</a></li>";
	}
	$list .= "</fieldset>";
	return $list;
}
function checkVerify($address){
	$r=@mysql_query("SELECT * FROM addressVerify where address like '%".addslashes($address)."%' LIMIT 0,1 ");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[user] != ''){
		return true;
	}else{
		return false;
	}
}
function getVerify($address){
	$r=@mysql_query("SELECT * FROM addressVerify where address like '%".addslashes($address)."%' LIMIT 0,1 ");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	if ($d[user] != ''){
		return "<img src='http://staff.mdwestserve.com/otd/greenCheck.png' style='display:inline;'	title='Verified By $d[user]'>";
	}else{
		return "<img src='http://staff.mdwestserve.com/otd/redX.png' style='display:inline;' title='Unverified Address'>";
	}
}
function isVerified($packet){
	$r=@mysql_query("SELECT address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e FROM ps_packets where packet_id = '$packet' LIMIT 0,1 ");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	$i=0;
	//if address is not verified, increment counter
	$add=strtoupper($d[address1].', '.$d[city1].', '.$d[state1].' '.$d[zip1]);
	if ($d[address1] != '' && checkVerify($add) !== false){$i++;}
	foreach(range('a','e') as $letter){
		$add=strtoupper($d["address1$letter"].', '.$d["city1$letter"].', '.$d["state1$letter"].' '.$d["zip1$letter"]);
		if ($d["address1$letter"] != '' && checkVerify($add) !== false){$i++;}
	}
	if ($i > 0){
		return false;
	}else{
		return true;
	}
}

function id2name3($id){
	$q="SELECT name FROM ps_users WHERE id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[name];
}

function search($search,$string){
	$pos = strpos($string, $search);
	if ($pos === false) {
		$pass = "";
	} else {
		$pass = $string;
	}
	return $pass;
}
function getClose($packet){
	$r=@mysql_query("select estFileDate from ps_packets where packet_id = '$packet'") or die (mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[estFileDate];
}

function getTime($packet,$event){
	$r=@mysql_query("select timeline from ps_packets where packet_id = '$packet'") or die (mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	$explode = explode('<br>',$d[timeline]);
	foreach ($explode as $key => $value) {	
		if (search($event,$value)){
			$search = search($event,$value);
		}
	}
	$array = array();
	if ($search){
		$array[css] = "done";
	}else{
		$array[css] = "pending";
	}
	$array[event] = $event;
	$array[eDate] = substr($search,0,17);
	return $array;
}

function rescanStatus($a,$b,$p){
	if ($a && $b){ return 'REQUEST BY '.id2name($a).' SCANNED BY '.id2name($b); }
	if ($a && !$b){ return 'RESCAN REQUESTED BY '.id2name($a); }
	if (!$a){ return 'ORIGINAL SCANS'; }
}

function exportStatus($a,$b,$p){
	if ($a && $b){ return 'REQUEST BY '.id2name($a).' APPROVED BY '.id2name($b); }
	if ($a && !$b){
		$r=@mysql_query("SELECT * FROM ps_history WHERE packet_id='$p'");
		$d=mysql_num_rows($r);
		if ($d){ echo "<script>alert('!! Export Warning !! This packet has $d history items.');</script>"; }
		return 'EXPORT APPROVAL REQUESTED BY '.id2name($a);
	}
	if (!$a){ return 'ACTIVE DATABASE'; }
}
function talk($to,$message){
include_once '/thirdParty/xmpphp/XMPPHP/XMPP.php';
$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'talkabout.files@gmail.com', '', 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
try {
$conn->useEncryption(true);
$conn->connect();
$conn->processUntil('session_start');
//$conn->presence("Ya, I'm online","available","talk.google.com");
$conn->message($to, $message);
$conn->disconnect();
} catch(XMPPHP_Exception $e) {
die($e->getMessage());
}
}

function timeline($id,$note){
  @mysql_query("insert into explorer (date,date_time,user,packet,uri) values (NOW(),NOW(),'".$_COOKIE[psdata][name]."','OTD$id','$note')") or die(mysql_error());

error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');
mysql_select_db ('core');
hardLog("$note for packet $id",'user');
//talk('insidenothing@gmail.com',"$note for presale packet $id");
$q1 = "SELECT timeline FROM ps_packets WHERE packet_id = '$id'";
$r1 = @mysql_query ($q1) or die(mysql_error());
$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
$access=date('m/d/y g:i A');
if ($d1[timeline] != ''){
$notes = $d1[timeline]."<br>$access: ".$note;
}else{
$notes = $access.': '.$note;
}
$notes = addslashes($notes);
$q1 = "UPDATE ps_packets set timeline='$notes' WHERE packet_id = '$id'";
$r1 = @mysql_query ($q1) or die(mysql_error());
//@mysql_query("insert into syslog (logTime, event) values (NOW(), 'Packet $id: $note')");
}
 function ev_timeline($id,$note){
error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [".trim($id)."] [".trim($note)."] \n", 3, '/logs/timeline.log');

 mysql_select_db ('core');
hardLog("$note for eviction packet $id",'user');
//talk('insidenothing@gmail.com',"$note for eviction packet $id");

$q1 = "SELECT timeline FROM evictionPackets WHERE eviction_id = '$id'";
$r1 = @mysql_query ($q1) or die(mysql_error());
$d1 = mysql_fetch_array($r1, MYSQL_ASSOC);
$access=date('m/d/y g:i A');
if ($d1[timeline] != ''){
$notes = $d1[timeline]."<br>$access: ".$note;
}else{
$notes = $access.': '.$note;
}
$notes = addslashes($notes);
$q1 = "UPDATE evictionPackets set timeline='$notes' WHERE eviction_id = '$id'";
$r1 = @mysql_query ($q1) or die(mysql_error());
//@mysql_query("insert into syslog (logTime, event) values (NOW(), 'Packet $id: $note')");
}
function opLog($event){
//@mysql_query("insert into syslog (logTime, event) values (NOW(), '$event')");
}
function washURI($uri){
$return=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$uri);
$return=str_replace('data/service/orders/','PS_PACKETS/',$uri);
$return=str_replace('portal/','',$return);
$return=str_replace('http://mdwestserve.com','http://alpha.mdwestserve.com',$return);
return $return;
}


?>