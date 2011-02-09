<?
//error_log(date('r')." Start Watchdog Search  \n", 3, '/logs/watchdog.log');
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
function casesOnline($first,$last,$county,$company){
	$page = 'http://data.mdwestserve.com/caseSearch.php?firstName='.$first.'&lastName='.$last.'&county='.str_replace(' ','',$county).'&company='.$company;
	$page = $page;
	$html = getPage($page, '', '5', '');
	//echo "<li>$page</li>";
	if($html != '1'){ 
		$html = explode(' ',$html);
		if ($html[0] == 'One'){
			return "1";
		}else{
			return $html[0];
		}
	}else{
		return "0";
	}
}
ob_start();
mysql_connect();
mysql_select_db('service');
// first we have to build the search table ?? manual for now...

// loop through watchDog table
$rOut = @mysql_query("select * from watchDog where status <> 'Search Complete'") or die (mysql_error());
while ($dOut=mysql_fetch_array($rOut,MYSQL_ASSOC)){
	$id = $dOut['watchID'];
	$response = casesOnline($dOut['firstName'],$dOut['lastName'],$dOut['county'],$dOut['company']);
	//echo "<li>$dOut[firstName], $dOut[lastName], $dOut[county]: $response</li>";
	if (!$dOut['response'] && !$dOut['status']){
		@mysql_query("UPDATE watchDog set status = 'Case Watch Started', response = '$response', watchStart = NOW(), lastChecked = NOW() where watchID = '$id'") or die (mysql_error());
		//error_log(date('r')." Started Watch: Current cases found under ".$dOut['firstName']." ".$dOut['lastName']." in ".$dOut['county'].", ".$response." cases \n", 3, '/logs/watchdog.log');
		//mail('service@mdwestserve.com','Started Case Watch Dog for '.strtoupper($dOut['firstName']).' '.strtoupper($dOut['lastName']),'Current cases found under '.$dOut['firstName'].' '.$dOut['lastName'].' in '.$dOut['county'].', '.$response.' cases.');
	}elseif (trim($dOut['response']) < trim($response)){ 
		@mysql_query("UPDATE watchDog set status = 'New Case Found', lastResult = '$response', lastChecked = NOW() where watchID = '$id'") or die (mysql_error());
		//error_log(date('r').' New case found under '.$dOut['firstName'].' '.$dOut['lastName'].' in '.$dOut['county'].', '.$response.' cases (up from '.$dOut['response'].')'."  \n", 3, '/logs/watchdog.log');
		//mail('service@mdwestserve.com','Case Watch Dog for '.strtoupper($dOut['firstName']).' '.strtoupper($dOut['lastName']),'New case found under '.$dOut['firstName'].' '.$dOut['lastName'].' in '.$dOut['county'].', '.$response.' cases (up from '.$dOut['response'].')');
	}else{
		@mysql_query("UPDATE watchDog set status='Searching...', lastChecked = NOW(), lastResult = '$response' where watchID = '$id'") or die (mysql_error());
	}
}
//error_log(date('r')." Watchdog Search Complete \n", 3, '/logs/watchdog.log');
$result=ob_get_clean();
if (trim($result) != ''){
	error_log(date('r')." Watchdog Error: $result \n", 3, '/logs/watchdog.log');
}
?>
