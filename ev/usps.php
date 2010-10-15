<?
set_time_limit(2);
function pullHTML($url, $referer, $timeout, $header){
if(!isset($timeout))
$timeout=5;
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
function xmlplode($start,$end,$data){
$cut1 = explode($start,$data);
$cut2 = explode($end,$cut1[1]);
return $cut2[0];
}
function errorDecode($xml){
if ($xml){
$Number = xmlplode('<Number>','</Number>',$xml);
$Source = xmlplode('<Source>','</Source>',$xml);
$Description = xmlplode('<Description>','</Description>',$xml);
$HelpFile = xmlplode('<HelpFile>','</HelpFile>',$xml);
$HelpContext = xmlplode('<HelpContext>','</HelpContext>',$xml);
return "USPS Error $Number<br>($Source)<br><strong>$Description</strong><br>";
}else{
return "USPS Database Busy (No Data Returned)";
}
}
function trackingDecode($xml){
$Summary = xmlplode('<TrackSummary>','</TrackSummary>',$xml);
if ($Summary){
$html = "<b>".$Summary."</b><br>";
$raw = explode('<TrackDetail>',$xml);
$results = count($raw);
$i=0;
while ($i < $results){
$fresh = $raw[$i];
$done = explode('<\TrackDetail>',$fresh);
$html .= "<li>".$done[0]."</li>";
$i++;	
}
}else{
$html .= errorDecode($xml);
}
//$html = htmlspecialchars($xml);
return $html;
}
function pullTracking($track){
$tracking = str_replace(' ','',$track);
$a='http://production.shippingapis.com/ShippingAPI.dll?API=TrackV2&XML=';
$b = '<TrackRequest USERID="648HARVE5710"><TrackID ID="'.$tracking.'"></TrackID></TrackRequest>';
$c=urlencode($b);
$xml = pullHTML($a.$c,'mdwestserve.com','5','');
$box = trackingDecode($xml);
if ($box){
return $box;
}else{
return "USPS Database Busy (Pull Tracking)";
}
}
function addressDecode($xml){
$Address1 = xmlplode('<Address1>','</Address1>',$xml);
$Address2 = xmlplode('<Address2>','</Address2>',$xml);
$City = xmlplode('<City>','</City>',$xml);
$State = xmlplode('<State>','</State>',$xml);
$Zip5 = xmlplode('<Zip5>','</Zip5>',$xml);
// gonna drop a session var for dispatcher.php
$_SESSION[dropZIP] = $Zip5;
$Zip4 = xmlplode('<Zip4>','</Zip4>',$xml);
if ($Zip5){
$html = "<strong>VALID USPS ADDRESS</strong><br>$Address2<br>$Address1<br>$City, $State $Zip5-$Zip4";
}else{
$html = errorDecode($xml);
}
//$html = htmlspecialchars($xml);
return $html;
}
function clean($str){
return $str;
}
function addressLookup($id,$name,$aptsut,$address,$city,$state,$zip5,$zip4){
if (!isset($id)){$id='0';}
$a='http://production.shippingapis.com/ShippingAPI.dll?API=Verify&XML=';
$b = '<AddressValidateRequest USERID="648HARVE5710"><Address ID="'.$id.'"><Address1>'.clean($aptsut).'</Address1><Address2>'.clean($address).'</Address2><City>'.clean($city).'</City><State>'.clean($state).'</State><Zip5>'.clean($zip5).'</Zip5><Zip4>'.clean($zip4).'</Zip4></Address></AddressValidateRequest>';
$c=urlencode($b);
$xml = pullHTML($a.$c,'mdwestserve.com','5','');
$box = addressDecode($xml);
if ($box){
return $box;
}else{
return "USPS Database Busy (Pull Tracking)";
}
}
if ($_GET[track]){
echo pullTracking($_GET[track]);?>
<style>body { margin:0px; padding:0px;}</style><?
}
if ($_GET[address]){
echo addressLookup('0',$_GET[name],$_GET[aptsut],$_GET[address],$_GET[city],$_GET[state],$_GET[zip5],$_GET[zip4]);?>
<style>body { margin:0px; padding:0px;}</style>
<?
}
?>
