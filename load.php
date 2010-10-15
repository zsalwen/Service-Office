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
		
		$r2=@mysql_query("select packet_id from ps_packets where date_received like '$date%' and attorneys_id = '$d[attorneys_id]' and status <> 'FILE COPY' AND status <> 'DAMAGED PDF' AND status <> 'CANCELLED'");
		$c=mysql_num_rows($r2);
		$list .= "<li>$date-OTD) ".id2attorney($d[attorneys_id])." $c Files</li>";

	}
	return $list;
}
function buildList2($date){
	$r=@mysql_query("select DISTINCT attorneys_id from evictionPackets where date_received like '$date%'");
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$r2=@mysql_query("select eviction_id from evictionPackets where date_received like '$date%' and attorneys_id = '$d[attorneys_id]' and status <> 'FILE COPY' AND status <> 'DAMAGED PDF' AND status <> 'CANCELLED'");
		$c=mysql_num_rows($r2);
		$list .= "<li>$date-EV) ".id2attorney($d[attorneys_id])." $c Files</li>";

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
			echo buildList($checkDate);
			echo buildList2($checkDate);
		$monthStart++;
		}
	}else{
		while ($monthStart <= $monthEndA){
			$checkDate = "$yearStart-".leading_zeros($monthStart,2);
			echo buildList($checkDate);
			echo buildList2($checkDate);
			$monthStart++;
		}
	}
$yearStart++;
}
?>
