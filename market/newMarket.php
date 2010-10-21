<?
// connect
mysql_connect();
mysql_select_db('core');
if ($_POST[submit]){
	@mysql_query("INSERT INTO market (type, name, contact, phone, address, createDate, createID) VALUES ('$_POST[type]','$_POST[name]','$_POST[contact]','$_POST[phone]', $_POST[address], NOW(), '".$_COOKIE[psdata][user_id]."')") or die (mysql_error());
	echo "<table><tr><td>A new user has been created:<br>ID: ".mysql_insert_id()."<br>Type: $_POST[type]<br>Name: $_POST[name]<br>Contact: $_POST[contact]<br>Phone: $_POST[phone]";
	error_log("[".date('h:iA n/j/y')."] ".$_COOKIE[psdata][name]." CREATED NEW MARKETING CONTACT FOR ".strtoupper($_POST[type])." ".strtoupper($_POST[name]),3,"/logs/user.log");
}
?>
<form method="post">
<table align="center">
	<tr>
		<td>Type:</td>
		<td><select name="type"><option>attorney</option><option>auctioneer</option></select></td>
	</tr>
	<tr>
		<td>Name:</td>
		<td><input name="name"></td>
	</tr>
	<tr>
		<td>Contact:</td>
		<td><input name="contact"></td>
	</tr>
	<tr>
		<td>Phone:</td>
		<td><input name="phone"></td>
	</tr>
	<tr>
		<td>Address</td>
		<td><textarea name='address'></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="submit" value="Submit"></td>
	</tr>
</table>