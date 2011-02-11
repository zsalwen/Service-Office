<?php
include 'common.php';
?>
<script>
function automation() {
  window.opener.location.href = window.opener.location.href;
  //window.open('write_update.php','update','width=1,height=1,toolbar=no,location=no')
  if (window.opener.progressWindow)
		
 {
    window.opener.progressWindow.close()
  }
  window.close();
}
function prompter(packetID,newDate,oldDate){
	var reply = prompt("Please enter your reason for updating the Est. Close Date", "")
	if (reply == null){
		alert("That is not a valid reason")
		window.location="http://staff.mdwestserve.com/otd/order.php?packet="+packetID;
	}
	else{
		window.location="http://staff.mdwestserve.com/otd/tlEntry.php?packet="+packetID+"&entry="+reply+"&newDate="+newDate+"&oldDate="+oldDate,"OTD Timeline Entry";
	}
}
function ChgText(myResponse,myInput)
{
    var MyElement = document.getElementById(myInput);
    MyElement.value = myResponse;
    return true;
}
function setAddress1(street,city,state,zip)
{
ChgText(street,'address');
ChgText(city,'city');
ChgText(state,'state');
ChgText(zip,'zip');
}
function setAddress2(street,city,state,zip)
{
ChgText(street,'addressa');
ChgText(city,'citya');
ChgText(state,'statea');
ChgText(zip,'zipa');
}
function setAddress3(street,city,state,zip)
{
ChgText(street,'addressb');
ChgText(city,'cityb');
ChgText(state,'stateb');
ChgText(zip,'zipb');
}
function setAddress4(street,city,state,zip)
{
ChgText(street,'addressc');
ChgText(city,'cityc');
ChgText(state,'statec');
ChgText(zip,'zipc');
}
function setAddress5(street,city,state,zip)
{
ChgText(street,'addressd');
ChgText(city,'cityd');
ChgText(state,'stated');
ChgText(zip,'zipd');
}
function setAddress6(street,city,state,zip)
{
ChgText(street,'addresse');
ChgText(city,'citye');
ChgText(state,'statee');
ChgText(zip,'zipe');
}
</script>
<style>
select.italic {
	font-style:italic;
	background-color:red;
}
</style>
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
			$newStr=str_replace($oldType,$newType,$dh[action_str]);
			if ($newStr != $dh[action_str]){
				$list .= "<tr><td>".$dh[history_id]."</td><td>$oldType</td><td>$newType</td></tr>'";
				@mysql_query("UPDATE ps_history SET action_str='".$newStr."' WHERE history_id='".$dh[history_id]."'");
			}
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$id=$_COOKIE[psdata][user_id];

if ($_POST[reopen]){
	$r13=@mysql_query("select processor_notes, fileDate from ps_packets where packet_id = '$_GET[packet]'");
	$d13=mysql_fetch_array($r13,MYSQL_ASSOC);
	$oldNote = $d13[processor_notes];
	$note="file originally closed out on ".$d13[fileDate];
	$newNote = "<li>From ".$_COOKIE[psdata][name]." on ".date('m/d/y g:ia').": \"".$note."\"</li>".$oldNote;
	$today=date('Y-m-d');
	$deadline=time()+432000;
	$deadline=date('Y-m-d',$deadline);
	$q="UPDATE ps_packets SET processor_notes='".dbIN($newNote)."', filing_status='REOPENED', affidavit_status='IN PROGRESS', affidavit_status2='REOPENED', process_status='ASSIGNED', reopenDate='$today', fileDate='0000-00-00', estFileDate='$deadline', request_close='', request_closea='', request_closeb='', request_closec='', request_closed='', request_closee='' WHERE packet_id='".$_GET[packet]."'";
	@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	timeline($_GET[packet],$_COOKIE[psdata][name]." Reopened File for Additional Service");
}

if ($_POST[sendToClient]){
	$today=date('Y-m-d');
	@mysql_query("UPDATE ps_packets SET fileDate='$today', estFileDate='$today', filing_status='SEND TO CLIENT' WHERE packet_id='$_GET[packet]'");
	timeline($_GET[packet],$_COOKIE[psdata][name]." Marked File Send to Client");
}

if ($_POST[submit]){
	if ($_GET[packet]){
		$q=@mysql_query("SELECT * from ps_packets WHERE packet_id='$_POST[packet_id]'") or die (mysql_error());
		$d=mysql_fetch_array($q, MYSQL_ASSOC);
		if ($_POST[estFileDate] != $d[estFileDate]){
			//if estFileDate has been changed, set flag to open prompter
			$newClose=1;
			$oldFileDate=$d[estFileDate];
		}
		if (($d[address1] != $_POST[address]) || ($d[city1] != $_POST[city]) || ($d[state1] != $_POST[state]) || ($d[zip1] != $_POST[zip])){
			$newAddress=1;
		}
		foreach(range('a','e') as $letter){
			if (($d["address1$letter"] != $_POST["address$letter"]) || ($d["city1$letter"] != $_POST["city$letter"]) || ($d["state1$letter"] != $_POST["state$letter"]) || ($d["zip1$letter"] != $_POST["zip$letter"])){
				$newAddress=1;
			}
		}
		//reset uspsVerify if the address has been modified and no confirmed entry already exists within the addressVerify table
		if ($newAddress != '' && isVerified($d[packet_id]) !== true){
			@mysql_query("UPDATE ps_packets SET uspsVerify='' WHERE packet_id='$d[packet_id]'");
		}
		$case_no=trim($_POST[case_no]);
		// un dbCleaner on all items

		if ($_POST[addressType] != $d[addressType]){
			$searchAdd=$d[address1].", ".$d[city1].", ".$d[state1]." ".$d[zip1];
			$searchAdd=strtoupper($searchAdd);
			$reviseList=addressRevise($_POST[packet_id],$searchAd,$d[addressType],$_POST[addressType]);
			$TYPE .= "<table><tr><td>History ID</td><td>Old Type</td><td>New Type</td></tr>".$reviseList;
			//$TYPE .= "<h1>POST addressType: ".$_POST[addressType]."</h1><br><h1>DB addressType: ".$d[addressType]."</h1>";
		}
		foreach(range('a','e') as $letter){
			if ($_POST["addressType$letter"] != $d["addressType$letter"]){
				$searchAdd=$d["address1$letter"].", ".$d["city1$letter"].", ".$d["state1$letter"]." ".$d["zip1$letter"];
				$searchAdd=strtoupper($searchAdd);
				$reviseList=addressRevise($_POST[packet_id],$searchAdd,$d["addressType$letter"],$_POST["addressType$letter"]);
				$TYPE .= "<table><tr><td>History ID</td><td>Old Type</td><td>New Type</td></tr>".$reviseList;
				//$TYPE .= "<h1>POST addressType".$letter.": ".$_POST["addressType$letter"]."</h1><br><h1>DB addressType".$letter.": ".$d["addressType$letter"]."</h1>";
			}
		}
		if ($newClose != 1){
			$estQ="estFileDate='$_POST[estFileDate]',";
		}
			@mysql_query("UPDATE ps_packets SET process_status='$_POST[process_status]',
			filing_status='$_POST[filing_status]',
			service_status='$_POST[service_status]',
			fileDate='$_POST[fileDate]',
			courierID='$_POST[courierID]',
			addlDocs='$_POST[addlDocs]',
			lossMit='$_POST[lossMit]',
			avoidDOT='$_POST[avoidDOT]', ".$estQ."
			reopenDate='$_POST[reopenDate]',
			status='$_POST[status]',
			affidavit_status='$_POST[affidavit_status]',
			affidavit_status2='$_POST[affidavit_status2]',
			photoStatus='$_POST[photoStatus]',
			pobox='$_POST[pobox]',
			pocity='$_POST[pocity]',
			postate='$_POST[postate]',
			pozip='$_POST[pozip]',
			pobox2='$_POST[pobox2]',
			pocity2='$_POST[pocity2]',
			postate2='$_POST[postate2]',
			pozip2='$_POST[pozip2]',
			mail_status='$_POST[mail_status]',
			affidavitType='$_POST[affidavitType]',
			onAffidavit1='$_POST[onAffidavit1]',
			onAffidavit2='$_POST[onAffidavit2]',
			onAffidavit3='$_POST[onAffidavit3]',
			onAffidavit4='$_POST[onAffidavit4]',
			onAffidavit5='$_POST[onAffidavit5]',
			onAffidavit6='$_POST[onAffidavit6]',
			refile='$_POST[refile]',
			amendedAff='$_POST[amendedAff]',
			mailWeight='$_POST[mailWeight]',
			altPlaintiff='$_POST[altPlaintiff]',
			pages='$_POST[pages]',
			rush='$_POST[rush]',
			priority='$_POST[priority]',
			request_close='$_POST[request_close]',
			request_closea='$_POST[request_closea]',
			request_closeb='$_POST[request_closeb]',
			request_closec='$_POST[request_closec]',
			request_closed='$_POST[request_closed]',
			request_closee='$_POST[request_closee]',
			serveComplete='$_POST[serveComplete]',
			serveCompletea='$_POST[serveCompletea]',
			serveCompleteb='$_POST[serveCompleteb]',
			serveCompletec='$_POST[serveCompletec]',
			serveCompleted='$_POST[serveCompleted]',
			serveCompletee='$_POST[serveCompletee]',
			addressType='$_POST[addressType]',
			addressTypea='$_POST[addressTypea]',
			addressTypeb='$_POST[addressTypeb]',
			addressTypec='$_POST[addressTypec]',
			addressTyped='$_POST[addressTyped]',
			addressTypee='$_POST[addressTypee]',
			client_file='".strtoupper($_POST[client_file])."',
			case_no='".str_replace('?',0,$case_no)."',
			reopenNotes='".addslashes(strtoupper($_POST[reopenNotes]))."',
			auctionNote='".strtoupper($_POST[auctionNote])."',
			circuit_court='".strtoupper($_POST[circuit_court])."'
			WHERE packet_id='$_POST[packet_id]'") or die(mysql_error());
	}else{
		$case_no=trim($_POST[case_no]);
		@mysql_query("UPDATE ps_packets SET process_status='$_POST[process_status]',
		filing_status='$_POST[filing_status]',
		service_status='$_POST[service_status]',
		pobox='$_POST[pobox]',
		pocity='$_POST[pocity]',
		postate='$_POST[postate]',
		pozip='$_POST[pozip]',
		pobox2='$_POST[pobox2]',
		pocity2='$_POST[pocity2]',
		postate2='$_POST[postate2]',
		pozip2='$_POST[pozip2]',
		entry_id='$id',
		courierID='$_POST[courierID]',
		addlDocs='$_POST[addlDocs]',
		lossMit='$_POST[lossMit]',
		avoidDOT='$_POST[avoidDOT]',
		fileDate='$_POST[fileDate]',
		estFileDate='$_POST[estFileDate]',
		reopenDate='$_POST[reopenDate]',
		affidavit_status='$_POST[affidavit_status]',
		affidavit_status2='$_POST[affidavit_status2]',
		photoStatus='$_POST[photoStatus]',
		onAffidavit1='$_POST[onAffidavit1]',
		onAffidavit2='$_POST[onAffidavit2]',
		onAffidavit3='$_POST[onAffidavit3]',
		onAffidavit4='$_POST[onAffidavit4]',
		onAffidavit5='$_POST[onAffidavit5]',
		onAffidavit6='$_POST[onAffidavit6]',
		refile='$_POST[refile]',
		amendedAff='$_POST[amendedAff]',
		mailWeight='$_POST[mailWeight]',
		altPlaintiff='$_POST[altPlaintiff]',
		rush='$_POST[rush]',
		priority='$_POST[priority]',
		pages='$_POST[pages]',
		request_close='$_POST[request_close]',
		request_closea='$_POST[request_closea]',
		request_closeb='$_POST[request_closeb]',
		request_closec='$_POST[request_closec]',
		request_closed='$_POST[request_closed]',
		request_closee='$_POST[request_closee]',
		serveComplete='$_POST[serveComplete]',
		serveCompletea='$_POST[serveCompletea]',
		serveCompleteb='$_POST[serveCompleteb]',
		serveCompletec='$_POST[serveCompletec]',
		serveCompleted='$_POST[serveCompleted]',
		serveCompletee='$_POST[serveCompletee]',
		addressType='$_POST[addressType]',
		addressTypea='$_POST[addressTypea]',
		addressTypeb='$_POST[addressTypeb]',
		addressTypec='$_POST[addressTypec]',
		addressTyped='$_POST[addressTyped]',
		addressTypee='$_POST[addressTypee]',
		mail_status='$_POST[mail_status]',
		affidavitType='$_POST[affidavitType]',
		client_file='".strtoupper($_POST[client_file])."',
		case_no='".str_replace('?',0,$case_no)."',
		process_status='READY',
		status='RECIEVED',
		circuit_court='".strtoupper($_POST[circuit_court])."'
		WHERE packet_id='$_POST[packet_id]'") or die(mysql_error());
		timeline($_POST[packet_id],$_COOKIE[psdata][name]." Performed Data Entry");
		//if file is mail only, then open mailMatrix, minips_pay (upon submission of minips_pay, have that open quality control checklist)
		if ($_POST[service_status] == "MAIL ONLY"){
			@mysql_query("UPDATE ps_packets SET process_status='AWAITING CONFIRMATION' WHERE packet_id='".$_POST[packet_id]."'");
		}
		//here is where we will automate the address check and other popups
		echo "<script>window.open('supernova.php?packet=".$_POST[packet_id]."&close=1',   'supernova',   'width=600, height=800'); </script>";
	}
	//set servers and make timeline entries (if necessary);
	$timeline='';
	$dispDate=date('F jS, Y');
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Dispatched Service for Packet $d[packet_id] ($d[client_file])";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
	$body="Service for Packet $d[packet_id] (<strong>$d[client_file]</strong>) has been dispatched by ".$_COOKIE[psdata][name].", today $dispDate.<br><b>Please understand that this email is sent as confirmation of a process service file sent from our office today.  If you do not reply to the contrary--stating files have not been received--within 24 hours, you will be held responsible for any delays not made known to our office.</b><br>".$_COOKIE[psdata][name]."<br>MDWestServe<br>service@mdwestserve.com<br>(410) 828-4568<br>".time()."<br>".md5(time());
	if (isset($_POST[server1])){
		@mysql_query("UPDATE ps_packets SET server_id='$_POST[server1]' WHERE packet_id='$_POST[packet_id]'") or die(mysql_error());
		if ($_POST[server1] != $d[server_id]){
			$serverID=$_POST[server1];
			$id2name=id2name($serverID);
			if ($id2name == ''){
				$id2name="[BLANK]";
			}
			$id2company=id2company($serverID);
			if (trim($id2company) == ''){
				$id2company=$id2name;
			}
			$timeline = $_COOKIE[psdata][name]." Updated Order, Set $id2name as Server";
			if (($_POST[process_status] == 'ASSIGNED') && ($serverID != '')){
				//if file is currently assigned, send email to server(s) updating them about dispatch.
				$sdCount[$serverID]++;
				$subject2 = $subject." To [$id2company]";
				$headers2 = $headers."Cc: Service Updates <".id2email($d[server_id])."> \n";
				$headers3 .= $headers2;
				mail($to,$subject2,$body,$headers2);
			}
		}
	}
	foreach (range('a','e') as $letter){
		if (isset($_POST["server1$letter"])){
			@mysql_query("UPDATE ps_packets SET server_id$letter='".$_POST["server1$letter"]."' WHERE packet_id='$_POST[packet_id]'") or die(mysql_error());
			if ($_POST["server1$letter"] != $d["server_id$letter"]){
				$serverID='';
				$serverID=$_POST["server1$letter"];
				$id2name='';
				$id2name=id2name($serverID);
				if ($id2name == ''){
					$id2name="[BLANK]";
				}
				$id2company='';
				$id2company=id2company($serverID);
				if (trim($id2company) == ''){
					$id2company=$id2name;
				}
				if (($_POST[process_status] == 'ASSIGNED') && ($serverID != '') && ($sdCount[$serverID] < 1)){
					//if file is currently assigned, send email to server(s) updating them about dispatch.
					$sdCount[$serverID]++;
					$subject2 = $subject." To [$id2company]";
					$headers2 = $headers."Cc: Service Updates <".id2email($serverID)."> \n";
					$headers3 .= $headers2;
					mail($to,$subject2,$body,$headers2);
				}
				if (trim($timeline) != ''){
					$timeline .= ", Set $id2name as Server ".strtoupper($letter);
				}else{
					$timeline = $_COOKIE[psdata][name]." Updated Order, Set $id2name as Server ".strtoupper($letter);
				}
			}
		}
	}
	if ($noEntry != 1){
		if ($_GET[packet] && $timeline == ''){
			timeline($_GET[packet],$_COOKIE[psdata][name]." Updated Order");
		}elseif (trim($timeline) != ''){
			timeline($_POST[packet_id],$timeline);
		}
	}
	$r=mysql_query("SELECT name1, name2, name3, name4, name5, name6, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e, pobox, pobox2, pocity, pocity2, postate, postate2, pozip, pozip2 from ps_packets WHERE packet_id='$_POST[packet_id]'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC) or die(mysql_error());
	$i2=0;
	$qList="";
	while ($i2 < 6){$i2++;
		if (isset($_POST["name$i2"]) && ($_POST["name$i2"] != $d["name$i2"])){
			$qList .="name$i2='".dbCleaner($_POST["name$i2"])."', ";
		}
		if (isset($_POST[address]) && $_POST[address] != $d["address$i2"]){
			$qList .="address$i2='".dbCleaner($_POST[address])."', ";
		}
		if (isset($_POST[city]) && $_POST[city] != $d["city$i2"]){
			$qList .="city$i2='".dbCleaner($_POST[city])."', ";
		}
		if (isset($_POST[state]) && $_POST[state] != $d["state$i2"]){
			$qList .="state$i2='".dbCleaner($_POST[state])."', ";
		}
		if (isset($_POST[zip]) && $_POST[zip] != $d["zip$i2"]){
			$qList .="zip$i2='".dbCleaner($_POST[zip])."', ";
		}
		foreach(range('a','e') as $letter){
			$var=$i2.$letter;
			if (isset($_POST["address$letter"]) && $_POST["address$letter"] != $d["address$var"]){
				$qList .="address$var='".dbCleaner($_POST["address$letter"])."', ";
			}
			if (isset($_POST["city$letter"]) && $_POST["city$letter"] != $d["city$var"]){
				$qList .="city$var='".dbCleaner($_POST["city$letter"])."', ";
			}
			if (isset($_POST["state$letter"]) && $_POST["state$letter"] != $d["state$var"]){
				$qList .="state$var='".dbCleaner($_POST["state$letter"])."', ";
			}
			if (isset($_POST["zip$letter"]) && $_POST["zip$letter"] != $d["zip$var"]){
				$qList .="zip$var='".dbCleaner($_POST["zip$letter"])."', ";
			}
		}
	}
	if ($qList != ""){
		$q="UPDATE ps_packets SET ".substr($qList,0,-2)." WHERE packet_id='$_POST[packet_id]'";
		@mysql_query($q) or die("Query: $q<br>".mysql_error());
	}
	if ($_GET[packet] && $newClose == 1){
		echo "<script>prompter('$_POST[packet_id]','$_POST[estFileDate]','$oldFileDate');</script>";
	}elseif ($_GET[packet]){
		header ('Location: order.php?packet='.$_GET[packet].'&type='.addslashes($TYPE));
	}else{
		if ($_GET[start]){
			header ('Location: order.php?start='.$_GET[start].'&type='.addslashes($TYPE));
		}else{
			echo "<script>window.location.href='order.php';</script>";
		}
	}
}


if ($_GET[packet]){
	$r=@mysql_query("SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM ps_packets where packet_id='$_GET[packet]'");
	hardLog('loaded order for '.$_GET[packet],'user');
}else{
	if($_GET[start]){
		$r=@mysql_query("SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM ps_packets where process_status='READY' and qualityControl='' and packet_id >= '$_GET[start]' order by packet_id ");
	}else{
		$r=@mysql_query("SELECT *, CONCAT(TIMEDIFF( NOW(), date_received)) as hours FROM ps_packets where status='NEW' and process_status <> 'CANCELLED' and process_status <> 'DUPLICATE' AND process_status <> 'DAMAGED PDF' and process_status <> 'DUPLICATE/DIFF-PDF' order by RAND() ");
		$test55 = 1;
	}
}
$d=mysql_fetch_array($r, MYSQL_ASSOC);

if ($test55){
	hardLog('loaded NEW order for '.$d[packet_id],'user');
}
if ($_GET[cancel] == 1){
	//if MDWS employee confirms file cancellation, send separate emails to all servers (if service is is active), and another email to clients/service.
	if ($d[process_status] == 'ASSIGNED'){
		//if file is currently assigned, send email to server(s).
		$to = "Service Updates <mdwestserve@gmail.com>";
		$subject = "Cancelled Service for Packet $d[packet_id] ($d[client_file])";
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: ".$_COOKIE[psdata][name]." <".$_COOKIE[psdata][email]."> \n";
		$body="Service for Packet $d[packet_id] (<strong>$d[client_file]</strong>) has been cancelled by ".$_COOKIE[psdata][name].", if service is still in progress, please contact MDWestServe for instructions on how to proceed.";
		$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
		//send separate emails so that servers do not see each other, but so that mdwestserve@gmail.com does
		if ($d[server_id]){
			$serverID=$d[server_id];
			$sCount[$serverID]++;
			$headers2 = $headers."Cc: Service Updates <".id2email($d[server_id])."> \n";
			$headers3 .= $headers2;
			mail($to,$subject,$body,$headers2);
		}
		foreach(range('a','e') as $letter){
			$serverID='';
			$serverID=$d["server_id$letter"];
			if ($serverID != '' && $sCount[$serverID] < 1){
				$sCount[$serverID]++;
				$headers2 = $headers."Cc: Service Updates <".id2email($serverID)."> \n";
				$headers3 .= $headers2;
				mail($to,$subject,$body,$headers2);
			}
		}
		echo "<div style='background-color:#00FF00; font-size:11px;'>$headers3<hr>$body</div>";
	}
	@mysql_query("UPDATE ps_packets SET process_status = 'CANCELLED', service_status='CANCELLED', status='CANCELLED', affidavit_status='CANCELLED', payAuth='1' where packet_id='$d[packet_id]'");
	timeline($d[packet_id],$_COOKIE[psdata][name]." Cancelled Order  PER ".$_GET[cancelRef]);
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." Cancelled Process Service PER ".$_GET[cancelRef]." for OTD$d[packet_id] ($d[client_file]) [RECEIVED: $d[date_received]]",3,"/logs/user.log");
	// email client
	$to = "Service Updates <mdwestserve@gmail.com>";
	$subject = "Cancelled Service for Packet $d[packet_id] ($d[client_file])";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: ".$_COOKIE[psdata][name]." <service.cancelled@mdwestserve.com> \n";
	$attR = @mysql_query("select ps_to from attorneys where attorneys_id = '$d[attorneys_id]'");
	$attD = mysql_fetch_array($attR, MYSQL_BOTH);
	$c=-1;
	$cc = explode(',',$attD[ps_to]);
	$ccC = count($cc)-1;
	while ($c++ < $ccC){
		$headers .= "Cc: Service Updates <".trim($cc[$c])."> \n";
	}
	if (stripos($headers,$_GET[cancelRef]) == false){
		$headers .= "Cc: Service Updates <".$_GET[cancelRef]."> \n";
	}
	$headers .= "Cc: Service Updates <service@mdwestserve.com> \n";
	$history=historyList($d[packet_id],$d[attorneys_id]);
	if (strpos($history,'"')){
		$history=str_replace('"','\"',$history);
	}
	$attachmentList=attachmentList($d[packet_id],'OTD');
	$body ="<strong>Thank you for selecting MDWestServe as Your Process Service Provider.</strong><br>
	Service for Packet $d[packet_id] (<strong>$d[client_file]</strong>) is cancelled by ".$_COOKIE[psdata][name].", per ".$_GET[cancelRef]." closeout documents as follows:
	$attachmentList
	<div style='border:solid 1px;'>Service in $d[circuit_court] COUNTY was $d[service_status]. Filing status was $d[filing_status].<br>
	<center><h2>HISTORY ITEMS:</h2>
	$history
	</center></div><br>";
	$body .= "<br><br>(410) 828-4568<br>service@mdwestserve.com<br>MDWestServe, Inc.";
	mail($to,$subject,$body,$headers);
	echo "<div style='background-color:#00FF00; font-size:11px;'>$headers<hr>$body</div>";
	$r=@mysql_query("select * from ps_packets where packet_id='$d[packet_id]'");
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
}
?>
<center style="padding:0px;">

<?
// rescan commands
$rTest=@mysql_query("select * from rescanRequests where packetID = '$d[packet_id]'");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);

if ($_GET[rescan]){
	if(!$dTest[byID]){
		hardLog('requested rescan '.$d[packet_id],'user');
		//mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' requested rescan '.$d[packet_id],$_COOKIE[psdata][name].' requested rescan of packet '.$d[packet_id]);
		@mysql_query("INSERT INTO rescanRequests (packetID,byID) values ('$d[packet_id]','".$_COOKIE[psdata][user_id]."') ");
		echo "<script>automation();</script>";
	}else{
		hardLog('approved rescan '.$d[packet_id],'user');
		//mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' approved rescan '.$d[packet_id],$_COOKIE[psdata][name].' approved rescan of packet '.$d[packet_id]);
		@mysql_query("UPDATE rescanRequests set rescanID = '".$_COOKIE[psdata][user_id]."', rescanDate = NOW() where packetID = '$d[packet_id]'");
		echo "<script>automation();</script>";
	}
}
$rTest=@mysql_query("select * from rescanRequests where packetID = '$d[packet_id]'");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
$rescanStatus = rescanStatus($dTest[byID],$dTest[rescanID],$d[packet_id]);
// end rescan commands



// export commands
$rTest=@mysql_query("select * from exportRequests where packetID = '$d[packet_id]'");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
if ($_GET[export]){
	if(!$dTest[byID]){
		mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' requested export '.$d[packet_id],$_COOKIE[psdata][name].' requested export of packet '.$d[packet_id]);
		@mysql_query("INSERT INTO exportRequests (packetID,byID) values ('$d[packet_id]','".$_COOKIE[psdata][user_id]."') ");
		echo "<script>automation();</script>";
	}elseif($dTest[byID] != $_COOKIE[psdata][user_id]){
		mail('patrick@mdwestserve.com',$_COOKIE[psdata][name].' approved '.$d[packet_id],$_COOKIE[psdata][name].' approved export of packet '.$d[packet_id]);
		@mysql_query("UPDATE exportRequests set confirmID = '".$_COOKIE[psdata][user_id]."' where packetID = '$d[packet_id]'");
		echo "<script>automation();</script>";
	}else{	
		echo "<script>alert('You cannot approve exports you requested silly goose!');</script>";
	}
}
$rTest=@mysql_query("select * from exportRequests where packetID = '$d[packet_id]'");
$dTest=mysql_fetch_array($rTest,MYSQL_ASSOC);
$exportStatus = exportStatus($dTest[byID],$dTest[confirmID],$d[packet_id]);
// end export commands
?>
<style>
.done {
	height:50px;
	width:175px;
	font-size:12pt;
	text-align:center;
	background-color:ccffcc;
	border:ridge 3px #FF0000;
	}	
.active {
	height:50px;
	width:175px;
	font-size:12pt;
	text-align:center;
	background-color:ffffcc;
	border:ridge 3px #FFFF00;
	}
.alert	{
	height:50px;
	width:175px;
	font-size:12pt;
	text-align:center;
	background-color:ffcccc;
	border:ridge 3px #FF0000;
	}
.pending{
	height:50px;
	width:175px;
	font-size:12pt;
	text-align:center;
	background-color:cccccc;
	border:ridge 3px #FF0000;
	}
a { text-decoration:none}
table { padding:0px; margin:0px; cellpadding:0px;}
body { margin:0px; padding:0px;}
input, select { background-color:#CCFFFF; font-variant:small-caps; }
td { font-variant:small-caps;}
legend {margin:0px; border:solid 1px #FF0000; background-color:#cccccc; padding:0px;}
legend.a {margin:0px; border:solid 1px #FF0000; background-color:#cccccc; padding:0px; font-size:12px}
fieldset {margin:0px; padding:0px; background-color:#FFFFFF; }
.single{background-color:#00FF00}
.duplicate{background-color:#FF0000}
</style>
<table align="center"><tr>
<?
$packet=$d[packet_id];
?>
<? $test1 = getTime($packet,'Data Entry');?>
<td><div class="<?=$test1[css];?>"><?=$test1[event];?><br><?=$test1[eDate];?></div></td>

<? $test2 = getTime($packet,'Dispatched');?>
<td><div class="<?=$test2[css];?>"><?=$test2[event];?><br><?=$test2[eDate];?></div></td>

<? $test3 = getTime($packet,'Completing Service');?>
<? if (!$test3[eDate] && $test2[eDate]){ ?>
<td><div class="active">Service In Progress<br><?=date('m/d/y');?></div></td>
<? } else{ ?>
<td><div class="pending">Service In Progress<br></div></td>
<? } ?>
<td><div class="<?=$test3[css];?>"><?=$test3[event];?><br><?=$test3[eDate];?></div></td>
<? $test4 = getTime($packet,'Confirmed Filing');?>
<? if (!$test4[eDate] && $test3[eDate]){ ?>
<td><div class="active">Post-Service<br><?=date('m/d/y');?></div></td>
<? } else{ ?>
<td><div class="pending">Post-Service<br></div></td>
<? } ?>
<? if($test4[eDate]){ ?>
<td><div class="<?=$test4[css];?>"><?=$test4[event];?><br><?=$test4[eDate];?></div></td>
<? }else{ ?>
<td><div class="alert">Estimated Close<br><?=getClose($packet);?></div></td>
<? }?>
<td><div class="alert"style="font-size:10px;"><a href="?packet=<?=$packet?>&rescan='<?=time();?>'">RESCAN</a><hr><?=$rescanStatus;?></div></td>
<td><div class="alert"style="font-size:10px;"><a href="?packet=<?=$packet?>&export='<?=time();?>'">EXPORT</a><hr><?=$exportStatus;?></div></td>
</tr></table>
</center>





<script  type="text/javascript">
function confirmation(email) {
	if (email != ''){
		var answer = confirm("Are you sure that you want to cancel service per "+email+"? Emails will be sent to the client and all servers, should service be active.  Make sure that you have entered a valid client email address for reference.");
		if (answer){
			window.location = "http://staff.mdwestserve.com/otd/order.php?packet=<?=$d[packet_id]?>&cancelRef="+email+"&cancel=1";
		}
		else{
			alert("::ABORTED::");
			self.close();
		}
	}
	else{
		alert(email+"::NEED VALID EMAIL ADDRESS.  ABORTED::");
		self.close();
	}
}
function hideshow(which){
if (!document.getElementById)
return
if (which.style.display=="block")
which.style.display="none"
else
which.style.display="block"
}

function ClipBoard()
{
holdtext.innerText = copytext.innerText;
Copied = holdtext.createTextRange();
Copied.execCommand("Copy");
}
</script>

<? if (!$d[packet_id]){ ?>
<center>
<img src="/404.gif" border="1"><br>
<form>Jump to packet <input name="packet"></form><br><br>
<a href="archive.php?packet=<?=$_GET[packet]?>">Have you checked <b>the archives</b> for packet <?=$_GET[packet]?>?</a>
</center>
<? }else{ ?>
<body  style="padding:0px;">
<form method="post">
<input type="hidden" name="uspsVerify" value="<?=$d[uspsVerify]?>">
<table width="100%" style='background-color:<?=colorCode(stripHours($d[hours]),$d[filing_status]);?>; padding:0px;'>
<tr>
<td valign="top">
<FIELDSET style="padding:0px;">
<div style="background-color:#FFFFFF; padding:0px;" align="center">
<table width="100%"  style="padding:0px; font-size: 11px;"><tr><td align="center">
<? if (!$d[uspsVerify]){?><a href="supernova.php?packet=<?=$d[packet_id]?>" target="preview">!!!Verify Addresses!!!</a><? }else{ ?><img src="http://www.usps.com/common/images/v2header/usps_hm_ci_logo2-159x36x8.gif" ><br>Verified by <? echo $d[uspsVerify]; } ?>
<?
$received=strtotime($d[date_received]);
$deadline=$received+432000;
$deadline=date('F jS Y',$deadline);
$days=number_format((time()-$received)/86400,0);
$hours=number_format((time()-$received)/3600,0);
?>
 </td><td align="center">
<? if(!$d[caseVerify]){ ?> <a href="validateCase.php?case=<?=$d[case_no]?>&packet=<?=$d[packet_id]?>&county=<?=$d[circuit_court]?>" target="preview">!!!Verify Case Number!!!</a><? }else{ ?><img src="http://www.courts.state.md.us/newlogosm.gif"><br>Verified by <? echo $d[caseVerify]; }?>
</td><td align="center">
<? if(!$d[qualityControl]){ ?> <a href="entryVerify.php?packet=<?=$d[packet_id]?><? if ($d[service_status] == 'MAIL ONLY'){ echo '&matrix=1';} ?>&frame=no" target="preview">!!!Verify Data Entry!!!</a><? }else{ ?><img src="http://staff.mdwestserve.com/small.logo.gif" height="41" width="41"><br>Verified by <? echo $d[qualityControl]; }?>
</td><td align="center"><div style="font-size:15pt" ><?=$hours?> Hours || <?=$days?> Days<br>Deadline: <?=$deadline?><div></td></tr></table>
</div>
<? if ($d[possibleDuplicate]){?>
<div style="background-color:#ff0000" align="center">Duplicate Warning Level: <?=$d[possibleDuplicate]?></div>
<? } ?>
<table width="100%" style="padding:0px;"><tr>
<?
$dupCheck=dupCheck($d[client_file]);
?>
<td valign="top" <?=$dupCheck?>>
<FIELDSET style="padding:0px;">
<LEGEND ACCESSKEY=C><?=id2attorney($d[attorneys_id]);?> File Data <input type="submit" name="submit" style="background-color:#00FF00; font-weight:bold; width:100px;" value="SAVE"></LEGEND>
<table>
<tr>
<td>Client&nbsp;File </td>
<td><input name="client_file" value="<?=$d[client_file]?>" /></td>
</tr>
<tr>
<td><a href="http://casesearch.courts.state.md.us/inquiry/inquiryDetail.jis?caseId=<?=str_replace('-','',trim($d[case_no]))?>&detailLoc=<? if ($d[circuit_court] == "MONTGOMERY"){ echo "MCCI";}elseif($d[circuit_court] == "PRINCE GEORGES"){echo "PGV";}else{ echo "CC";} ?>" target="preview">Case&nbsp;Number</a></td>
<td><input name="case_no" value="<?=$d[case_no]?>" /></td>
</tr>
<tr>
<td>Circuit&nbsp;Court</a></td>
<td><select name="circuit_court"><?=mkCC($d[circuit_court]);?></select></td>
</tr>
<tr>
<td>Est. Close</td>
<td><input name="estFileDate" value="<?=$d[estFileDate]?>"></td>
</tr>
<?
$rXX=@mysql_query("select name, phone from courier where courierID = '$d[courierID]'");
$dXX=mysql_fetch_array($rXX,MYSQL_ASSOC);
if ($dXX[phone]){
	$phone="-".$dXX[phone];
}
?>
<tr>
<td>Courier</td>
<td><select name="courierID"><option value="<?=$d[courierID]?>"><?=$dXX[name]?><?=$phone?></option>
<?
$CCr=@mysql_query("select * from courier WHERE isActive='1'");
while($CCd=mysql_fetch_array($CCr,MYSQL_ASSOC)){
if ($CCd[phone]){
	$phone="-".$CCd[phone];
}else{
	$phone='';
}
?>
<option value="<?=$CCd[courierID]?>"><?=$CCd[name]?><?=$phone?> (<?=$CCd[courierID]?>)</option>
<? }?></select></td>
</tr>
<tr>
<td>File Closed</td>
<td><input name="fileDate" value="<?=$d[fileDate]?>"><input type="submit" name="sendToClient" style="background-color:#66CCFF; font-weight:bold; width:140px;" value="SEND TO CLIENT"></td>
</tr>
<tr>
<td>File Reopened</td>
<td><input name="reopenDate" value="<?=$d[reopenDate]?>"><input type="submit" name="reopen" style="background-color:#FFFF00; font-weight:bold; width:70px;" value="REOPEN"></td>
</tr>
<tr>
<td>Alt. Plaintiff</td>
<td><input name="altPlaintiff" value="<?=$d[altPlaintiff]?>"></td>
</tr>
<tr>
<td>Addl&nbsp;Docs</td>
<td><input name="addlDocs" value="<?=$d[addlDocs]?>"></td>
</tr>
<tr>
<td>Loss Mitigation</td>
<td><? if ($d[lossMit] != ''){ echo "<select name='lossMit'><option>$d[lossMit]</option>";}elseif($d[status] == 'NEW'){ echo "<select name='lossMit' class='italic'><option value='FINAL' class='italic'>FINAL</option>"; }else{ echo "<select name='lossMit' class='italic'><option value='' class='italic'></option>"; } ?>
<option>FINAL</option>
<option>PRELIMINARY</option>
<option>N/A - OLD LAW</option>
<?
if($d[lossMit] != ''){
?>
<option value=''></option>
<? } ?>
</select><div style='display:inline;<? if ($d[avoidDOT] == 'checked'){ echo " background-color:#FF0000;"; }?>'><input <? if ($d[avoidDOT] == 'checked'){echo "checked";}?> type='checkbox' value='checked' name='avoidDOT'> Only Post @ DOT</div>
</td>
</tr>
<tr>
<td colspan='2'><div style=" font-size:12px; background-color:ffffff; border:solid 1px #ffff00; padding:0px;">
<?
mysql_select_db('core');
$q5="SELECT * FROM ps_affidavits WHERE packetID = '$d[packet_id]' order by defendantID";
$r5=@mysql_query($q5) or die ("Query: $q5<br>".mysql_error());
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){
		$defname = $d["name".$d5[defendantID]];
		echo "<li><a target='_blank' href='".str_replace('ps/','',$d5[affidavit])."'><strong>".$defname."</strong>: $d5[method]</a></li>";
}
?>
<a href="affidavitUpload.php?packet=<?=$d[packet_id]?>" target="preview">Upload More Documents</a>, <a href="#" onclick="window.open('/lightboard.php?packet=<?=$d[packet_id]?>','Lightboard','menubar=0,resizable=1,status=0,width=800,height=600') ">Lightboard</a>
</div></td></tr>
<? if ($d[attorney_notes]){ ?>
<tr>
<td colspan='2'>Instructions: <?=$d[date_received];?><br><?=$d[attorney_notes];?></td>
</tr>
<? }?>
</table>
</FIELDSET>
<?
if ($dupCheck == "class='duplicate'"){
	echo dupList($d[client_file],$d[packet_id]);
}
?>
</td>
<td valign="top">
<FIELDSET style="padding:0px;">
<LEGEND ACCESSKEY=C>Persons to Serve</LEGEND>
<table>
<tr>
<td nowrap>1<input size="20" name="name1" id="name1" value="<?=stripslashes($d[name1])?>" /><input <? if ($d[onAffidavit1]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit1"></td><? $mult=1;?>
</tr><tr>
<td nowrap>2<input size="20" name="name2" id="name2" value="<?=stripslashes($d[name2])?>" /><input <? if ($d[onAffidavit2]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit2"></td><? if ($d[name2]){$mult++;}?>
</tr><tr>
<td nowrap>3<input size="20" name="name3" id="name3" value="<?=stripslashes($d[name3])?>" /><input <? if ($d[onAffidavit3]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit3"></td><? if ($d[name3]){$mult++;}?>
</tr><tr>
<td nowrap>4<input size="20" name="name4" id="name4" value="<?=stripslashes($d[name4])?>" /><input <? if ($d[onAffidavit4]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit4"></td><? if ($d[name4]){$mult++;}?>
</tr><tr>
<td nowrap>5<input size="20" name="name5" id="name5" value="<?=stripslashes($d[name5])?>" /><input <? if ($d[onAffidavit5]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit5"></td><? if ($d[name5]){$mult++;}?>
</tr><tr>
<td nowrap>6<input size="20" name="name6" id="name6" value="<?=stripslashes($d[name6])?>" /><input <? if ($d[onAffidavit6]=='checked'){echo "checked";} ?> type="checkbox" value="checked" name="onAffidavit6"></td><? if ($d[name6]){$mult++;}?>
</tr>
</table>
</FIELDSET>
<?
$otdStr=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
$otdStr=str_replace('data/service/orders/','PS_PACKETS/',$otdStr);
$otdStr=str_replace('portal/','',$otdStr);
//$otdStr=str_replace('mdwestserve.com','alpha.mdwestserve.com',$otdStr);
/*if (!$otdStr){
	$otdStr=$d[otd];
}*/
if (!strpos($otdStr,'mdwestserve.com')){
	$otdStr="http://mdwestserve.com/".$otdStr;
}
if ($d[packet_id] > 3620 && $d[reopenDate] != ''){
	$checkLink="serviceSheet.php?packet=$d[packet_id]&autoPrint=1";
}else{
	$checkLink="oldServiceSheet.php?packet=$d[packet_id]&autoPrint=1";
}
$q5="SELECT DISTINCT serverID from ps_history WHERE packet_id='$d[packet_id]'";
$r5=@mysql_query($q5) or die(mysql_error());
$i=0;
$data5=mysql_num_rows($r5);
if ($data5 > 0){
while ($d5=mysql_fetch_array($r5, MYSQL_ASSOC)){$i++;
$q6="SELECT * FROM ps_history WHERE serverID='$d5[serverID]' and packet_id='$d[packet_id]'";
$r6=@mysql_query($q6) or die(mysql_error());
$d6=mysql_num_rows($r6);
if ($i == '1'){
if ($d6 > 1){
$server = $d6." ".initals(id2name($d5[serverID]));
}else{
$server = $d6." ".initals(id2name($d5[serverID]));
}
}else{
if ($d6 > 1){
$server .= ", ".$d6." ".initals(id2name($d5[serverID]));
}else{
$server .= ", ".$d6." ".initals(id2name($d5[serverID]));
}
}
}
}else{
$server="none";
}
$ri=@mysql_query("SELECT packetID FROM ps_instructions WHERE packetID='$d[packet_id]'") or die (mysql_error());
$di=mysql_fetch_array($ri,MYSQL_ASSOC);
if ($di[packetID]){
	$customBG="style='background-color:green;'";
}else{
	$customBG="style='background-color:red;'";
}
$rc=@mysql_query("SELECT * FROM ps_history WHERE packet_id='$d[packet_id]' AND wizard='CERT MAILING' LIMIT 0,1");
$dc=mysql_fetch_array($rc,MYSQL_ASSOC);
?>
<FIELDSET style="background-color:#FFFF00; padding:0px;">
<LEGEND ACCESSKEY=C>Service Links</LEGEND>
<table style="padding:0px; font-weight:bold; border-collapse:collapse; height:150px !important; font-size:11px;" cellpadding="0" cellspacing="0">
	<tr>
		<td><a href="http://staff.mdwestserve.com/otd/minips_pay.php?id=<?=$d[packet_id]?>" target="preview">Payments</a></td>
	</tr>
	<tr>
		<td><a href="http://staff.mdwestserve.com/standardExport.php?packet=<?=$d[packet_id]?>" target="preview">Transfer</a></td>
	</tr>
	<tr>
		<td><a href="historyModify.php?packet=<?=$d[packet_id]?>&form=1" target="preview">History (<?=$server?>)</a></td>
	</tr>
	<tr>
		<td><a href="http://service.mdwestserve.com/customInstructions.php?packet=<?=$d[packet_id]?>" target="preview">Instructions <?=id2attorney($d[attorneys_id])?></a>-<a href="instructMatrix.php?packet=<?=$d[packet_id]?>" <?=$customBG?> target="preview"><small>[CUSTOMIZE]</small></a></td>
	</tr>
	<tr>
		<td><a href="<?=$otdStr?>" target="preview">OTD</a> | <a href="serviceReview.php?packet=<?=$d[packet_id]?>" target="preview">Timeline</a> | <a href="<?=$checkLink?>" target="_blank">Checklist</a></td>
	</tr>
	<tr>
		<td><a href="photoDisplay.php?packet=<?=$d[packet_id]?>" target="preview"><?$photoCount=photoCount($d[packet_id]); echo $photoCount;?> Photo<? if($photoCount != 1){echo "s";}?></a></td>
	</tr>
	<tr>
		<td><a href="http://staff.mdwestserve.com/penalize.php?packet=<?=$d[eviction_id]?>&svc=OTD&display=1" target="preview">Penalties</a></td>
	</tr>
	<tr>
		<td><a href="mailings.php?OTD=<?=$d[packet_id]?>" target="preview">Mailings</a><? 	if (webservice($d[client_file]) && ($d[attorneys_id] == 1)){ ?> | <a href="http://staff.mdwestserve.com/otd/webservice.php?fileNumber=<?=$d[client_file];?>" target="preview">Webservice Data</a><? }?></td>
	</tr>
	<?
$FC = trim(getPage("http://data.mdwestserve.com/findFC.php?clientFile=$d[client_file]", "MDWS File Copy for Packet $d[packet_id]", '5', ''));
if ($FC != '' && $FC != '1'){
	echo "<tr><td>$FC</td></tr>";
}
$folder=getFolder($d[otd]);
$rfm='/data/service/orders/'.$folder.'/RequestforMediation.pdf';
$trioAff='/data/service/orders/'.$folder.'/TrioAffidavitService.pdf';
if (file_exists($rfm)){
	echo "<tr><td><a href='http://mdwestserve.com/PS_PACKETS/$folder/RequestforMediation.pdf' target='preview'>Request For Mediation</a></td></tr>";
}
if (file_exists($trioAff)){
	echo "<tr><td><a href='http://mdwestserve.com/PS_PACKETS/$folder/TrioAffidavitService.pdf' target='preview'>Trio Aff</a></td></tr>";
}
if ($dc[packet_id]){
	echo "<tr><td><a href='http://staff.mdwestserve.com/otd/serviceCertificate.php?packet=$d[packet_id]' target='preview'>Certificate of Service</a></td></tr>";
}
	?>
	
</table>
</FIELDSET>
</td></tr></table>
<table style="display:<? if ($_GET[packet]){ echo "block";}else{ echo "none"; }?>; padding:0px;" id="notes" width="100%"><tr><td colspan="2"><fieldset><legend>Notes</legend>
<iframe height="200px" width="700px"  frameborder="0" src="http://staff.mdwestserve.com/notes.php?packet=<?=$d[packet_id]?>"></iframe></fieldset></td></tr></table>
<table style="display:none;" id="track" width="100%"><tr><td align='center'>
<FIELDSET>
<LEGEND ACCESSKEY=C>docuTrack: in-house document tracking solution</LEGEND>
<table width="100%" border="1" style="border-collapse:collapse;" cellspacing='0' cellpadding='2'>
<tr>
	<td>Document</td>
	<td>Defendant</td>
	<td>Signer</td>
	<td>Processor</td>
	<td>Timestamp</td>
</tr>
<? 
$r92=@mysql_query("select * from docuTrack where packet = '$d[packet_id]' order by trackID desc");
while($d92=mysql_fetch_array($r92,MYSQL_ASSOC)){
if ($d92[defendant] == 'OCC'){
	$defname = "OCCUPANT";
}elseif ($d92[defendant] == 'CERT'){
	$defname = "CERTIFICATE";
}else{
	$defname = $d["name".$d92[defendant]];
}
if ($d92[server]){
	$signer = id2name($d92[server]);
}else{
	$signer = "Version 1 Barcode";
}?>
<tr>
	<td><?=$d92[document]?></td>
	<td><?=$defname?></td>
	<td><?=$signer?></td>
	<td><?=$d92[location]?></td>
	<td><?=$d92[binder]?></td>
</tr>
<? } ?>
</table>    
</FIELDSET></td></tr></table>

<? if(!$d[address1]){ ?>
<table width="100%" style="display:block;" id="addresses">
<? }else{ ?>
<table width="100%" style="display:none;" id="addresses">
<? } ?>
<?
$add1=strtoupper($d[address1].', '.$d[city1].', '.$d[state1].' '.$d[zip1]);
$add1a=strtoupper($d[address1a].', '.$d[city1a].', '.$d[state1a].' '.$d[zip1a]);
$add1b=strtoupper($d[address1b].', '.$d[city1b].', '.$d[state1b].' '.$d[zip1b]);
$add1c=strtoupper($d[address1c].', '.$d[city1c].', '.$d[state1c].' '.$d[zip1c]);
$add1d=strtoupper($d[address1d].', '.$d[city1d].', '.$d[state1d].' '.$d[zip1d]);
$add1e=strtoupper($d[address1e].', '.$d[city1e].', '.$d[state1e].' '.$d[zip1e]);
?>
<tr><td>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1]?>&city=<?=$d[city1]?>&state=<?=$d[state1]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1);?>&nbsp;<?=id2name($d[server_id]);?><br><input name="addressType" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressType]?>"></LEGEND>
<table>
<tr>
<td><input id="address" name="address" size="30" value="<?=$d[address1]?>" /></td>
</tr>
<tr>
<td><input size="20" name="city" id="city" value="<?=$d[city1]?>" /><input size="1" name="state" id="state" value="<?=$d[state1]?>" /><input size="4" name="zip"id="zip" value="<?=$d[zip1]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td><td>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=str_replace('#','',$d[address1a])?>&city=<?=$d[city1a]?>&state=<?=$d[state1a]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1a);?>&nbsp;<?=id2name($d[server_ida]);?><br><input name="addressTypea" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTypea]?>"></LEGEND>
<table>
<tr>
<td><input name="addressa" id="addressa" size="30" value="<?=$d[address1a]?>" /></td>
</tr>
<tr>
<td><input name="citya" id="citya" size="20" value="<?=$d[city1a]?>" /><input size="1" name="statea" id="statea" value="<?=$d[state1a]?>" /><input size="4" name="zipa" id="zipa" value="<?=$d[zip1a]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td><td>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1b]?>&city=<?=$d[city1b]?>&state=<?=$d[state1b]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1b);?>&nbsp;<?=id2name($d[server_idb]);?><br><input name="addressTypeb" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTypeb]?>"></LEGEND>
<table>
<tr>
<td><input name="addressb" id="addressb" size="30" value="<?=$d[address1b]?>" /></td>
</tr>
<tr>
<td><input name="cityb" id="cityb" size="20" value="<?=$d[city1b]?>" /><input size="1" name="stateb" id="stateb" value="<?=$d[state1b]?>" /><input size="4" name="zipb" id="zipb" value="<?=$d[zip1b]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td></tr>

<tr><td>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1c]?>&city=<?=$d[city1c]?>&state=<?=$d[state1c]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1c);?>&nbsp;<?=id2name($d[server_idc]);?><br><input name="addressTypec" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTypec]?>"></LEGEND>
<table>
<tr>
<td><input name="addressc" id="addressc" value="<?=$d[address1c]?>" size="30" /></td>
</tr>
<tr>
<td><input name="cityc" id="cityc" size="20" value="<?=$d[city1c]?>" /><input size="1" name="statec" id="statec" value="<?=$d[state1c]?>" /><input size="4" name="zipc" id="zipc" value="<?=$d[zip1c]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td><td>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1d]?>&city=<?=$d[city1d]?>&state=<?=$d[state1d]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1d);?>&nbsp;<?=id2name($d[server_idd]);?><br><input name="addressTyped" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTyped]?>"></LEGEND>
<table>
<tr>
<td><input name="addressd" id="addressd" size="30" value="<?=$d[address1d]?>" /></td>
</tr>
<tr>
<td><input name="cityd" id="cityd" size="20" value="<?=$d[city1d]?>" /><input size="1" name="stated" id="stated" value="<?=$d[state1d]?>" /><input size="4" name="zipd" id="zipd" value="<?=$d[zip1d]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td><td>
<FIELDSET>
<LEGEND class="a" ACCESSKEY=C><a href="http://staff.mdwestserve.com/dispatcher.php?aptsut=&address=<?=$d[address1e]?>&city=<?=$d[city1e]?>&state=<?=$d[state1e]?>&miles=5" target="_Blank"><img src="http://staff.mdwestserve.com/small-usps-logo.jpg" border="0"></a>&nbsp;<?=getVerify($add1e);?>&nbsp;<?=id2name($d[server_ide]);?><br><input name="addressTypee" size="55" style="font-size:10px; background-color:CCFFCC;" value="<?=$d[addressTypee]?>"></LEGEND>
<table>
<tr>
<td><input name="addresse" id="addresse" size="30" value="<?=$d[address1e]?>" /></td>
</tr>
<tr>
<td><input name="citye" id="citye" size="20" value="<?=$d[city1e]?>" /><input size="1" name="statee" id="statee" value="<?=$d[state1e]?>" /><input size="4" name="zipe" id="zipe" value="<?=$d[zip1e]?>" /></td>
</tr>
</table>    
</FIELDSET>
</td></tr>
</table>



<strong>
	<div align="center" style="background-color:#FFFF00">
    	<a onClick="hideshow(document.getElementById('track'))">Tracking</a> &curren; 
    	<a onClick="hideshow(document.getElementById('addresses'))">Addresses</a> &curren; 
    	<a onClick="hideshow(document.getElementById('pobox'))">Mail Only</a> &curren; 
    	<a onClick="hideshow(document.getElementById('status'))">Status</a> &curren; 
        <a onClick="hideshow(document.getElementById('servers'))">Servers</a> &curren; 
        <a onClick="hideshow(document.getElementById('notes'))">Notes</a> &curren; 
    </div>
</strong>
<table width="100%" id="pobox" style="display:none;"><tr><td>
<table width="100%">
<tr>
<td>Mail Only</td>
<td><input name="pobox" value="<?=$d[pobox]?>" /></td>
</tr>
<tr>
<td>City</td>
<td><input name="pocity" value="<?=$d[pocity]?>" /></td>
</tr>
<tr>
<td>State</td>
<td><input name="postate" value="<?=$d[postate]?>" /></td>
</tr>
<tr>
<td>ZIP</td>
<td><input name="pozip" value="<?=$d[pozip]?>" /></td>
</tr>
</table>
</td><td>
<table width="100%">
<tr>
<td>Mail Only 2</td>
<td><input name="pobox2" value="<?=$d[pobox2]?>" /></td>
</tr>
<tr>
<td>City 2</td>
<td><input name="pocity2" value="<?=$d[pocity2]?>" /></td>
</tr>
<tr>
<td>State 2</td>
<td><input name="postate2" value="<?=$d[postate2]?>" /></td>
</tr>
<tr>
<td>ZIP 2</td>
<td><input name="pozip2" value="<?=$d[pozip2]?>" /></td>
</tr>
</table>    
</td></tr></table>

<table width="100%" id="status" style="display:none; font-size:11px; padding:0px;">
<input type="hidden" name="packet_id" value="<?=$d[packet_id]?>" />
<tr>
<? if ($_GET[packet]){?>
<td align="center" width="25%">Client Status<br><select name="status"><option><?=$d[status]?></option>
<?
$q1="SELECT DISTINCT status from ps_packets WHERE status <> ''";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=$d1[status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<? }?>
<td align="center" width="25%">Service Status<br><select name="service_status"><option><?=$d[service_status]?></option>
<?
$q1="SELECT DISTINCT service_status from ps_packets WHERE service_status <> ''";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=$d1[service_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%">Filing Status<br><select name="filing_status"><option><?=$d[filing_status]?></option>
<?
$q1="SELECT DISTINCT filing_status from ps_packets WHERE filing_status <> '' AND filing_status <> 'REOPENED'";
$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
while ($d1=mysql_fetch_array($r1, MYSQL_ASSOC)){
?>
<option><?=strtoupper($d1[filing_status])?></option>
<? } ?>
<option>REOPENED</option>
<option value=""></option>
</select></td></tr><tr>
<td align="center" width="25%">Process Status<br><select name="process_status"><option><?=$d[process_status]?></option>
<?
$q2="SELECT DISTINCT process_status from ps_packets WHERE process_status <> ''";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
?>
<option><?=$d2[process_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%"><table><tr><td>Affidavit Status<br><select name="affidavit_status"><option><?=$d[affidavit_status]?></option>
<?
$q3="SELECT DISTINCT affidavit_status from ps_packets WHERE affidavit_status <> ''";
$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
while ($d3=mysql_fetch_array($r3, MYSQL_ASSOC)){
?>
<option><?=$d3[affidavit_status]?></option>
<? } ?>
<option value=""></option>
</select>

</td><td>
<td align="center" colspan='3'>Affidavit Status 2<br><select name="affidavit_status2"><option><?=$d[affidavit_status2]?></option>
<?
$q3="SELECT DISTINCT affidavit_status2 from ps_packets WHERE affidavit_status2 <> '' AND affidavit_status2 <> 'REOPENED' AND affidavit_status2 <> 'AWAITING OUT OF STATE AFFIDAVITS' AND affidavit_status2 <> 'AWAITING OUT OF STATE SERVICE' AND affidavit_status2 <> 'AWAITING MAILING'";
$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
while ($d3=mysql_fetch_array($r3, MYSQL_ASSOC)){
?>
<option><?=$d3[affidavit_status2]?></option>
<? } ?>
<option>AWAITING MAILING</option>
<option>AWAITING OUT OF STATE AFFIDAVITS</option>
<option>AWAITING OUT OF STATE SERVICE</option>
<option>REOPENED</option>
<option value=""></option>
</select></td>
</td></tr></table>
</td>
<td align="center">Photo Status<br><select name="photoStatus"><option><?=$d[photoStatus]?></option>
<?
$q4="SELECT DISTINCT photoStatus from ps_packets WHERE photoStatus <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[photoStatus]?></option>
<? } ?>
<option value=""></option>
</select></td></tr><tr>
<td align="center" width="25%">Affidavit Type<br><select name="affidavitType"><option><?=$d[affidavitType]?></option>
<?
$q4="SELECT DISTINCT affidavitType from ps_packets WHERE affidavitType <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[affidavitType]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align="center" width="25%">Mail Status<br><select name="mail_status"><option><?=$d[mail_status]?></option>
<?
$q4="SELECT DISTINCT mail_status from ps_packets WHERE mail_status <> ''";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while ($d4=mysql_fetch_array($r4, MYSQL_ASSOC)){
?>
<option><?=$d4[mail_status]?></option>
<? } ?>
<option value=""></option>
</select></td>
<td align='center'><? if ($d[process_status] != 'CANCELLED'){ ?><input style='font-size:11px;' name='cancelRef' value='Client Reference Email' onclick="value=''" size='25'><br><input style='background-color:pink; font-size: 10.5px;' type='button' name='cancel' value='CANCEL' onclick="confirmation(cancelRef.value);"><? }?></td>
</tr>
<tr><td align="center" colspan='3'><table align='center' style='font-size:12px;' width='100%'><tr><td align="center" width='25%'>
Refile<br>
<input type="checkbox" name="refile" <? if ($d[refile] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" width='25%' style="padding-left:5px">
Rush Service<br>
<input type="checkbox" name="rush" <? if ($d[rush] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" width='25%' style="padding-left:5px">
Priority Service<br>
<input type="checkbox" name="priority" <? if ($d[priority] == 'checked'){ echo "checked";} ?> value="checked">
</td><td align="center" width='25%' style="padding-left:5px">
Amended Affidavit<br>
<input type="checkbox" name="amendedAff" <? if ($d[amendedAff] == 'checked'){ echo "checked";} ?> value="checked">
</td></tr></table></td></tr>
<tr><td align="center" colspan='3'><table align='center' style='font-size:12px;' width='100%'><tr><td align="center">
Request Close<br>
<input type="checkbox" name="request_close" <? if ($d[request_close] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'A'<br>
<input type="checkbox" name="request_closea" <? if ($d[request_closea] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'B'<br>
<input type="checkbox" name="request_closeb" <? if ($d[request_closeb] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'C'<br>
<input type="checkbox" name="request_closec" <? if ($d[request_closec] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'D'<br>
<input type="checkbox" name="request_closed" <? if ($d[request_closed] == 'YES'){ echo "checked";} ?> value="YES">
</td>
<td align="center">
Request Close 'E'<br>
<input type="checkbox" name="request_closee" <? if ($d[request_closee] == 'YES'){ echo "checked";} ?> value="YES">
</td>
</tr></table></td></tr>
<tr>
<td align="center">
<? if ($d[server_id]){ echo id2name($d[server_id]);}else{echo "Server 1";} ?> Complete<br>
<input name="serveComplete" size="1"  value="<?=$d[serveComplete]?>">
</td>
<td align="center">
<? if ($d[server_ida]){ echo id2name($d[server_ida]);}else{echo "Server 2";} ?> Complete<br>
<input name="serveCompletea" size="1"  value="<?=$d[serveCompletea]?>">
</td>
<td align="center">
<? if ($d[server_idb]){ echo id2name($d[server_idb]);}else{echo "Server 3";} ?> Complete<br>
<input name="serveCompleteb" size="1"  value="<?=$d[serveCompleteb]?>">
</td>
</tr>
<tr>
<td align="center">
<? if ($d[server_idc]){ echo id2name($d[server-idc]);}else{echo "Server 4";} ?> Complete<br>
<input name="serveCompletec" size="1"  value="<?=$d[serveCompletec]?>">
</td>
<td align="center">
<? if ($d[server_idd]){ echo id2name($d[server_idd]);}else{echo "Server 5";} ?> Complete<br>
<input name="serveCompleted" size="1"  value="<?=$d[serveCompleted]?>">
</td>
<td align="center">
<? if ($d[server_ide]){ echo id2name($d[server_ide]);}else{echo "Server 6";} ?> Complete<br>
<input name="serveCompletee" size="1"  value="<?=$d[serveCompletee]?>">
</td>
</tr>
</table>





<table width="100%"  id="servers" style="display:none;">
<tr>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server #<?=$d[server_id]?><? if ($d[svrPrint] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_id]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrint] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? if ($d[server_ida]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "a" #<?=$d[server_ida]?><? if ($d[svrPrinta] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_ida]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrinta] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
<? if ($d[server_idb]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "b" #<?=$d[server_idb]?><? if ($d[svrPrintb] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_idb]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrintb] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
</tr>
<tr>
<? if ($d[server_idc]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "c" #<?=$d[server_idc]?><? if ($d[svrPrintc] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_idc]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrintc] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
<? if ($d[server_idd]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "d" #<?=$d[server_idd]?><? if ($d[svrPrintd] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_idd]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrintd] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
<? if ($d[server_ide]){ ?>
<td valign="top">
<FIELDSET>
<LEGEND ACCESSKEY=C>Process Server "e" #<?=$d[server_ide]?><? if ($d[svrPrinte] > 0){echo " - <small>PRINTED</small>";}?></LEGEND>
<?
mysql_select_db("core");
$r2=@mysql_query("select * from ps_users where id = '$d[server_ide]'");
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
?>
<table <? if ($d[svrPrinte] > 0){echo "bgcolor='#FFFFFF'";}?>>
<tr>
<td><?=$d2[company]?></td>
</tr>
<tr>
<td><?=$d2[name]?></td>
</tr>
<tr>
<td><?=$d2[phone]?></td>
</tr>
<tr>
<td><?=$d2[address]?><br><?=$d2[city]?> <?=$d2[state]?> <?=$d2[zip]?></td>
</tr>
</table>    
</FIELDSET>
</td>
<? }?>
<td valign="top">
</td></tr><tr><td>
<?
$q7= "select * from ps_users where contract = 'YES' order by id ASC";
$r7=@mysql_query($q7) or die("Query: $q7<br>".mysql_error());
while ($d7=mysql_fetch_array($r7, MYSQL_ASSOC)) {
	$sList .= "<option value='$d7[id]'>";
	if ($d7[company]){ $sList .= "$d7[company], $d7[name]" ;}else{ $sList .= "$d7[name]" ;}
	$sList .= "</option>";
} ?>
<select name="server1"><? if (!$d[server_id]){ ?><option value="">Select Server </option><? }else{ ?><option value="<?=$d[server_id]?>"><?=id2name($d[server_id]);?> (Server)</option><? } ?>
<?=$sList?>
<option value=""></option>
</select>
<?
foreach(range('a','e') as $letter){
?>
<br />
<select name="server1<?=$letter?>"><? if (!$d["server_id$letter"]){ ?><option value="">Select Server '<?=strtoupper($letter)?>'</option><? }else{ ?><option value="<?=$d["server_id$letter"]?>"><?=id2name($d["server_id$letter"]);?> (Server <?=strtoupper($letter)?>)</option><? } ?>
<?=$sList?>
<option value=""></option>
</select>
<? } ?>
</td>
</tr></table>
</FIELDSET>
<? if ($_GET[start]){
	$src=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
	$src=str_replace('data/service/orders/','PS_PACKETS/',$src);
	$src=str_replace('portal/','',$src);

?>
<iframe height="285px" width="740px" name="QCOTD" src="<?=$src?>"></iframe>
<? } ?>
</td><td valign="top" width="10%">
<?
	$getFolder=getFolder($d[otd]);
	$trioAff='/data/service/orders/'.$getFolder.'/TrioAffidavitService.pdf';
	//if BGW file with client service affidavit, pop open affidavit
	if ($d[status] == 'NEW' && $d[process_status] != 'CANCELLED' && $d[process_status] != 'DUPLICATE' && $d[process_status] != 'DAMAGED PDF' && $d[process_status] != 'DUPLICATE/DIFF-PDF' && $d[attorneys_id] == 70 && file_exists($trioAff)){
		$affPath='http://mdwestserve.com/PS_PACKETS/'.$getFolder.'/TrioAffidavitService.pdf';
		echo "<script>window.open('$affPath','Trio Service Affidavit','width=600, height=800')</script>";
	}
	//only run rfmMerge if file is from BGW, is new, has an existing Request For Mediation uploaded in the same folder as the OTD, and has not already unsuccessfully tried to merge already.
	$rfm='/data/service/orders/'.$getFolder.'/RequestforMediation.pdf';
	if ($d[status] == 'NEW' && $d[process_status] != 'CANCELLED' && $d[process_status] != 'DUPLICATE' && $d[process_status] != 'DAMAGED PDF' && $d[process_status] != 'DUPLICATE/DIFF-PDF' && $d[attorneys_id] == 70 && file_exists($rfm) && ($d[prevOTD] == '' || $d[prevOTD] != $d[otd])){
		$src= "http://staff.mdwestserve.com/temp/rfmMerge.php?packet=$d[packet_id]";
	}elseif($d[status]=="NEW" || $_GET[otd] == '1'){
		$src=str_replace('portal//var/www/dataFiles/service/orders/','PS_PACKETS/',$d[otd]);
		$src=str_replace('data/service/orders/','PS_PACKETS/',$src);
		$src=str_replace('portal/','',$src);
		
		//$src=str_replace('mdwestserve.com','alpha.mdwestserve.com',$src);
		/*if (!$src){
			$src=$d[otd];
		}*/
	}elseif(!$d[uspsVerify]){
		$src="supernova.php?packet=$d[packet_id]";
	}elseif(!$d[caseVerify] && $d[case_no]){
		$src="validateCase.php?case=$d[case_no]&packet=$d[packet_id]&county=$d[circuit_court]";
	}elseif(!$d[qualityControl]){
		if ($d[service_status] == 'MAIL ONLY'){
			$src="entryVerify.php?packet=$d[packet_id]&frame=no&matrix=1";
		}else{
			$src="entryVerify.php?packet=$d[packet_id]&frame=no";
		}
	}elseif(!$d[caseVerify]){
		$src="validateCase.php?case=$d[case_no]&packet=$d[packet_id]&county=$d[circuit_court]";
	}elseif($d[process_status] == "CANCELLED" || $d[filing_status]=="FILED WITH COURT" || $d[filing_status]=="FILED WITH COURT - FBS"){
		$src="http://staff.mdwestserve.com/otd/minips_pay.php?id=$d[packet_id]";
	}else{
		$src="serviceReview.php?packet=$d[packet_id]"; 
	}

	$explode = explode("/",$d[otd]);
	$explodeCount=count($explode)-1;
?>
<table style="padding:0px;" width="100%">
	<tr>
		<td style='font-size:12px;' valign="bottom"><input name="pages" value="<?=$d[pages]?>" size="3"> # OTD Pages <?=testLink($d[otd])?> <b style="background-color:#FFFF00; padding:0px;"><?=trim($explode["$explodeCount"])?></b></td>
		<td style='font-size:12px;' valign="bottom"><input name="mailWeight" size="4" value="<?=$d[mailWeight]?>"> Mail Weight</td></form>
		<form action="http://staff.mdwestserve.com/temp/pageRemove.php"><td valign="bottom"><input type="hidden" name="id" value="<?=$d[packet_id]?>"><input type="hidden" name="type" value="OTD"><? if ($_GET[packet]){ ?><input type="hidden" name="packet" value="<?=$d[packet_id]?>"><? } ?><input name="skip" onclick="value=''" value="Remove Page #"> <input type="submit" value="GO!"></td></form>
	</tr>
	<tr>
		<td colspan="3" valign="bottom">
		<input name="otd" value="<?=$d[otd]?>" size="80"> <? if($d[status]=="NEW"){ echo "<a href='renameOTD.php?packet=$d[packet_id]&test=1'>FIX OTD LINK</a>";}else{echo "<a href='renameOTD.php?packet=$d[packet_id]'>FIX</a>";} ?><?=searchList($d[packet_id]);?>
		</td>
	</tr>
</table>
<? if (webservice($d[client_file]) && ($d[attorneys_id] == 1)){
		echo "<table align='center'><tr><td>";
		include "http://staff.mdwestserve.com/otd/webservice.php?fileNumber=$d[client_file]";
		echo "</td></tr></table>";
	}
?>
<iframe height="622px" width="900px" name="preview" id="preview" src="<?=$src?>" ></iframe>
</td></tr></table>
<? }?>
<script>document.title='<?=$_GET[packet]?>|<?=$d[status]?>|<?=$d[service_status]?>|<?=$d[process_status]?>|<?=$d[affidavit_status]?>|<?=$d[filing_status]?>|<?=$d[affidavit_status2]?>'</script>
<? 
if ($_GET[type]){
	echo $_GET[type];
}
$r=@mysql_query("select * from fileWatch where clientFile = '$d[client_file]'");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<script>alert('".addslashes($d[message])."');</script>";
}

include 'footer.php';
?>