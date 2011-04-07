<? 
include 'common.php';
if ($_COOKIE[psdata][level] != "Dispatch"){
	if ($_COOKIE[psdata][level] == "SysOp" || $_COOKIE[psdata][level] == "Administrator" || $_COOKIE[psdata][level] == "Operations"){
	
	}else{
	header('Location: http://staff.mdwestserve.com');
		$event = 'contractor_review.php';
		$email = $_COOKIE[psdata][email];
		$q1="INSERT into ps_security (event, email, entry_time) VALUES ('$event', '$email', NOW())";
		//@mysql_query($q1) or die(mysql_error());
		header('Location: router.php');
		}
}
if ($_GET[delete]){
@mysql_query("UPDATE ps_users SET level='DELETED' where id = '$_GET[delete]'");
}

if ($_POST[submit]){

$review = addslashes($_POST[manager_review]);
$q1 = "UPDATE ps_users SET 
							contract='$_POST[contract]',
							oc='$_POST[oc]',
							w9='$_POST[w9]',
							verify='$_POST[verify]',
							needSignatory='$_POST[needSignatory]',
							manager_review='$review'
						WHERE id = '$_GET[admin]'";

@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
//$message = "<font color='#FFFFFF' size='+1'><b>+++ Review Saved +++</b></font>";
header('Location: serviceReport.php');
}

//include 'menu.php';



$q= "select * from ps_users where id = '$_GET[admin]'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC); 

?>

<style>
a {text-decoration:none;}
.pop { color: #0000FF; font-size:16px; cursor:pointer; }
</style>

<?=$message?>
<table width="100%"><tr><td valign="top">
        	<table border="1" width="100%" bgcolor="#CCFFFF">
      <form method="post">
            	<tr>
                	<td colspan="2" align="center" bgcolor="#FFFF99"><input  type="submit" name="submit" value="Save Member Review" /></td>
                </tr>
				<tr>
                	<td>Name</td>
                    <td><?=$d[name]?></td>
                </tr>
				<tr>
                	<td>Company</td>
                    <td><?=$d[company]?></td>
                </tr>
				<tr>
                	<td>County of Residence</td>
                    <td><?=$d[county]?></td>
                </tr>
                 <tr>
                	<td>Address</td>
                	<td><?=$d[address]?></td>
				</tr>
                <tr>
                	<td>City, State ZIP </td>
                	<td><?=$d[city]?>, <?=$d[state]?> <?=$d[zip]?></td>
				</tr>
                <tr>
                	<td>Phone</td>                
                	<td><?=$d[phone]?></td>
				</tr>
                <tr>
                	<td>Email Address, Status</td>
                    <td><?=$d[email]?> (<?=$d[email_status]?>)</td>
                </tr>
                <tr>
                	<td>Experience</td>
                    <td><?=$d[experence]?></td>
                </tr>
                <tr>
                	<td>Drivers / Staff</td>
                    <td><?=$d[drivers]?></td>
                </tr>
                <tr>
                    <td>User Notes</td>
                    <td><pre><?=stripslashes($d[user_notes])?></pre></td>
                </tr>
            </table>
            </td></tr></table>
<table bgcolor="#FFFFCC" width="100%" align="center" cellpadding="3" border="1" style="border-collapse:collapse; font-size:18px; font-variant:small-caps">
	<tr>
    	<td <? if($d[allegany]){ echo "bgcolor='00FFFF'"; }?>>allegany <?=$d[allegany]?>
        <td>anne_arundel <?=$d[anne_arundel]?> 
        <td>baltimore_city <?=$d[baltimore_city]?> 
        <td>baltimore_county <?=$d[baltimore_county]?> 
        <td>caroline <?=$d[caroline]?> 
        <td>carroll <?=$d[carroll]?> 
        <td>calvert <?=$d[calvert]?> 
        <td>charles <?=$d[charles]?> 
    </tr>
    <tr>
        <td>cecil <?=$d[cecil]?> 
        <td>dorchester <?=$d[dorchester]?> 
        <td>frederick <?=$d[frederick]?> 
        <td>garrett <?=$d[garrett]?> 
        <td>harford <?=$d[harford]?> 
        <td>howard <?=$d[howard]?> 
       <td>kent <?=$d[kent]?> 
        <td>montgomery <?=$d[montgomery]?> 
     </tr>
    <tr>
        <td>pg <?=$d[pg]?> 
        <td>queen_anne <?=$d[queen_anne]?> 
        <td>st_mary <?=$d[st_mary]?> 
        <td>somerset <?=$d[somerset]?> 
        <td>talbot <?=$d[talbot]?> 
        <td>washington <?=$d[washington]?> 
        <td>wicomico <?=$d[wicomico]?> 
        <td>worcester <?=$d[worcester]?> 
   </tr>     
</table>

<table><tr><td valign="top">
<table bgcolor="#CCFFFF">
	<tr>
    	<td>Account Level</td>
    	<td><select name="level">
        	<option><?=$d[level]?></option>
            <option>Platinum Member</option>
            <option>Gold Member</option>
            <option>Green Member</option>
            <option>Data Entry</option>
            <option>Manager</option>
            <option>Accounting</option>
         </select></td>
    </tr>
	<tr>
    	<td>Account Verified</td>
    	<td><select name="verify"><option><? if ($d[verify]){ echo $d[verify]; }else{ echo "NO";}?></option><option>YES</option><option>NO</option></select></td>
    </tr>
	<tr>
    	<td>Under Contract</td>
    	<td><select name="contract"><option><?=$d[contract];?></option><option>YES</option><option>NO</option></select></td>
    </tr>
	<tr>
    	<td>Recieved Original Contract</td>
    	<td><select name="oc"><option><?=$d[oc];?></option><option>YES</option><option>NO</option></select></td>
    </tr>
	<tr>
    	<td>Recieved W9</td>
    	<td><select name="w9"><option><?=$d[w9];?></option><option>YES</option><option>NO</option></select></td>
    </tr>
	<tr>
    	<td>Require Signatory</td>
    	<td><input type="checkbox" name="needSignatory" <? if ($d[needSignatory] == 'checked'){ echo "checked";} ?> value="checked"></td>
    </tr>
	<tr>
    	<td>Manager Notes</td>
        <td><textarea name="manager_review" cols="70" rows="5"><?=stripslashes($d[manager_review])?></textarea> </td>
    </tr>
    <tr>
    <td align="left"><a class="pop" onclick="window.open('ps_geocode2.php?id=<?=$_GET[admin]?>','edit2','width=200,height=100,toolbar=no,location=no')">Geocode</a></td>
    <td align="right"><a href="?delete=<?=$_GET[admin];?>">Flag Account for Deletion</a></td></tr>
</form></table>
</td><td valign="top" bgcolor="#FFCC33"><?=$d[whois]?></td><td valign="top" bgcolor="#CCFFFF">
<table width="100%">
	<tr>
    	<td><strong>Previous Serves:</strong></td>
    </tr>
    <?
	$q2="SELECT DISTINCT city1, state1, zip1, packet_id, ps_pay.contractor_rate from ps_packets, ps_pay WHERE server_id='$_GET[admin]' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a><?=strtoupper($d2[city1])?>, <?=strtoupper($d2[state1])?>, <?=$d2[zip1]?> - <b>$<?=$d2[contractor_rate]?></b></td>
    </tr>
	<? }
	$q2="SELECT DISTINCT city1a, state1a, zip1a, packet_id, ps_pay.contractor_ratea from ps_packets, ps_pay WHERE server_ida='$_GET[admin]' AND server_id <> '$_GET[admin]' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1a])?>, <?=strtoupper($d2[state1a])?>, <?=$d2[zip1a]?> - <b>$<?=$d2[contractor_ratea]?></b></td>
    </tr>
	<? } 
	$q2="SELECT DISTINCT city1b, state1b, zip1b, packet_id, ps_pay.contractor_rateb from ps_packets, ps_pay WHERE server_idb='$_GET[admin]' AND server_id <> '$_GET[admin]' AND server_ida <> '$_GET[admin]' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1b])?>, <?=strtoupper($d2[state1b])?>, <?=$d2[zip1b]?> - <b>$<?=$d2[contractor_rateb]?></b></td>
    </tr>
	<? }
	$q2="SELECT DISTINCT city1c, state1c, zip1c, packet_id, ps_pay.contractor_ratec from ps_packets, ps_pay WHERE server_idc='$_GET[admin]' AND server_id <> '$_GET[admin]' AND server_ida <> '$_GET[admin]' AND server_idb <> '$_GET[admin]' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1c])?>, <?=strtoupper($d2[state1c])?>, <?=$d2[zip1c]?> - <b>$<?=$d2[contractor_ratec]?></b></td>
    </tr>
	<? }
	$q2="SELECT DISTINCT city1d, state1d, zip1d, packet_id, ps_pay.contractor_rated from ps_packets, ps_pay WHERE server_idd='$_GET[admin]' AND server_id <> '$_GET[admin]' AND server_ida <> '$_GET[admin]' AND server_idb <> '$_GET[admin]' AND server_idc <> '$_GET[admin]' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1d])?>, <?=strtoupper($d2[state1d])?>, <?=$d2[zip1d]?> - <b>$<?=$d2[contractor_rated]?></b></td>
    </tr>
	<? }
	$q2="SELECT DISTINCT city1e, state1e, zip1e, packet_id, ps_pay.contractor_ratee from ps_packets, ps_pay WHERE server_ide='$_GET[admin]' AND server_id <> '$_GET[admin]' AND server_ida <> '$_GET[admin]' AND server_idb <> '$_GET[admin]' AND server_idc <> '$_GET[admin]' AND server_idd <> '$_GET[admin]' AND ps_packets.packet_id=ps_pay.packetID AND ps_pay.product='OTD'";
	$r2=@mysql_query($q2) or die ("Query: $q2<br>".mysql_error());
	while ($d2=mysql_fetch_array($r2, MYSQL_ASSOC)){?>
	<tr>
    	<td><a href="/otd/order.php?packet=<?=$d2[packet_id]?>" target="_blank">(<?=$d2[packet_id]?>)</a> <?=strtoupper($d2[city1e])?>, <?=strtoupper($d2[state1e])?>, <?=$d2[zip1e]?> - <b>$<?=$d2[contractor_ratee]?></b></td>
    </tr>
	<? } ?>
</table></td></tr></table>
<script>window.onLoad=hideshow(document.getElementById('disp'));</script>
<?
include 'footer.php';
?>