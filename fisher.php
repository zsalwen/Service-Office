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

?>


<? 
$html = getPage('http://www.first-legal.com/salelist/sales.html','anarchy.com','5','');

$cut = explode('<td nowrap align=right><b>Deposit Amount</b></td>',$html);
$cut = explode('</table></body></html>',$cut[1]);

$data = str_replace(',','',$data); // remove commas!



$data = str_replace('<tr valign=top>
','[BREAK1]
',$cut[0]);
$data = str_replace('</tr>
','',$data);
$data = str_replace('
','',$data);

$data = str_replace('</tr>','[BREAK2]',$data);

$data = str_replace('[BREAK2][BREAK1]','',$data);
$data = str_replace('[BREAK1]','[LOOP]',$data);

$data = str_replace('<td nowrap>','',$data);
$data = str_replace('<td colspan=3>&nbsp;</td>','',$data);
$data = str_replace('<td nowrap align=right>','',$data);
$data = str_replace('</td>','[SPLIT]',$data);

//$data = str_replace(' ','.',$data);


$loop = explode('[LOOP]',$data);
$new=0;
$counter = 0;
$items = count($loop);
while ($counter < $items){
$counter++;
echo "<div style='border:solid 1px;'>";

$split = explode('[SPLIT]',$loop[$counter]);

$key = $split[2];
$county = $split[1];
$address = $split[3]." ".$split[6];
$notes = $split[0]." ".$split[4]." ".$split[5]." ".$split[7];


$r=@mysql_query("select id from fisherSales where id='$key' and notes = '$notes'");
if (!$d=mysql_fetch_array($r,MYSQL_ASSOC)){
$new++;
echo "Key: $key<br>";
echo "County: $county<br>";
echo "Address: $address<br>";
echo "Notes: $notes<br>";

@mysql_query("insert into fisherSales (id,online,county,address,notes) values ('$key',NOW(),'$county','$address','$notes') ");
echo mysql_error();	
}
echo "</div>";
}

if ($new){
mail('service@mdwestserve.com',$new.' New Fisher Sales','Database updated for new auctions.'); 
}


?>

