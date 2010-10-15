<?
mysql_connect();
mysql_select_db('hacking');
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
/*$now=time();
while (date('w',$now) != 0){
	$now=$now-86400;
}
$date=date('Y-m-d',$date);*/
if ($_GET[date]){
	$date=$_GET[date];
}else{
	$date=date('Y-m-d');
}
$html = getPage('http://realestate.alexcooper.com/print_foreclosures.php?week='.$date.'&day=%&county=%','anarchy.com','20','');
$cut = explode('<div id="dataarea">',$html);
$cut = explode('<script',$cut[1]);
$data=str_replace('<div style="font-size: 18px; font-weight: bold; color: #241600; margin-bottom: 8px;">','',$cut[0]);
$data=str_replace('<div class="foreclosureContainer">','[LOOP1]',$data);
$data=str_replace('<div style="margin-bottom: 10px;">','',$data);
$data=str_replace('<div class="foreclosureContainerAlt">','[LOOP1]',$data);
$data=str_replace('<div style="color: #241600; font-weight: bold; margin-bottom: 5px; margin-top: 10px;">','[LOOP2]',$data);
$data=str_replace('<div style="font-size: 12px;">','[LOOP3]',$data);
$data=str_replace('<span style="color: #241600; font-weight: bold;">','',$data);
$data=str_replace('</div>','
',$data);
$data=str_replace('</span>','',$data);
$data=str_replace('<br clear="all" />','',$data);
$data=str_replace('<b>','',$data);
$data=str_replace('<br />','',$data);
$data=str_replace('<del>','',$data);
$data=str_replace('</b>','',$data);
$data=str_replace('</del>','',$data);
$data=str_replace('-','',$data);


$loop = explode('[LOOP1]',$data);
$new=0;
$counter = 0;
$items = count($loop);
while ($counter < $items){
	$counter++;
	echo "<div>";

	$date=explode('[LOOP2]',$loop[$counter]);
	$date=$date[0];
	//echo "<h1>$date</h1>";
	$loop2 = explode('[LOOP2]',$loop[$counter]);
	$counter2 = 0;
	$items2 = count($loop2);
	while ($counter2 < $items2){
		$counter2++;
		echo "<div>";
		$county=explode('[LOOP3]',$loop2[$counter2]);
		$county=$county[0];
		//echo "<h2>$county</h2>";
		$loop3 = explode('[LOOP3]',$loop2[$counter2]);
		$counter3 = 0;
		$items3 = count($loop3);
		while ($counter3 < $items3){
			$counter3++;
			echo "<div>";
			
			
			$saledata=$loop3[$counter3];
			$saledata=str_replace('AM','AM [SPLIT]',$saledata);
			$saledata=str_replace('PM','PM [SPLIT]',$saledata);
			$saledata=str_replace('Dep.','[SPLIT] Dep. ',$saledata);
			$split = explode('[SPLIT]',$saledata);

			$key = '';
			$county = $county;
			$address = $split[1];
			$notes = $date." ".$split[0]." ".$split[2];
			$r=@mysql_query("select address from cooperSales where address='".addslashes($address)."' and notes = '".addslashes($notes)."'");
			if (!$d=mysql_fetch_array($r,MYSQL_ASSOC)){
				if ($address){
					$new++;

					echo "Key: $key<br>";
					echo "County: $county<br>";
					echo "Address: $address<br>";
					echo "Notes: $notes<br>";

					@mysql_query("insert into cooperSales (id,online,county,address,notes) values ('$key',NOW(),'".addslashes($county)."','".addslashes($address)."','".addslashes($notes)."') ");
					echo mysql_error();	
				}
			}
			echo "</div>";
		}
		echo "</div>";
	}


	//echo $loop[$counter];

	echo "</div>";
}
if ($new){
mail('service@mdwestserve.com',$new.' New Cooper Sales','Database updated for new auctions.'); 
}


//$return=$data;
//echo "<hr><pre>".htmlspecialchars($return)."</pre>";
?>