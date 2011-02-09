<?
//this page generates an email 
mysql_connect();
mysql_select_db('service');
include 'common.php';
if ($_GET[date]){
	$today=$_GET[date];
	$today2=explode('-',$today);
	$today2=$today2[1]."/".$today2[2]."/".$today2[0];
}else{
	$today=date('Y-m-d');
	$today2=date('m/d/Y');
}
$qs="SELECT id from ps_users ORDER BY id DESC limit 0,1";
$rs=@mysql_query($qs) or die("Query: $qs<br>".mysql_error());
$ds=mysql_fetch_array($rs,MYSQL_ASSOC);
$count=$ds[id];
$q="SELECT id FROM ps_packages WHERE assign_date LIKE '$today%'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	$q1="SELECT packet_id, server_id, server_ida, server_idb, server_idc, server_idd, server_ide FROM ps_packets WHERE package_id='".$d[id]."' ORDER BY 'packet_id' ASC";
	$r1=@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
	while($d1=mysql_fetch_array($r1,MYSQL_ASSOC)){
		$server_id=$d1[server_id];
		if (strpos($emailList["$server_id"],"|$d1[packet_id]|")){
			//$emailList["$server_id"] .= "|<b>SKIPPED</b> <i>$d1[packet_id]</i>|<br>";
		}else{
			$emailList["$server_id"] .= "|$d1[packet_id]|<br>";
		}
		foreach(range('a','e') as $letter){
			if ($d1["server_id$letter"]){
				$server_id=$d1["server_id$letter"];
				if (strpos($emailList["$server_id"],"|$d1[packet_id]|")){
					//$emailList["$server_id"] .= "|<b>SKIPPED</b> <i>$d1[packet_id]</i>|<br>";
				}else{
					$emailList["$server_id"] .= "|$d1[packet_id]|<br>";
				}
			}
		}
	}
}
$q4="SELECT id FROM evictionPackages WHERE assign_date LIKE '$today%'";
$r4=@mysql_query($q4) or die("Query: $q4<br>".mysql_error());
while($d4=mysql_fetch_array($r4,MYSQL_ASSOC)){
	$q2="SELECT eviction_id, server_id FROM evictionPackets WHERE package_id='".$d4[id]."' ORDER BY 'eviction_id' ASC";
	$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
	while($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){
		$server_id=$d2[server_id];
		$emailList["$server_id"] .= "|EV$d2[eviction_id]|<br>";
	}
}
$i=0;
while ($i < $count){$i++;
	if ($emailList["$i"] != ''){
		$q3="SELECT * from ps_users WHERE id='$i'";
		$r3=@mysql_query($q3) or die("Query: $q3<br>".mysql_error());
		$d3=mysql_fetch_array($r3,MYSQL_ASSOC);
		if ($d3[email] != ''){
			if ($d3[company] != ''){
				$company=$d3[company]." - ";
			}else{
				$company='';
			}
			$subject="Files Dispatched To ".$company.$d3[name]." on $today2";
			$to = "MDWestServe Archive <service@mdwestserve.com>";
			$headers  = "MIME-Version: 1.0 \n";
			$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
			$headers .= "From: MDWestServe, Inc. <service@mdwestserve.com> \n";
			$headers .= "Cc: ".$d3[email]."\n";
			$body=$subject."<br>".str_replace("|","",$emailList["$i"])."<br><b>Please understand that this email is sent as confirmation of all process service files sent from our office today.  If you do not reply to the contrary--stating files have not been received--within 24 hours, you will be held responsible for any delays not made known to our office.</b><br>MDWestServe<br>service@mdwestserve.com<br>(410) 828-4568<br>".time()."<br>".md5(time());
			mail($to,$subject,$body,$headers);
			//echo "<div style='border:1px solid red;'><b>".id2name($i).":</b> Dispatched $today2<br>".str_replace("|","",$emailList["$i"])."</div>";
		}
	}
}
?>