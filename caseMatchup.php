<? include 'common.php';
include 'lock.php';
include 'menu.php';
$i=0;
if ($_GET[mark]){
psActivity("caseNumber");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pull Case Numbers</title>
</head>
<body bgcolor="#999999">
<table width="100%" border="1" style="border-collapse:collapse">
	<tr>
    	<td valign="top" bgcolor="#CCFFFF"><div style="overflow:auto; width:100%; height:700;"><br />
<ol><?
$q="select client_file, name1, name2, name3, name4, name5, name6, packet_id from ps_packets where case_no = '' and caseLookupFlag = '1' and status <> 'CANCELLED'";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
echo "<li><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]' target='order'>".strtoupper($d[client_file]).'</a> <a href="?mark=1">DONE</a><div>'.strtoupper($d[name1]).'</div><div>'.strtoupper($d[name2]).'</div><div>'.strtoupper($d[name3]).'</div><div>'.strtoupper($d[name4]).'</div><div>'.strtoupper($d[name5]).'</div><div>'.strtoupper($d[name6]).'</div></li><hr>';
        }
$q="select client_file, name1, name2, name3, name4, name5, name6, eviction_id from evictionPackets where case_no = '' and caseLookupFlag = '1' and status <> 'CANCELLED'";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
echo "<li><a href='http://staff.mdwestserve.com/ev/order.php?packet=$d[eviction_id]' target='order'>".strtoupper($d[client_file]).'</a> <a href="?mark=1">DONE</a><div>'.strtoupper($d[name1]).'</div><div>'.strtoupper($d[name2]).'</div><div>'.strtoupper($d[name3]).'</div><div>'.strtoupper($d[name4]).'</div><div>'.strtoupper($d[name5]).'</div><div>'.strtoupper($d[name6]).'</div></li><hr>';
        }
?>        </ol></div>
</td>
        <td valign="top" align="center">
        <iframe id="order" name="order" width="970" height="600" frameborder="1" src=""></iframe>
        </td>
</tr>
<CENTER><strong><?=$i?> Case Numbers Awaiting Response</strong></CENTER>
</table>
</body>
</html>
<? include 'footer.php'; ?>