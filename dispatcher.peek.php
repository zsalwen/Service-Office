<?
include 'common.php';
require_once('zipcode.class.php');      // zip code class

$r=@mysql_query("select packet_id from ps_packets where server_id = '$_GET[server]' or server_ida = '$_GET[server]' or server_idb = '$_GET[server]' or server_idc = '$_GET[server]' or server_idd = '$_GET[server]' or server_ide = '$_GET[server]'");
$allfiles = @mysql_num_rows($r);
$r=@mysql_query("select packet_id from ps_packets where process_status='ASSIGNED' and ( server_id = '$_GET[server]' or server_ida = '$_GET[server]' or server_idb = '$_GET[server]' or server_idc = '$_GET[server]' or server_idd = '$_GET[server]' or server_ide = '$_GET[server]')");
$assignedfiles = @mysql_num_rows($r);
$r=@mysql_query("select packet_id from ps_packets where affidavit_status='SERVICE CONFIRMED' and filing_status ='' and ( server_id = '$_GET[server]' or server_ida = '$_GET[server]' or server_idb = '$_GET[server]' or server_idc = '$_GET[server]' or server_idd = '$_GET[server]' or server_ide = '$_GET[server]')");
$confirmfiles = @mysql_num_rows($r);
$r=@mysql_query("select packet_id from ps_packets where affidavit_status='SERVICE CONFIRMED' and filing_status <>'' and ( server_id = '$_GET[server]' or server_ida = '$_GET[server]' or server_idb = '$_GET[server]' or server_idc = '$_GET[server]' or server_idd = '$_GET[server]' or server_ide = '$_GET[server]')");
$compfiles = @mysql_num_rows($r);
?>
<style>
body {padding:0px; margin:0px}

</style>
<?
$z = new zipcode_class;
$miles = $z->get_distance(id2zip($_GET[server]), $_GET[zip]);
?>
<table border="1" align="center" style="border-collapse:collapse;">
	<tr>
    	<td colspan="2" align="center"><a href="zeitachse.php?review=<?=$_GET[server]?>" target="_blank"><em><?=trim(id2name($_GET[server]));?>'s Details</em></a></td>
	</tr>
	<tr>
    	<td colspan="2" align="center"><a href="contractor_review.php?admin=<?=$_GET[server]?>" target="_blank"><em><?=trim(id2name($_GET[server]));?>'s Contacts</em></a></td>
	</tr>
    <tr>
    	<td colspan="2" align="center"><strong><?=id2phone($_GET[server]);?></strong></td>
    </tr>    
    <tr>
    	<td colspan="2" align="center"><?=strtoupper(id2csz($_GET[server]));?></td>
    </tr>    
    <tr>
    	<td colspan="2" align="center"><strong><?=$miles?></strong> MILES SERVER TO SERVE</td>
    </tr>    
    <tr>
        <td>Assigned Files</td>
        <td><strong><?=$assignedfiles?></strong></td>
    </tr>    
    <tr>
        <td>Confirmed Files</td>
        <td><?=$confirmfiles?></td>
    </tr>    
    <tr>
        <td>Completed Files</td>
        <td><?=$compfiles?></td>
    </tr>    
    <tr>
        <td>Cases on Record</td>
        <td><?=$allfiles?></td>
    </tr>    
</table>
