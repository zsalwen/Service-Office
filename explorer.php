<?
mysql_connect();
mysql_select_db('core');
$today = date('Y-m-d');
$r=@mysql_query("select distinct user from explorer where date = '$today'");
?>
<center>Today's Packet Activity</center>
<table align="center" border="1" cellspacing="0" cellpadding="4">
	<tr>
		<?
		while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
		<td align="left" valign='top'>
			<div><?=$d[user]?></div>
			<?
			$r2=@mysql_query("select distinct packet from explorer where date = '$today' and user = '$d[user]' order by packet DESC");
		while($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){ ?>
		<li style='white-space:pre;'><?=$d2[packet]?></li>
		<? } ?>

		</td>
		<? } ?>
	</tr>	
</table>
<center>Not all actions are currently recorded here.</center>