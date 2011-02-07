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

if ($_GET[delete]){
	@mysql_query("UPDATE ps_users SET level='DELETED' where id = '$_GET[delete]'");
	if ($_COOKIE[psdata][level] != "Operations"){
		header('Location: login.php?message=Your account has been removed.');
	}else{
		$event = 'account deleted';
		$email = $_COOKIE[psdata][email];
		$q1="INSERT into ps_security (event, email, entry_time) VALUES ('$event', '$email', NOW())";
		//@mysql_query($q1) or die(mysql_error());
		header('Location: home.php');
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
		return "<option style='color:green;font-weight:bold;'>ACTIVE</option>";
	}elseif(strtoupper($str) == 'NO'){
		return "<option style='color:red;font-weight:bold;'>INACTIVE</option>";
	}
}

$q= "select * from ps_users where id = '$id'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC); 
?>
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
					<td>Status</td>
					<td colspan="3"><select name="contract"><?=contractStatus($d[contract])?><option value='YES'>ACTIVE</option><option value='NO'>INACTIVE</option></select></td>
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
    	<td><strong>Previous Serves:</strong></td>
    </tr>
    <?
	$q2="SELECT DISTINCT packet_id, city1, state1, zip1, contractor_rate from ps_packets WHERE server_id='$_GET[admin]'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a><?=strtoupper($d2[city1])?>, <?=strtoupper($d2[state1])?>, <?=$d2[zip1]?> - <b>$<?=$d2[contractor_rate]?></b></td>
    </tr>
	<? }
	$q2="SELECT DISTINCT packet_id, city1a, state1a, zip1a, contractor_ratea from ps_packets WHERE server_ida='$_GET[admin]' AND server_id <> '$_GET[admin]'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1a])?>, <?=strtoupper($d2[state1a])?>, <?=$d2[zip1a]?> - <b>$<?=$d2[contractor_ratea]?></b></td>
    </tr>
	<? } 
	$q2="SELECT DISTINCT packet_id, city1b, state1b, zip1b, contractor_rateb from ps_packets WHERE server_idb='$_GET[admin]' AND server_id <> '$_GET[admin]' AND server_ida <> '$_GET[admin]'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1b])?>, <?=strtoupper($d2[state1b])?>, <?=$d2[zip1b]?> - <b>$<?=$d2[contractor_rateb]?></b></td>
    </tr>
	<? }
	$q2="SELECT DISTINCT packet_id, city1c, state1c, zip1c, contractor_ratec from ps_packets WHERE server_idc='$_GET[admin]' AND server_id <> '$_GET[admin]' AND server_ida <> '$_GET[admin]' AND server_idb <> '$_GET[admin]'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1c])?>, <?=strtoupper($d2[state1c])?>, <?=$d2[zip1c]?> - <b>$<?=$d2[contractor_ratec]?></b></td>
    </tr>
	<? }
	$q2="SELECT DISTINCT packet_id, city1d, state1d, zip1d, contractor_rated from ps_packets WHERE server_idd='$_GET[admin]' AND server_id <> '$_GET[admin]' AND server_ida <> '$_GET[admin]' AND server_idb <> '$_GET[admin]' AND server_idc <> '$_GET[admin]'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1d])?>, <?=strtoupper($d2[state1d])?>, <?=$d2[zip1d]?> - <b>$<?=$d2[contractor_rated]?></b></td>
    </tr>
	<? }
	$q2="SELECT DISTINCT packet_id, city1e, state1e, zip1e, contractor_ratee from ps_packets WHERE server_ide='$_GET[admin]' AND server_id <> '$_GET[admin]' AND server_ida <> '$_GET[admin]' AND server_idb <> '$_GET[admin]' AND server_idc <> '$_GET[admin]' AND server_idd <> '$_GET[admin]'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1e])?>, <?=strtoupper($d2[state1e])?>, <?=$d2[zip1e]?> - <b>$<?=$d2[contractor_ratee]?></b></td>
    </tr>
	<? } ?>
</table></td></tr></table>
<? include 'footer.php';?>