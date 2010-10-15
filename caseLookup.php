<? include 'common.php';include 'menu.php'; $i=0;if ($_GET[mark]){
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
    	<td valign="top" bgcolor="#CCFFFF">
        <div style="overflow:auto; height:700; width:100%"><br />
<ol><?
$q="select packet_id, name1, name2, name3, name4, name5, name6 from ps_packets where case_no = '' and filing_status='FILED WITH COURT' and caseLookupFlag = '0'";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
echo "<li><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]#case_no' target='order'>OTD".strtoupper($d[packet_id]).'</a> <a href="?mark=1">DONE</a><div>'.strtoupper($d[name1]).'</div><div>'.strtoupper($d[name2]).'</div><div>'.strtoupper($d[name3]).'</div><div>'.strtoupper($d[name4]).'</div><div>'.strtoupper($d[name5]).'</div><div>'.strtoupper($d[name6]).'</div></li>';
        }
?>        </ol><hr />
<ol><?
$q="select packet_id, name1, name2, name3, name4, name5, name6 from ps_packets where case_no = '' and caseLookupFlag = '0' and filing_status <> 'FILED WITH COURT' and process_status <> 'DUPLICATE' and process_status <> 'DAMAGED PDF' and (status = 'RECEIVED' or status = 'RECIEVED')";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
echo "<li><a href='http://staff.mdwestserve.com/otd/order.php?packet=$d[packet_id]#case_no' target='order'>OTD".strtoupper($d[packet_id]).'</a> <a href="?mark=1">DONE</a><div>'.strtoupper($d[name1]).'</div><div>'.strtoupper($d[name2]).'</div><div>'.strtoupper($d[name3]).'</div><div>'.strtoupper($d[name4]).'</div><div>'.strtoupper($d[name5]).'</div><div>'.strtoupper($d[name6]).'</div></li><hr>';
        }
?>        </ol><hr>
<ol><?
$q="select eviction_id, name1, name2, name3, name4, name5, name6 from evictionPackets where case_no = '' and filing_status='FILED WITH COURT' and caseLookupFlag = '0'";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
echo "<li><a href='http://staff.mdwestserve.com/ev/order.php?packet=$d[eviction_id]#case_no' target='order'>EV".strtoupper($d[eviction_id]).'</a> <a href="?mark=1">DONE</a><div>'.strtoupper($d[name1]).'</div><div>'.strtoupper($d[name2]).'</div><div>'.strtoupper($d[name3]).'</div><div>'.strtoupper($d[name4]).'</div><div>'.strtoupper($d[name5]).'</div><div>'.strtoupper($d[name6]).'</div></li>';
        }
?>        </ol><hr />
<ol><?
$q="select eviction_id, name1, name2, name3, name4, name5, name6 from evictionPackets where case_no = '' and caseLookupFlag = '0' and filing_status <> 'FILED WITH COURT' and process_status <> 'DUPLICATE' and process_status <> 'DAMAGED PDF' and (status = 'RECEIVED' or status = 'RECIEVED')";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
echo "<li><a href='http://staff.mdwestserve.com/ev/order.php?packet=$d[eviction_id]#case_no' target='order'>EV".strtoupper($d[eviction_id]).'</a> <a href="?mark=1">DONE</a><div>'.strtoupper($d[name1]).'</div><div>'.strtoupper($d[name2]).'</div><div>'.strtoupper($d[name3]).'</div><div>'.strtoupper($d[name4]).'</div><div>'.strtoupper($d[name5]).'</div><div>'.strtoupper($d[name6]).'</div></li><hr>';
        }
?>        </ol>
</div>
</td>
        <td valign="top" align="center">
        <iframe name="court" id="court" width="970" height="400" frameborder="0" src="http://casesearch.courts.state.md.us/inquiry/processDisclaimer.jis"></iframe>        
        <iframe id="order" name="order" width="970" height="300" frameborder="0" src=""></iframe>
        </td>
</tr>
<?=$i?> Case Numbers Left to Look Up
</table>
</body>
</html>
<? include 'footer.php'; ?>