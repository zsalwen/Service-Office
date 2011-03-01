 <script>document.title = 'Company Intranet - Courier Settings';</script>
 <style>
 td { font-size: 10px; }
 </style>
<?
mysql_connect();
mysql_select_db('core');
include 'common.php';
function isActive($str){
	if ($str != "checked"){
		return 0;
	}else{
		return $str;
	}
}
if ($_POST[courierID]){
	$courierID=$_POST[courierID];
}elseif($_GET[courierID]){
	$courierID=$_GET[courierID];
}
if ($_POST[submit] == "Save Settings"){
	echo "<center>SAVED, <a href='http://staff.mdwestserve.com/courierSettings.php'>Select Another Courier</a></center>";
	$q = "UPDATE courier SET
							name='$_POST[name]',
							phone='$_POST[phone]',
							email='$_POST[email]',
							password='$_POST[password]',
							notes='$_POST[notes]',
							isActive='".isActive($_POST[isActive])."'
								WHERE courierID = '$courierID'";
	$r = @mysql_query($q) or die(mysql_error());
}
if ($_POST[submit2] == "ENTER"){
	echo "<center>SAVED, <a href='http://staff.mdwestserve.com/courierSettings.php'>Select Another Courier</a></center>";
	$q = "INSERT INTO courier (name, phone, email, password, notes) VALUES ('$_POST[name]','$_POST[phone]','$_POST[email]','$_POST[password]','$_POST[notes]')";
	$r = @mysql_query($q) or die(mysql_error());
}
 if ($_POST[courierID] || $_GET[courierID]){
 
$q = "SELECT * FROM courier WHERE courierID = '$courierID'";
$r = @mysql_query($q) or die(mysql_error());
$d = mysql_fetch_array($r, MYSQL_ASSOC);
?>
<form method="post">
<input type="hidden" name="courierID" value="<?=$courierID?>"/>
<h1 align="center">Courier ID #<?=$d[courierID]?></h1>
<table align="center">
	<tr>
    	<td>Name</td>
        <td><input name="name" size="100" value="<?=$d[name]?>"></td>
    </tr>
	<tr>
    	<td>Phone</td>
        <td><input name="phone" size="100" value="<?=$d[phone]?>"></td>
    </tr>
	<tr>
    	<td>Email</td>
        <td><input name="email" size="100" value="<?=$d[email]?>"></td>
    </tr>
	<tr>
    	<td>Password</td>
        <td><input name="password" size="100" value="<?=$d[password]?>"></td>
    </tr>
	<tr>
		<td>Notes</td>
		<td><textarea name="notes" rows="2" cols="75"><?=stripslashes($d[notes])?></textarea></td>
	</tr>
	<tr>
		<td>Active</td>
		<td><input type='checkbox' value='checked' name='isActive' <? if ($d[isActive] == '1'){ echo "checked ";}?>></td>
	</tr>
	<tr>
        <td colspan="2" align="center"><input type="submit" name="submit" value="Save Settings"></td>
    </tr>
</table>
</form>
<? }elseif($_POST[newCourier]){ ?>
<form method="post">
<input type="hidden" name="page" value="email_config"/>
<h1 align="center">New Courier</h1>
<table align="center">
	<tr>
    	<td>Name</td>
        <td><input name="name" size="100"></td>
    </tr>
	<tr>
    	<td>Phone</td>
        <td><input name="name" size="100"></td>
    </tr>
	<tr>
    	<td>Email</td>
        <td><input name="email" size="100"></td>
    </tr>
	<tr>
    	<td>Password</td>
        <td><input name="password" size="100"></td>
    </tr>
	<tr>
		<td>Notes</td>
		<td><textarea name="notes" rows="2" cols="75"></textarea></td>
	</tr>
	<tr>
        <td colspan="2" align="center"><input type="submit" name="submit2" value="ENTER"></td>
    </tr>
</table>
</form>
<? }else{?>
<center>
<form method="post" style='display:inline;'><select name="courierID">
		<?
		$q8 = "SELECT * FROM courier ORDER BY name ASC";	
		$r8 = @mysql_query ($q8) or die(mysql_error());
		while ($data8 = mysql_fetch_array($r8, MYSQL_ASSOC)){ 
	echo "<option value='$data8[courierID]'>$data8[name]</option>";
		}
		?>
</select><br>
<input type="submit" value="Load Settings"></form><form method="post" style='display:inline;'> | <input type="submit" name="newCourier" value="Enter New Courier"></form></center>
<? }?>
