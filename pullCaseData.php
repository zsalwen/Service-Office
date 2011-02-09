<?
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
function caseDetail($caseNumber){
	$page = 'http://data.mdwestserve.com/marylandCaseDetail.php?caseNumber='.$caseNumber;
	$page = $page;
	$html = getPage($page, '', '5', '');
	$test=trim(substr($html,0,10));	
	if ($test == "<html>"){
		$status = "District Court Case";
		error_log("[".date('r')."] [pullCaseData] [Case Removed] [District Court Case] [$_GET[restart]] \n", 3, '/logs/webservice.log');
	}else{
		$status = "Active";
		error_log("[".date('r')."] [pullCaseData] [Active] \n", 3, '/logs/webservice.log');
	}
		//echo "<div style='border:solid 10px #ff0;'>$test</div>";
		@mysql_query("update marylandCaseData set status = '$status', details = '$html' where caseNumber = '$caseNumber'");
		echo "<div style='border:solid 10px #f0f;'>".htmlspecialchars($test)." - $status</div>";
		//echo "<div style='border:solid 10px #0ff;'><pre>".htmlspecialchars($html)."</pre></div>";
}
mysql_connect();
mysql_select_db('service');
// loop through watchDog table
$rOut = @mysql_query("select caseNumber from marylandCaseData where status = 'Active'");
while ($dOut=mysql_fetch_array($rOut,MYSQL_ASSOC)){
	$response = caseDetail($dOut['caseNumber']);
}
?>
