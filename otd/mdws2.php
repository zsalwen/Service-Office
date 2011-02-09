<?

function buildPath($link){
$newLink  = str_replace('http://mdws2.mdwestserve.com/PS_PACKETS/','/data/service/orders/',$link);



return $newLink;
}
function buildPath2($link){
$newLink  = str_replace('/data/service/orders/','PS_PACKETS/',$link);
$newLink  = str_replace(':','_',$newLink);



return $newLink;
}
function buildFile($path){
$parts = explode('/',$path);
return $parts[count($parts)-1];
}
// set up basic connection
?>
<table border="1">
	<tr>
		<td><b>Packet ID</b></td>
		<td><b>New Database Links</b></td>
<?
function cleanPath($link){
$newLink  = str_replace('portal//var/www/dataFiles/service/orders','PS_PACKETS',$link);
$newLink  = str_replace('ps/','',$newLink);
$newLink  = str_replace('http://','httpX//',$newLink);
$newLink  = str_replace(':','_',$newLink);
$newLink  = str_replace('httpX//','http://',$newLink);
return $newLink;
}

function linkCheck($data){
$conn_id = ftp_connect('mdws2.mdwestserve.com');
// login with username and password
$login_result = ftp_login($conn_id, '', '');

$data = cleanPath($data);
$ch = curl_init($data);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_NOBODY, 1);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
// Check if any error occured
if(curl_errno($ch))
{
	$file = buildFile($data);

	$src_dir = buildPath(str_replace('/'.$file,'',$data));
	$dst_dir = buildPath2($src_dir);
	
	$parts = explode('/',$dst_dir);
	$i=0;
	$counter=count($parts);
	while($i<$counter){
	$newFolder = $parts[$i];
	$test .= "<li>Current directory: " . ftp_pwd($conn_id) . "</li>";
	if (ftp_chdir($conn_id, "$newFolder")) {
		$test .= "<li>Current directory is now: " . ftp_pwd($conn_id) . "</li>";
	} else { 
		$test .= "<li>Couldn't change directory to ".$parts[$i].", attempting to create</li>";
		
		if (ftp_mkdir($conn_id, "$newFolder")) {
			$test .= "<li>Directory created: " . $parts[$i] . "</li>";
		} else { 
			$test .= "<li>Couldn't Create directory ".$parts[$i]."</li>";
		}
		
		if (ftp_chdir($conn_id, "$newFolder")) {
			$test .= "<li>Current directory is now: " . ftp_pwd($conn_id) . "</li>";
		} else { 
			$test .= "<li>Couldn't change directory to ".$parts[$i].", final failure.</li>";
		}
		
		
	}
		$i++;
	}

	
	//ftp_mkdir($conn_id, $dst_dir); // create directories that do not yet exist
  $linux = str_replace('_',':',$src_dir."/".$file);
if (ftp_put($conn_id, $file, $linux, FTP_ASCII)) {
 $test .= "<li>successfully uploaded $file</li>";
} else {
$test .=  "<li>There was a problem while uploading $file</li>";
}
	
	
	
	

	
	    return  '<li><span style="color:#c92222">File Not Found on MDWS2. ('.curl_errno($ch).') ('.$data.')</span></li><li>File: '.$file.'</li><li>Source: '.$src_dir.'</li><li>Destination: '.$dst_dir.'</li>'.$test;

	
	
	
	
} else {
    return '<span style="color:#22c922">Found on MDWS2! ('.$data.')</span>';
	// update ps_packets.dataLocation
}

// Close handle
curl_close($ch);
ftp_close($conn_id);

}





function convert($link,$field,$packet){
	$newVal = str_replace('//mdwestserve','//mdws2.mdwestserve',$link);
	$pos = strpos($newVal, 'mdws2');
	if ($pos !== false) {
		return cleanPath($newVal);
	} else {
		return "Error mdws2 convert failed. Link: $link";
	}
}

mysql_connect();
mysql_select_db('service');
// this will be the backup routine for our first true archive, 2008 to begin.
$q="SELECT * from ps_packets where date_received LIKE '2008-%' order by packet_id DESC limit 0,10";
// we will focus on three areas for OTD's
$r=@mysql_query($q);
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
echo "<tr><td>Packet $d[packet_id]</td>";

//
// Archive Old Orders
//
$newOTD = convert($d[otd],'otd',$d[packet_id]);
$testOTD = linkCheck($newOTD);
echo "</td><td><li>".$testOTD."</li>";





//
// Archive Old Photographs ps_photos
//
$r2=@mysql_query("select * from ps_photos where packetID = '$d[packet_id]'");
while($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){

//echo "<li>$d2[localPath]</li>";

$newLink = convert($d2[localPath],'ps_photos',$d[packet_id]);
$testLink = linkCheck($newLink);
echo "<li>".$testLink."</li>";


}


//
// Archive Old Uploads ps_affidavits
//

$r2=@mysql_query("select * from ps_affidavits where packetID = '$d[packet_id]'");
while($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){

//echo "<li>$d2[affidavit]</li>";
$newLink = convert($d2[affidavit],'ps_affidavit',$d[packet_id]);
$testLink = linkCheck($newLink);
echo "<li>".$testLink."</li>";


}

echo "</td></tr>";
}

?></table>

<?
// close the connection
?>