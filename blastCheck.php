<?
mysql_connect();
mysql_select_db('joomla');

function checkEmail($email){
	if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)){
		return "FAILED";
	}
}

$r=@mysql_query("select id, email from jos_users");



?>
<table>
	<tr>
		<td>email</td>
		<td>test</td>
	</tr>
<? while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ if (checkEmail($d[email])){?>	
	<tr>
		<td><?=$d[email]?></td>
		<td><?=checkEmail($d[email])?></td>
	</tr>
<? }}?>
</table>
