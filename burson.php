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
$html = getPage('http://www.bursonlaw.com/html/current_sales.php','anarchy.com','20','');

$cut = explode('SALE TIME',$html);
$cut = explode('</table>',$cut[1]);

 



$data = str_replace("<td width='232'><p>",'[BREAK1]',$cut[0]);
$data = str_replace('</td></tr>','[BREAK2]
',$data);


$data = str_replace('</h2>','',$data);
$data = str_replace('</tr>','',$data);
$data = str_replace('<tr>','',$data);
$data = str_replace('<p>','',$data);
$data = str_replace('</p>','',$data);
$data = str_replace("</td><td width='60'>",'[SPLIT]',$data);
$data = str_replace("</td><td width='100'>",'[SPLIT]',$data);
$data = str_replace("</td><td align='center'>",'[SPLIT]',$data);
$data = str_replace("</td>",'',$data);


$data = str_replace('[BREAK1]','[LOOP]',$data);
$data = str_replace('[BREAK2]','',$data);

/*$data = str_replace('
','',$data);

$data = str_replace('</tr>','[BREAK2]',$data);

$data = str_replace('[BREAK2][BREAK1]','',$data);


$data = str_replace('<td nowrap>','',$data);
$data = str_replace('<td colspan=3>&nbsp;</td>','',$data);
$data = str_replace('<td nowrap align=right>','',$data);
$data = str_replace('</td>','[SPLIT]',$data);

//$data = str_replace(' ','.',$data);
*/

$loop = explode('[LOOP]',$data);
$new=0;
$counter = 0;
$items = count($loop);
while ($counter < $items){
$counter++;
echo "<div style='border:solid 1px;'>";

$split = explode('[SPLIT]',$loop[$counter]);

$key = $split[3];
$county = $split[2];
$address = $split[0]." ".$split[1];
$notes = $split[4]." ".$split[5];





$r=@mysql_query("select id from bursonSales where id='$key' and notes = '$notes'");
if (!$d=mysql_fetch_array($r,MYSQL_ASSOC)){
if ($key){
$new++;
echo "Key: $key<br>";
echo "County: $county<br>";
echo "Address: $address<br>";
echo "Notes: $notes<br>";

@mysql_query("insert into bursonSales (id,online,county,address,notes) values ('$key',NOW(),'".addslashes($county)."','".addslashes($address)."','$notes') ");
echo mysql_error();	
}}

echo "</div>";
}
if ($new){
mail('service@mdwestserve.com',$new.' New Burson Sales','Database updated for new auctions.'); 
}




?>

