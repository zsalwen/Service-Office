<?
mysql_connect();
mysql_select_db('core');
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
function hardLog($str){
	error_log(date('h:iA n/j/y')." ".$_COOKIE[psdata][name]." ".$_SERVER["REMOTE_ADDR"]." ".trim($str)."\n", 3, "/logs/cancelled.log");
}
function searchTimeline($packet,$term){
	$q="SELECT timeline FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if (strpos(strtoupper($d[timeline]),strtoupper($term))){
		return 1;
	}else{
		return 0;
	}
}
function getTimeline($packet){
	$q="SELECT timeline FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[timeline];
}
function append2timeline($packet,$str){
	$timeline= getTimeline($packet);
	$timeline .= $str;
	@mysql_query("UPDATE ps_packets SET timeline='$timeline' WHERE packet_id='$packet'");
}
function month2num($month){
	if (strtoupper($month) == 'JANUARY' || strtoupper($month) == 'JAN'){
		return '01';
	}elseif (strtoupper($month) == 'FEBRUARY' || strtoupper($month) == 'FEB'){
		return '02';
	}elseif (strtoupper($month) == 'MARCH' || strtoupper($month) == 'MAR'){
		return '03';
	}elseif (strtoupper($month) == 'APRIL' || strtoupper($month) == 'APR'){
		return '04';
	}elseif (strtoupper($month) == 'MAY'){
		return '05'; 
	}elseif (strtoupper($month) == 'JUNE' || strtoupper($month) == 'JUN'){
		return '06';
	}elseif (strtoupper($month) == 'JULY' || strtoupper($month) == 'JUL'){
		return '07';
	}elseif (strtoupper($month) == 'AUGUST' || strtoupper($month) == 'AUG'){
		return '08';
	}elseif (strtoupper($month) == 'SEPTEMBER' || strtoupper($month) == 'SEP'){
		return '09';
	}elseif (strtoupper($month) == 'OCTOBER' || strtoupper($month) == 'OCT'){
		return '10';
	}elseif (strtoupper($month) == 'NOVEMBER' || strtoupper($month) == 'NOV'){
		return '11';
	}elseif (strtoupper($month) == 'DECEMBER' || strtoupper($month) == 'DEC'){
		return '12'; 
	}else{
		return $month;
	}
}
function dateExplode($date){
	$date=explode(' ',$date);
	$year=str_replace('20','',$date[2]);
	$month=month2num($date[1]);
	$day=$date[0];
	if ($day < 10){
		$day = '0'.$day;
	}
	$date=$month."/".$day."/".$year.' '.$date[3];
	return $date;
}
function dateExplode2($date){
	$date=explode(' ',$date);
	$year=$date[2];
	$month=month2num($date[1]);
	$day=$date[0];
	if ($day < 10){
		$day = '0'.$day;
	}
	$date=$year."-".$month."-".$day;
	return $date;
}
function searchFileDate($packet){
	$q="SELECT fileDate FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[fileDate] != '0000-00-00'){
		return 1;
	}else{
		return 0;
	}
}
function getFileDate($packet){
	$q="SELECT fileDate FROM ps_packets WHERE packet_id='$packet'";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	return $d[fileDate];
}
function updateFileDate($packet,$date){
	@mysql_query("UPDATE ps_packets SET fileDate='$date' WHERE packet_id='$packet'");
}
?>
<style>
.a {border-style:solid 5px; background-color:#FFFFCC; padding:0px; width:500px;}
</style>
<?
$html = getPage('http://staff.mdwestserve.com/cancelled.txt','mdwestserve.com','5','');
$msg = explode('From - ',$html);
$i=0;
while ($i < (count($msg) - 1)){$i++;
	echo "<fieldset>";
	$explode1=explode('Subject: ',$msg["$i"]);
	$subject=explode(' (',$explode1[1]);
	$packet=explode('Packet ',$subject[0]);
	$packet=explode(" (",$packet[1]);
	$packet=trim($packet[0]);
	if (strlen($packet) > 4){
		$packet=explode(" By",$packet);
		$packet=trim($packet[0]);
	}
	//now we check to see if this info should be appended to the packet's timeline
	$searchFileDate = searchFileDate($packet);
	if ($searchFileDate != '1'){
		$timeline = "<br>NEEDS UPDATE";
		$bg="#FF0000";
	}else{
		$timeline = "<fieldset class='a'>".getFileDate($packet)."</fieldset>";
		$bg="#00FF00";
	}
	//back to exploding and echoing
	echo "<legend style='background-color:$bg;'>$packet</legend>";
	if (trim($subject[0]) != ''){
		$subject=trim($subject[0]);
		echo "Subject: ".$subject."<br>";
	}
	$explode2=explode("<br>",$msg["$i"]);
	$body=explode(", closeout documents as follows:",$explode2[1]);
	if (trim($body[0]) != ''){
		if (strpos($body[0],"<table>")){
			$body=explode("<table>",$body[0]);
			$body=$body[0];
		}else{
			$body=$body[0];
		}
		echo "Body: ".$body."<br>";
	}
	$explode3=explode("Date: ",$msg["$i"]);
	$date=explode(', ',$explode3[1]);
	$date=explode('-',$date[1]);
	$date=dateExplode2(trim($date[0]));
	echo "Date: ".$date;
	echo "$timeline</fieldset>";
	if ($searchFileDate != '1'){
		updateFileDate($packet,$date);
	}
}
die();
?>