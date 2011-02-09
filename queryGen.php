<?
mysql_connect();
mysql_select_db('service');
if ($_POST[submit]){
	$pass=rand(1000,9999);
	$q="INSERT INTO `contacts` (`contact_id`, `user_id`, `name`, `email`, `attorneys_id`, `phone`, `position`, `password`, `uid`, `uid_date`, `uid_ip`, `online_now`, `user_admin`, `master_admin`)
VALUES (NULL, '', '$_POST[name]', '$_POST[email]', '$_POST[attorneys_id]', '$_POST[phone]', '', '$pass', '', '', '', '', 'NO', 'NO')";
}
?>
<form method='post'>
<table align='center'>
	<tr>
		<td>Name:</td>
		<td><input name="name" value="<?=$_POST[name]?>"></td>
	</tr>
	<tr>
		<td>Email:</td>
		<td><input name="email" value="<?=$_POST[email]?>"></td>
	</tr>
	<tr>
		<td>Attorneys ID:</td>
		<td><input name="attorneys_id" value="<?=$_POST[attorneys_id]?>"></td>
	</tr>
	<tr>
		<td>Phone:</td>
		<td><input name="phone" value="<?=$_POST[phone]?>"></td>
	</tr>
	<tr>
		<td align='center' colspan='2'><input type='submit' name='submit' value='Submit'></td>
	</tr>
</table>
</form>
<? if ($q){ ?>
<center><fieldset style="border-style:solid 1px; font-size: 16px; width:450px; text-align:center; padding:0px;"><legend>Query</legend><?=$q?></fieldset></center>
<? } ?>