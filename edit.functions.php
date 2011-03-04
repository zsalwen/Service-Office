<?
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