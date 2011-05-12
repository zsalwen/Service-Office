<? include 'common.php';
logAction($_COOKIE[psdata][user_id], $_SERVER['PHP_SELF'], 'Viewing Personal Profile');

function stateList(){
	$list .= "<option>AL</option>";
	$list .= "<option>AK</option>";
	$list .= "<option>AR</option>";
	$list .= "<option>AS</option>";
	$list .= "<option>AZ</option>";
	$list .= "<option>CA</option>";
	$list .= "<option>CO</option>";
	$list .= "<option>CT</option>";
	$list .= "<option>DC</option>";
	$list .= "<option>DE</option>";
	$list .= "<option>FL</option>";
	$list .= "<option>GA</option>";
	$list .= "<option>HI</option>";
	$list .= "<option>IA</option>";
	$list .= "<option>ID</option>";
	$list .= "<option>IL</option>";
	$list .= "<option>KS</option>";
	$list .= "<option>KY</option>";
	$list .= "<option>LA</option>";
	$list .= "<option>MA</option>";
	$list .= "<option>MD</option>";
	$list .= "<option>ME</option>";
	$list .= "<option>MI</option>";
	$list .= "<option>MN</option>";
	$list .= "<option>MO</option>";
	$list .= "<option>MS</option>";
	$list .= "<option>MT</option>";
	$list .= "<option>NC</option>";
	$list .= "<option>ND</option>";
	$list .= "<option>NE</option>";
	$list .= "<option>NH</option>";
	$list .= "<option>NJ</option>";
	$list .= "<option>NM</option>";
	$list .= "<option>NV</option>";
	$list .= "<option>NY</option>";
	$list .= "<option>OH</option>";
	$list .= "<option>OK</option>";
	$list .= "<option>OR</option>";
	$list .= "<option>PA</option>";
	$list .= "<option>RI</option>";
	$list .= "<option>SC</option>";
	$list .= "<option>SD</option>";
	$list .= "<option>TN</option>";
	$list .= "<option>TX</option>";
	$list .= "<option>UT</option>";
	$list .= "<option>VA</option>";
	$list .= "<option>VT</option>";
	$list .= "<option>WA</option>";
	$list .= "<option>WI</option>";
	$list .= "<option>WV</option>";
	$list .= "<option>WY</option>";
	return $list;
}

function row_color2($str,$bg1,$bg2){
	$i=0;
	$explode=explode("bgcolor='[color]",$str);
	$count=count($explode)-1;
	$return=$explode[0];
	while ($i < $count){$i++;
		if ( $i%2 ) {
			$return .= "bgcolor='$bg1".$explode[$i];
		} else {
			 $return .= "bgcolor='$bg2".$explode[$i];
		}
	}
	return $return;
}

function justZip($zip){
	if (strpos($zip,'-') !== false){
		$zip=explode('-',$zip);
		$zip=$zip[0];
	}
	return $zip;
}

if ($_GET[delete]){
	@mysql_query("UPDATE ps_users SET level='DELETED' where id = '$_GET[delete]'");
	if ($_COOKIE[psdata][level] != "Operations"){
		header('Location: login.php?message=Your account has been removed.');
	}else{
		$event = 'account deleted';
		$email = $_COOKIE[psdata][email];
		$q1="INSERT into ps_security (event, email, entry_time) VALUES ('$event', '$email', NOW())";
		//@mysql_query($q1) or die(mysql_error());
		header('Location: http://staff.mdwestserve.com');
	}
}
$id = $_GET[admin];

if ($_POST[submit]){
	$img = str_replace('www.','',$_POST[img]);
	$img = str_replace('http://','',$img);
	$img = str_replace('https://','',$img);
	setcookie ("psdata[effects]", $_POST[effects]);

	$q1 = "UPDATE ps_users SET 
								p_update=NOW(),
								company='$_POST[company]',
								contract='$_POST[contract]',
								envPrint='$_POST[envPrint]',
								manager_review='$_POST[manager_review]',
								name='$_POST[name]',
								email='$_POST[email]',
								address='$_POST[address]',
								city='$_POST[city]',
								state='$_POST[state]',
								zip='$_POST[zip]',
								address2='$_POST[address2]',
								city2='$_POST[city2]',
								state2='$_POST[state2]',
								zip2='$_POST[zip2]',
								phone='$_POST[phone]'
							WHERE id = '$id'";

	@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
	echo "<center><h1>PROFILE UPDATED</h1></center>";
}

function isChecked($value){
	if ($value > '0'){
	return 'checked';
	}
}

function contractStatus($str){
	if (strtoupper($str) == 'YES'){
		return " style='color:green;font-weight:bold;'><option value='YES'>ACTIVE</option>";
	}else{
		return " style='color:red;font-weight:bold;'><option value='NO'>INACTIVE</option>";
	}
}

function printStatus($str){
	if (strtoupper($str) == 'YES'){
		return " style='color:green;font-weight:bold;'><option value='YES'>ALLOWED</option>";
	}else{
		return " style='color:red;font-weight:bold;'><option value=''>RESTRICTED</option>";
	}
}

function getCounty($zip){
	//make sure zip is only 5 digits
	if (strpos($zip,'-') !== false){
		$zip=explode('-',$zip);
		$zip=$zip[0];
	}
	$q= "select county from zip_code where zip_code = '$zip' LIMIT 0,1";
	$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
	$d=mysql_fetch_array($r, MYSQL_ASSOC); 
	if ($d[county]){
		return strtoupper($d[county]);
	}else{
		//curl getzips.com for appropriate info
		$url="http://www.getzips.com/CGI-BIN/ziplook.exe?What=1&Zip=$zip&Submit=Look+It+Up";
		$html=getPage($url,"Zip Code Lookup",'5','');
		$explode=explode("<P><B>AREA</B></TD></TR>",$html);
		$explode=explode(" VALIGN=TOP><P>",$explode[1]);
		$return=explode("</TD>",$explode[3]);
		if(strtoupper($return[0]) == "PRINCE GEORGE'S"){
			return "PRINCE GEORGES";
		}else{
			return strtoupper($return[0]);
		}
	}
}

function makeAnchor($i,$key){
	if ( $i%3 === 0){
		return "<tr>
		<td>
		<a href='#$key'>Jump to $key</a></td>";
    }elseif ( $i%3 === 1) {
        return "<td>
		<a href='#$key'>Jump to $key</a></td>";
    } else {
        return "<td>
		<a href='#$key'>Jump to $key</a></td></tr>";
    }
}

$q= "select * from ps_users where id = '$id'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC); 
?>
<style>
a{color:FF0000;}
a:hover{color:888888;}
a:visited{color:6600AA;}
table, tr, td, fieldset, legend{padding:0px;}
</style>
<table align="center" width="100%"><tr><td valign="top">
        	<table border="1" align="center" style="border-collapse:collapse" width="100%">
            	<form method="post"><tr>
                	<td colspan="4" align="center" bgcolor="#CCCCCC"><a href="profile.php?id=<?=$d[id]?>">++ Independent Contractor Profile ++</a></td>
                </tr>
				<tr>
                	<td>Company Name</td>
                    <td colspan="3"><input name="company" size="60" value="<?=$d[company]?>" /></td>
                </tr>
				<tr>
                	<td>Server Name</td>
                    <td colspan="3"><input name="name" size="60" value="<?=$d[name]?>" /></td>
                </tr>
                 <tr>
                	<td>Address</td>
                	<td colspan="3"><input name="address" size="60" value="<?=$d[address]?>" /></td>
				</tr>
                <tr>
                	<td>City, State ZIP </td>
                	<td colspan="3"><input name="city" size="40" value="<?=$d[city]?>" /><select name="state"><option><?=$d[state]?></option><?=stateList()?></select><input name="zip" size="5" value="<?=$d[zip]?>" /></td>
				</tr>
				 <tr>
                	<td>Mailing Address</td>
                	<td colspan="3"><input name="address2" size="60" value="<?=$d[address2]?>" /></td>
				</tr>
                <tr>
                	<td>City, State ZIP </td>
                	<td colspan="3"><input name="city2" size="40" value="<?=$d[city2]?>" /><select name="state2"><option><?=$d[state2]?></option><?=stateList()?></select><input name="zip2" size="5" value="<?=$d[zip2]?>" /></td>
				</tr>
                <tr>
                	<td>Phone</td>                
                	<td colspan="3"><input name="phone" size="60" value="<?=$d[phone]?>" /></td>
				</tr>
                <tr bgcolor="<? if ($d[email_status] != "VERIFIED"){ echo "FF0000"; }else{ echo "00FF00"; }?>">
                	<td>E-Mail</td>
                    <td colspan="3"><input name="email" size="60" value="<?=$d[email]?>" /></td>
                </tr>
				<tr>
					<td>Acct. Status</td>
					<td colspan="3"><select name="contract"<?=contractStatus($d[contract])?><option value='YES'>ACTIVE</option><option value='NO'>INACTIVE</option></select></td>
				</tr>
				<tr>
					<td>Envelope Printing</td>
					<td colspan="3"><select name="envPrint"<?=printStatus($d[envPrint])?><option value='YES'>ALLOWED</option><option value='NO'>RESTRICTED</option></select></td>
				</tr>
    	<tr>
    	<td valign="top">Notes</td>
        <td colspan="3" valign="top"><textarea name="manager_review" cols="45" rows="5"><?=stripslashes($d[manager_review])?></textarea></td>
    </tr>
         <tr>
                	<td colspan="4" align="center"><input  type="submit" name="submit" value="Update <?=$d[level]?>" /></form><? if ($_COOKIE[psdata][level] == "Operations"){?>
  <a href="?delete=<?=$id;?>">Flag account for deletion.</a> <form action="http://service.mdwestserve.com/liveAffidavit.php" target="_blank" style='display:inline;'><input type="hidden" name="start" value="0"><input type="hidden" name="stop" value="200000"><input type="hidden" name="server" value="<?=$id?>"><input type="hidden" name="user_id" value="<?=$id?>"><input type="hidden" name="level" value="GOLD"><input type="hidden" name="ev" value="YES"><input type="submit" name="submit" value="Print Presale & Eviction Affidavits"></form>
<? } ?></td>
                </tr>      
            
			<tr><td bgcolor="#FFCC33" colspan="4"><?=$d[whois]?></td></tr></table>
</td><td valign="top" bgcolor="#CCFFFF" colspan="2"><table width="100%">
	<tr>
    	<td><strong>Previous Serves: Order <a href="contractor_profile.php?admin=<?=$_GET[admin]?>&serves=county">By County</a> OR <a href="contractor_profile.php?admin=<?=$_GET[admin]?>&serves=ordered">By Packet #</a></strong></td>
    </tr>
    <?
	if ($_GET[serves] == 'county'){
		$i2=0;
		$q2="SELECT packet_id, circuit_court, city1, state1, zip1, ps_pay.contractor_rate from ps_packets, ps_pay WHERE server_id='$_GET[admin]' AND address1 <> '' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
		$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
		$exclude=" AND server_id <> '$_GET[admin]'";
		while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){$i2++;
			$county=strtoupper($d2[circuit_court]);
			if ($county == "ST MARYS"){
				$county="SAINT MARYS";
			}elseif($county == "PRINCE GEORGE'S"){
				$county="PRINCE GEORGES";
			}
			$zip=justZip($d2[zip1]);
			$rate=$d2[contractor_rate];
			if (isset($countyList[$county][$zip][$rate])){
				$countyList[$county][$zip][$rate] = $countyList[$county][$zip][$rate]."<tr bgcolor='[color]'><td>
				<a href='/otd/order.php?packet=$d2[packet_id]' target='_blank'>(OTD$d2[packet_id])</a>
				</td><td>".strtoupper($d2[city1]).", ".strtoupper($d2[state1])."</td><td></td></tr>";
			}else{
				$countyList[$county][$zip][$rate] = "<tr bgcolor='[color]'><td>
				<a href='/otd/order.php?packet=$d2[packet_id]' target='_blank'>(OTD$d2[packet_id])</a>
				</td><td>".strtoupper($d2[city1]).", ".strtoupper($d2[state1])."</td><td>$".$rate."</td></tr>";
			}
		}
		foreach(range('a','e') as $letter){
			$q2="SELECT packet_id, city1$letter, state1$letter, zip1$letter, ps_pay.contractor_rate$letter from ps_packets, ps_pay WHERE server_id$letter='$_GET[admin]' AND address1$letter <> ''$exclude AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
			$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
			while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){$i2++;
				$county=getCounty($d2["zip1$letter"]);
				$zip=justZip($d2["zip1$letter"]);
				$rate=$d2["contractor_rate$letter"];
				if (isset($countyList[$county][$zip][$rate])){
					$countyList[$county][$zip][$rate] = $countyList[$county][$zip][$rate]."<tr bgcolor='[color]'><td>
					<a href='/otd/order.php?packet=$d2[packet_id]' target='_blank'>(OTD$d2[packet_id])</a>
					</td><td>".strtoupper($d2["city1$letter"]).", ".strtoupper($d2["state1$letter"])."</td><td></td></tr>";
				}else{
					$countyList[$county][$zip][$rate] = "<tr bgcolor='[color]'><td>
					<a href='/otd/order.php?packet=$d2[packet_id]' target='_blank'>(OTD$d2[packet_id])</a>
					</td><td>".strtoupper($d2["city1$letter"]).", ".strtoupper($d2["state1$letter"])."</td><td>$".$rate."</td></tr>";
				}
			}
			$exclude .= " AND server_id$letter <> '$_GET[admin]'";
		}
		//evictions
		$q2="SELECT eviction_id, circuit_court, city1, state1, zip1, ps_pay.contractor_rate from evictionPackets, ps_pay WHERE server_id='$_GET[admin]' AND address1 <> '' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' ORDER BY eviction_id DESC";
		$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
		$exclude=" AND server_id <> '$_GET[admin]'";
		while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){$i2++;
			$county=strtoupper($d2[circuit_court]);
			$zip=justZip($d2[zip1]);
			$rate=$d2[contractor_rate];
			if ($county == "ST MARYS"){
				$county="SAINT MARYS";
			}
			if (isset($countyList[$county][$zip][$rate])){
				$countyList[$county][$zip][$rate] = $countyList[$county][$zip][$rate]."<tr bgcolor='[color]'><td>
				<a href='/ev/order.php?packet=$d2[eviction_id]' target='_blank'>(EV$d2[eviction_id])</a>
				</td><td>".strtoupper($d2[city1]).", ".strtoupper($d2[state1])."</td><td></td></tr>";
			}else{
				$countyList[$county][$zip][$rate] = "<tr bgcolor='[color]'><td>
				<a href='/ev/order.php?packet=$d2[eviction_id]' target='_blank'>(EV$d2[eviction_id])</a>
				</td><td>".strtoupper($d2[city1]).", ".strtoupper($d2[state1])."</td><td>$".$rate."</td></tr>";
			}
		}
		//standards
		$q2="SELECT packet_id, circuit_court, city1, state1, zip1, ps_pay.contractor_rate from standard_packets, ps_pay WHERE server_id='$_GET[admin]' AND address1 <> '' AND standard_packets.packet_id=ps_pay.packetID AND ps_pay.product='S' ORDER BY packet_id DESC";
		$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
		$exclude=" AND server_id <> '$_GET[admin]'";
		while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){$i2++;
			$county=getCounty($d2["zip1"]);
			if ($county == "ST MARYS"){
				$county="SAINT MARYS";
			}
			$zip=justZip($d2[zip1]);
			$rate=$d2[contractor_rate];
			if (isset($countyList[$county][$zip][$rate])){
				$countyList[$county][$zip][$rate] = $countyList[$county][$zip][$rate]."<tr bgcolor='[color]'><td>
				<a href='/standard/order.php?packet=$d2[packet_id]' target='_blank'>(S$d2[packet_id])</a>
				</td><td>".strtoupper($d2[city1]).", ".strtoupper($d2[state1])."</td><td></td></tr>";
			}else{
				$countyList[$county][$zip][$rate] = "<tr bgcolor='[color]'><td>
				<a href='/standard/order.php?packet=$d2[packet_id]' target='_blank'>(S$d2[packet_id])</a>
				</td><td>".strtoupper($d2[city1]).", ".strtoupper($d2[state1])."</td><td>$".$rate."</td></tr>";
			}
		}
		foreach(range('a','e') as $letter){
			$q2="SELECT packet_id, city1$letter, state1$letter, zip1$letter, ps_pay.contractor_rate$letter from standard_packets, ps_pay WHERE server_id$letter='$_GET[admin]' AND address1$letter <> ''$exclude AND standard_packets.packet_id=ps_pay.packetID AND ps_pay.product='S' ORDER BY packet_id DESC";
			$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
			while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){$i2++;
				$county=getCounty($d2["zip1$letter"]);
				$zip=justZip($d2["zip1$letter"]);
				$rate=$d2["contractor_rate$letter"];
				if (isset($countyList[$county][$zip][$rate])){
					$countyList[$county][$zip][$rate] = $countyList[$county][$zip][$rate]."<tr bgcolor='[color]'><td>
					<a href='/standard/order.php?packet=$d2[packet_id]' target='_blank'>(S$d2[packet_id])</a>
					</td><td>".strtoupper($d2["city1$letter"]).", ".strtoupper($d2["state1$letter"])."</td><td></td></tr>";
				}else{
					$countyList[$county][$zip][$rate] = "<tr bgcolor='[color]'><td>
					<a href='/standard/order.php?packet=$d2[packet_id]' target='_blank'>(S$d2[packet_id])</a>
					</td><td>".strtoupper($d2["city1$letter"]).", ".strtoupper($d2["state1$letter"])."</td><td>$".$rate."</td></tr>";
				}
			}
			$exclude .= " AND server_id$letter <> '$_GET[admin]'";
		}
		if (isset($countyList)){
			ksort($countyList);
			$i=-1;
			$count=count($countyList)-1;
			echo "<tr>
			<td>
			<table>";
			foreach($countyList as $k1 => $v1){$i++;
				//county
				echo "
				".makeAnchor($i,$k1);
				if($i == $count && (($i%3 === 1) || ($i%3 === 0))){
					echo "</tr>";
				}
				$count2=count($v1);
				$list .= "</table><table align='center'><tr><td>
				<table align='left'><tr><td colspan='$count2'>
				<div style='background-color:FF9900; font-size:22px; padding-left:20px; font-weight:bold; font-variant:small-caps;' id='$k1'>$k1</div>
				</td></tr><tr><td>";
				ksort($v1);
				foreach($v1 as $k2 => $v2){
					//zip
					krsort($v2);
					$count3=count($v2);
					$list .= "
					<td valign='top'>
					<table align='center' border='1'>
					<tr bgcolor='#FFFF00'>
					<td align='center' colspan='$count3' style='font-weight:bold;'>$k2</td>
					</tr>
					<tr bgcolor='#FF0000'>
					";
					foreach($v2 as $k3 => $v3){
						//rate
						//krsort($v3);
						$list .= "<td valign='top' style='padding-left:0px; padding-right:0px;' align='center'>
						<table style='border: 1px solid black; border-collapse:collapse;' border='1' align='center'>
						".row_color2($v3,"#FFFFFF","#CCCCCC")."
						</table>
						</td>";
					}
					$list .= "
					</tr>
					</table>
					</td>";
				}
				"</tr></table>
				</td></tr>";
			}
			$list .= "</table></td></tr>";
		}
		echo $list;
	}else{
		$i=-1;
		$q2="SELECT DISTINCT packet_id, city1, state1, zip1, ps_pay.contractor_rate from ps_packets, ps_pay WHERE server_id='$_GET[admin]' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
		$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
		$exclude=" AND server_id <> '$_GET[admin]'";
		while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
			$packet=$d2[packet_id];
			$list[$packet] = "<tr><td><a href='/otd/order.php?packet=$d2[packet_id]' target='_blank'>(OTD$d2[packet_id])</a> ".strtoupper($d2[city1]).", ".strtoupper($d2[state1]).", $d2[zip1] - <b>$$d2[contractor_rate]</b></td></tr>";
		}
		foreach(range('a','e') as $letter){
			$q2="SELECT DISTINCT packet_id, city1$letter, state1$letter, zip1$letter, ps_pay.contractor_rate$letter from ps_packets, ps_pay WHERE server_id$letter='$_GET[admin]'$exclude AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD' ORDER BY packet_id DESC";
			$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
			while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
				$packet=$d2[packet_id];
				$list[$packet] .= "<tr><td><a href='/otd/order.php?packet=$d2[packet_id]' target='_blank'>(OTD$d2[packet_id])</a> ".strtoupper($d2["city1$letter"]).", ".strtoupper($d2["state1$letter"]).", ".$d2["zip1$letter"]." - <b>$".$d2["contractor_rate$letter"]."</b></td></tr>";
			}
			$exclude .= " AND server_id$letter <> '$_GET[admin]'";
		}
		if (isset($list)){
			krsort($list);
			$bigList .= "<tr><td align='center' id='otd' style='font-weight:bold; border:1px solid black;'>PRESALE</td></tr>";
			foreach($list as $value){
				$bigList .= $value;
			}
		}
		//evictions
		$q2="SELECT DISTINCT eviction_id, city1, state1, zip1, ps_pay.contractor_rate from evictionPackets, ps_pay WHERE server_id='$_GET[admin]' AND evictionPackets.eviction_id=ps_pay.packetID AND ps_pay.product='EV' ORDER BY eviction_id DESC";
		$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
		$exclude=" AND server_id <> '$_GET[admin]'";
		while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
			$packet=$d2[eviction_id];
			$list2[$packet] = "<tr><td><a href='/ev/order.php?packet=$d2[eviction_id]' target='_blank'>(EV$d2[eviction_id])</a> ".strtoupper($d2[city1]).", ".strtoupper($d2[state1]).", $d2[zip1] - <b>$$d2[contractor_rate]</b></td></tr>";
		}
		if (isset($list2)){
			krsort($list2);
			$bigList .= "<tr><td align='center' id='ev' style='font-weight:bold; border:1px solid black;'>EVICTIONS</td></tr>";
			foreach($list2 as $value){
				$bigList .= $value;
			}
		}
		
		//standards
		$q2="SELECT DISTINCT packet_id, city1, state1, zip1, ps_pay.contractor_rate from standard_packets, ps_pay WHERE server_id='$_GET[admin]' AND standard_packets.packet_id=ps_pay.packetID AND ps_pay.product='S' ORDER BY packet_id DESC";
		$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
		$exclude=" AND server_id <> '$_GET[admin]'";
		while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
			$packet=$d2[packet_id];
			$list3[$packet] = "<tr><td><a href='/standard/order.php?packet=$d2[packet_id]' target='_blank'>(S$d2[packet_id])</a> ".strtoupper($d2[city1]).", ".strtoupper($d2[state1]).", $d2[zip1] - <b>$$d2[contractor_rate]</b></td></tr>";
		}
		foreach(range('a','e') as $letter){
			$q2="SELECT DISTINCT packet_id, city1$letter, state1$letter, zip1$letter, ps_pay.contractor_rate$letter from standard_packets, ps_pay WHERE server_id$letter='$_GET[admin]'$exclude AND standard_packets.packet_id=ps_pay.packetID AND ps_pay.product='S' ORDER BY packet_id DESC";
			$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
			while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){
				$packet=$d2[packet_id];
				$list3[$packet] .= "<tr><td><a href='/standard/order.php?packet=$d2[packet_id]' target='_blank'>(S$d2[packet_id])</a> ".strtoupper($d2["city1$letter"]).", ".strtoupper($d2["state1$letter"]).", ".$d2["zip1$letter"]." - <b>$".$d2["contractor_rate$letter"]."</b></td></tr>";
			}
			$exclude .= " AND server_id$letter <> '$_GET[admin]'";
		}
		if (isset($list3)){
			krsort($list3);
			$bigList .= "<tr><td align='center' id='s' style='font-weight:bold; border:1px solid black;'>STANDARD PACKETS</td></tr>";
			foreach($list3 as $value){
				$bigList .= $value;
			}
		}
		if (isset($bigList)){
			if ($list2 && $list3){
				echo "<tr><td><strong><a href='#ev'>Jump to Evictions</a> | <a href='#s'>Jump to Standard Packets</a></strong></td></tr>";
			}elseif($list3){
				echo "<tr><td><strong><a href='#s'>Jump to Standard Packets</a></strong></td></tr>";
			}elseif($list2){
				echo "<tr><td><strong><a href='#ev'>Jump to Evictions</a></strong></td></tr>";
			}
			echo $bigList;
		}
	}
	?>
</table></td></tr></table>
<? include 'footer.php';?>