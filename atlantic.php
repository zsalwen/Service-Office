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

$html = getPage('http://client.tidewaterauctions.com/public/PublicMAA.aspx','anarchy.com','20','');
$cut = explode('<table cellspacing="0" rules="all" border="1" id="ctl00_ContentPlaceHolder1_gvstatus" height="157" width="711">',$html);
$cut = explode('</table>',$cut[1]);
$data = str_replace('<font face="Arial" size="2">','',$cut[0]);
$data = str_replace('</font>','',$data);
$data = str_replace('<th scope="col">','',$data);
$data = str_replace('<font face="Arial" color="Blue" size="2">','',$data);
$data = str_replace('<td>','',$data);
$data = str_replace('<a href="javascript:__doPostBack(\'ctl00$ContentPlaceHolder1$gvstatus\',\'Sort$Saledate\')">','',$data);
$data = str_replace('<a href="javascript:__doPostBack(\'ctl00$ContentPlaceHolder1$gvstatus\',\'Sort$Countyname\')">','',$data);
$data = str_replace('<span id="ctl00_ContentPlaceHolder1_gvstatus_ct','[WEBID',$data);
$data = str_replace('_lblsalid">',']',$data);
$data = str_replace('<font color="Blue">','',$data);
$data = str_replace('</span>','',$data);
$data = str_replace('</a>','',$data);
$data = str_replace('<a id="ctl00_ContentPlaceHolder1_gvstatus_ct','[WEBID',$data);
$data = str_replace('</tr>','',$data);
$data = str_replace('">Map','',$data);
$data = str_replace('_hypSale" href="http://maps.google.com/maps?daddr=','MAP]',$data);
$data = str_replace('<strike>','',$data);
$data = str_replace('</strike>','',$data);
$data = str_replace('<td nowrap="nowrap">','',$data);


$data = str_replace('</td>','[SPLIT]',$data);
$data = str_replace('<tr>','[LOOP]',$data);

function cleanString($id,$str){
	$remove1 = "[WEBID".trim($id)."]";
	$remove2 = "[WEBID".trim($id)."MAP]";
	$str2 = str_replace($remove1,'',$str);
	$str2 = str_replace($remove2,'',$str2);
return $str2;
}

$loop = explode('[LOOP]',$data);
$new=0;
$counter = 0;
$items = count($loop);
while ($counter < $items){
$counter++;
echo "<div style='border:solid 1px;'>";

$split = explode('[SPLIT]',$loop[$counter]);


$webID = $split[0];
$webIDMain = str_replace('[WEBID','',$webID);
$webIDSub = explode(']',$webIDMain);
$webID = $webIDSub[0];
$webClient = $webIDSub[1];

$key = '';
$county = cleanString($webID,$split[7]);
$address = cleanString($webID,$split[4]);
$notes =   cleanString($webID,$split[0])." ".cleanString($webID,$split[2]);

$r=@mysql_query("select address from atlanticSales where address='$address' and notes = '$notes'");
if (!$d=mysql_fetch_array($r,MYSQL_ASSOC)){
if ($address){
$new++;

echo "Key: $key<br>";
echo "County: $county<br>";
echo "Address: $address<br>";
echo "Notes: $notes<br>";

@mysql_query("insert into atlanticSales (id,online,county,address,notes) values ('$key',NOW(),'".addslashes($county)."','".addslashes($address)."','$notes') ");
echo mysql_error();	
}}

echo "</div>";
}

if ($new){
mail('service@mdwestserve.com',$new.' New Atlantic Sales','Database updated for new auctions.'); 
}

//echo "<pre>".htmlspecialchars($cut[1])."</pre>";
?>