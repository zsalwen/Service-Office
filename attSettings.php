 <script>document.title = 'Company Intranet - Attorney Settings';</script>
 <style>
 td { font-size: 10px; }
 </style>
<?
mysql_connect();
mysql_select_db('core');
include 'common.php';
if ($_POST[att_id]){
	$att_id=$_POST[att_id];
}elseif($_GET[att_id]){
	$att_id=$_GET[att_id];
}
if ($_POST[submit] == "Save Settings"){

echo "<center>SAVED, <a href='http://staff.mdwestserve.com/attSettings.php'>Select Another Attorney</a></center>";
$q = "UPDATE attorneys SET
						trust_names='$_POST[trust_names]', 
						address='$_POST[address]', 
						invoice_to='$_POST[invoice_to]', 
						statement_to='$_POST[statement_to]', 
						ps_to='$_POST[ps_to]',
						ps_to_alt='$_POST[ps_to_alt]',
						ps_alt2='$_POST[ps_alt2]',
						ps_plaintiff='$_POST[ps_plaintiff]',
						payInstructions='".addslashes($_POST[payInstructions])."',
						upcoming_report_to='$_POST[upcoming_report_to]' 
							WHERE attorneys_id = '$att_id'";
$r = @mysql_query($q) or die(mysql_error());
}
 if ($_POST[att_id] || $_GET[att_id]){
 
$q = "SELECT * FROM attorneys WHERE attorneys_id = '$att_id'";
$r = @mysql_query($q) or die(mysql_error());
$d = mysql_fetch_array($r, MYSQL_ASSOC);
?>
<form method="post">
<input type="hidden" name="page" value="email_config"/>
<input type="hidden" name="att_id" value="<?=$att_id?>"/>
<h1 align="center">Attorney ID #<?=$d[attorneys_id]?></h1>
<table align="center">
	<tr>
    	<td>Display Name</td>
        <td><input disabled="disabled" name="display_name" size="100" value="<?=$d[display_name]?>"></td>
    </tr>
	<tr>
    	<td>Constant Trustee's</td>
        <td><input name="trust_names" size="100" value="<?=$d[trust_names]?>"> Seperate with Comma <em>(ALL CAPS)</em></td>
    </tr>
	<tr>
    	<td>Process Server Plaintiff</td>
        <td><input name="ps_plaintiff" size="100" value="<?=$d[ps_plaintiff]?>"> Seperate with Hyphen <em>(ALL CAPS)</em></td>
    </tr>
	<tr>
    	<td>Address</td>
        <td><input name="address" size="100" value="<?=$d[address]?>"> Line Break with Hyphen <em>(3 Line Max)</em></td>
    </tr>
	<tr>
    	<td colspan="2" align="center"><strong style='font-size: 16px;'>E-Mail Contacts</strong></td>
    </tr>
	<tr>
    	<td>Send Invoice To</td>
        <td><input name="invoice_to" size="100" value="<?=$d[invoice_to]?>"> Seperate with Comma</td>
    </tr>
	<tr>
    	<td>Send FILING COMPLETE Updates To</td>
        <td><input name="ps_to" size="100" value="<?=$d[ps_to]?>"> Seperate with Comma</td>
    </tr>
	<tr>
    	<td>Send SERVICE CONFIRMED Updates To</td>
        <td><input name="ps_to_alt" size="100" value="<?=$d[ps_to_alt]?>"> Seperate with Comma</td>
    </tr>
	<tr>
    	<td>Send Defaulting Purchaser To</td>
        <td><input name="ps_alt2" size="100" value="<?=$d[ps_alt2]?>"> Seperate with Comma</td>
    </tr>
	<tr>
    	<td>Send Statement To</td>
        <td><input name="statement_to" size="100" value="<?=$d[statement_to]?>"> Seperate with Comma</td>
    </tr>
	<tr>
		<td>Payment Instructions<br>(Displays On Payment Entry Page)</td>
		<td><textarea name="payInstructions" rows="2" cols="75"><?=stripslashes($d[payInstructions])?></textarea></td>
	</tr>
	<tr>
        <td colspan="2" align="center"><input type="submit" name="submit" value="Save Settings"></td>
    </tr>
</table>
</form>
<? }else{?>
<center>
<form method="post"><input type="hidden" name="page" value="email_config"/><select name="att_id">
		<?
		$q8 = "SELECT * FROM attorneys where attorneys_id >'0' ORDER BY display_name ASC";		
		$r8 = @mysql_query ($q8) or die(mysql_error());
		while ($data8 = mysql_fetch_array($r8, MYSQL_ASSOC)){ 
	echo "<option value='$data8[attorneys_id]'>$data8[display_name]</option>";
		}
		?>
</select><input type="submit" value="Load Settings"></form></center>
<? }?>
