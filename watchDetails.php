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
function casesOnline($first,$last,$county,$packet,$defendant){
	$page = 'http://data.mdwestserve.com/caseExpand.php?firstName='.$first.'&lastName='.$last.'&county='.str_replace(' ','',$county);
	$page = $page;
	$html = getPage($page, '', '5', '');
	// ok lets tear this apart
	if ($html = explode('<tbody>',$html)){
		$html = str_replace('caseId=','[AAA]',$html[1]);
		$html = str_replace('&amp;loc=','[BBB]',$html);
		$html = str_replace('</a>','[CCC]',$html);
		$html = explode('</tbody>',$html);
		// ready for loop and process
		$master = explode('[CCC]',$html[0]);
		$html='';
		$loopA=0;
		$stop = count($master);
		while($loopA < $stop){
			$sub = explode('[AAA]',$master[$loopA]);
			$sub = explode('[BBB]',$sub[1]);
			$case = $sub[0];
			if ($case){
				@mysql_query("insert into marylandCaseData (caseNumber, startDate, searchFirstName, searchLastName, searchCounty, packet, defendant) values ('$case', NOW(), '".addslashes($first)."','".addslashes($last)."','".addslashes($county)."', '$packet', '$defendant')");
				$html .= "<li>$case</li>";
			}	
			$loopA++;
		}
		//echo "<div style='border:solid 10px #ff0;'>$page</div>";
		//echo "<div style='border:solid 10px #0ff;'>$html</div>";
	}
}
mysql_connect();
mysql_select_db('core');
// loop through watchDog table
$rOut = @mysql_query("select * from watchDog where status = 'SEARCHING...'");
while ($dOut=mysql_fetch_array($rOut,MYSQL_ASSOC)){
	error_log("[".date('r')."] [watchDetails] [".$dOut['firstName']."] [".$dOut['lastName']."] [".$dOut['county']."] [".$dOut['packetID']."] [".$dOut['defID']."] \n", 3, '/logs/webservice.log');
	$response = casesOnline($dOut['firstName'],$dOut['lastName'],$dOut['county'],$dOut['packetID'],$dOut['defID']);
}

?>
