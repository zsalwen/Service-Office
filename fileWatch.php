Set File Watch<br>
<?
mysql_connect();
mysql_select_db('service');
if ($_POST[clientFile] && $_POST[message]){
@mysql_query("insert into fileWatch (clientFile, message) values ('$_POST[clientFile]', '$_POST[message]')");
}
?>

<table>
	<tr>
		<td>File</td>
		<td>Message</td>
	</tr>
<form method="post">	<tr>
		<td><input name="clientFile"></td>
		<td><input name="message"><input type="submit" value="Add"></td>
	</tr></form>
	<?
	$r=mysql_query("select * from fileWatch order by clientFile DESC");
	while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		echo "<tr><td>$d[clientFile]</td><td>$d[message]</td></tr>";
	}
	?>
</table>