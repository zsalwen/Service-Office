<?
mysql_connect();
mysql_select_db('hacking');
$r=@mysql_query("select * from fisherSales order by county, online DESC");
?>
<style>td { font-size:12px;} </style>
<table cellspacing="0" cellpadding="1" border="1" style="border-collapse:collapse;">
	<tr>
		<td></td>
		<td bgcolor="#FF0000">County</td>
		<td bgcolor="#FF00FF">Address</td>
		<td bgcolor="#FFFF00">Names</td>
		<td bgcolor="#FFFF00">Case Number</td>
		<td bgcolor="#0000FF">Server Notes</td>
	</tr>	
<? while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){ if ($d[id]){ ?>
	<tr>
		<td><a href="fisherMod.php?id=<?=$d[id]?>">E</a></td>
		<td><?=$d[county];?></td>
		<td><?=$d[address]?></td>
		<td><?=$d[caseNames]?></td>		
		<td><?=$d[caseNumber]?></td>
		<td><?=$d[server]?></td>
	</tr>	
<? } }?>
</table>
