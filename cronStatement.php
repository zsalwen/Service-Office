<?
session_start();
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
function leading_zeros($value, $places){
    if(is_numeric($value)){
        for($x = 1; $x <= $places; $x++){
            $ceiling = pow(10, $x);
            if($value < $ceiling){
                $zeros = $places - $x;
                for($y = 1; $y <= $zeros; $y++){
                    $leading .= "0";
                }
            $x = $places + 1;
            }
        }
        $output = $leading . $value;
    }
    else{
        $output = $value;
    }
    return $output;
}
function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
	return $d[display_name];
}
function buildList($date){
	$r=@mysql_query("select DISTINCT attorneys_id from ps_packets where date_received like '$date%'");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list .= id2attorney($d[attorneys_id]).", ";
		getPage('http://data.mdwestserve.com/statement.php?attid='.$d[attorneys_id].'&monthly='.$date.'&type=OTD','mdwestserve.com','5','');
		getPage('http://data.mdwestserve.com/statement.php?attid='.$d[attorneys_id].'&monthly='.$date.'&type=OTD&format=csv','mdwestserve.com','5','');
		$_SESSION[OTDmail][$d[attorneys_id]][$date] = "OTD-$d[attorneys_id]-$date.xml";
	}
	return $list;
}
function buildList2($date){
	$r=@mysql_query("select DISTINCT attorneys_id from evictionPackets where date_received like '$date%'");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$list .= id2attorney($d[attorneys_id]).", ";
		getPage('http://data.mdwestserve.com/statement.php?attid='.$d[attorneys_id].'&monthly='.$date.'&type=EV','mdwestserve.com','5','');
		getPage('http://data.mdwestserve.com/statement.php?attid='.$d[attorneys_id].'&monthly='.$date.'&type=EV&format=csv','mdwestserve.com','5','');
		$_SESSION[EVmail][$d[attorneys_id]][$date] = "EV-$d[attorneys_id]-$date.xml";
	}
	return $list;
}
mysql_connect();
mysql_select_db('core');
$yearStart = 2008;
$yearEnd = date('Y');
$monthEndA = 12;
$monthEndB = date('m');
$_SESSION[OTDmail] = array();
$_SESSION[EVmail] = array();
while ($yearStart <= $yearEnd){
$monthStart = 01;
	if ($yearStart == $yearEnd){
		while ($monthStart <= $monthEndB){
			$checkDate = "$yearStart-".leading_zeros($monthStart,2);
			buildList($checkDate);
			buildList2($checkDate);
		$monthStart++;
		}
	}else{
		while ($monthStart <= $monthEndA){
			$checkDate = "$yearStart-".leading_zeros($monthStart,2);
			buildList($checkDate);
			buildList2($checkDate);
			$monthStart++;
		}
	}
$yearStart++;
}
function sendStatement($attid,$fileArray,$type){
	$to = "Accounting Department <service@mdwestserve.com>";
	//$to = "Accounting Department <patrick@mdwestserve.com>"; // debug only
	$subject = "New MDWestServe $type Statements xml and csv (".id2attorney($attid).")";
	$headers  = "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$headers .= "From: $to \n";
	$headers .= "Cc: Patrick McGuire <patrick@mdwestserve.com> \n";
	$base = "http://data.mdwestserve.com/getXML.php?data=";
	$base2 = "http://data.mdwestserve.com/getCSV.php?data=";
	$body = "
	<div>
		MDWestServe up-to-date auto-statements.<br>
		If you have any problems loading the statement in excel<br>
		contact Patrick at <b>410-616-8878</b> (office) or <b>443-386-2584</b> (cell).
	</div>
	";
	foreach ($fileArray as $key => $value) {
		$body .= "<fieldset><legend>Statement for: $key</legend>";
		$body .= "<li><a href='$base/$value'>Microsoft Excel XP, 2003, 2007</a></li>
		";
		$value = str_replace('xml','csv',$value);
		$body .= "<li><a href='$base2/$value'>Microsoft Excel 2002, 2000, Open Office</a></li>
		";
		$body .="</fieldset>";
	}
	$r=@mysql_query("select statement_to from attorneys where attorneys_id = '$attid'");
	$d=mysql_fetch_array($r, MYSQL_BOTH);
	$c=-1;
	$cc = explode(',',$d[statement_to]);
	$ccC = count($cc)-1;
	while ($c++ < $ccC){
	$headers .= "Cc: Accounting Deptartments <".$cc[$c]."> \n";
	}
	mail($to,$subject,$body,$headers);
}
foreach ($_SESSION[OTDmail] as $key => $value) {
    sendStatement($key,$value,'Presale');
}
foreach ($_SESSION[EVmail] as $key => $value) {
    sendStatement($key,$value,'Eviction');
}
?>
