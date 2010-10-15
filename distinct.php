<?
include 'common.php';

function approved($test){
// pass
if ($test == "IN PROGRESS"){return "#00FF00";}
if ($test == "CANCELLED"){return "#00FF00";}
if ($test == "PERSONAL DELIVERY"){return "#00FF00";}
if ($test == "MAILING AND POSTING"){return "#00FF00";}
if ($test == "ASSIGNED"){return "#00FF00";}
if ($test == "SERVICE CONFIRMED"){return "#00FF00";}
// fail
if ($test == "RECIEVED"){return "#FF0000";}
if ($test == ""){return "#FF0000";}
if ($test == "CANCELLED/LOT"){return "#FF0000";}
// question
return "#ffff00";
}
$r1=@mysql_query("select DISTINCT status from ps_packets");
$r2=@mysql_query("select DISTINCT process_status from ps_packets");
$r3=@mysql_query("select DISTINCT service_status from ps_packets");
$r4=@mysql_query("select DISTINCT affidavit_status from ps_packets");
$r5=@mysql_query("select DISTINCT photo_status from ps_packets");

?>
<style>
div {
width:300px;
text-align:left;
}
</style>
<center>
<div>ps_packets.status
<? while ($d1=mysql_fetch_array($r1,MYSQL_ASSOC)){ ?>
<li style="background-color:<?=approved($d1[status])?>"><?=$d1[status]?></li>
<? }?></div>
<div>ps_packets.process.status
<? while ($d2=mysql_fetch_array($r2,MYSQL_ASSOC)){ ?>
<li style="background-color:<?=approved($d2[process_status])?>"><?=$d2[process_status]?></li>
<? }?></div>
<div>ps_packets.service_status
<? while ($d3=mysql_fetch_array($r3,MYSQL_ASSOC)){ ?>
<li style="background-color:<?=approved($d3[service_status])?>"><?=$d3[service_status]?></li>
<? }?></div>
<div>ps_packets.affidavit_status
<? while ($d4=mysql_fetch_array($r4,MYSQL_ASSOC)){ ?>
<li style="background-color:<?=approved($d4[affidavit_status])?>"><?=$d4[affidavit_status]?></li>
<? }?></div>
<div>ps_packets.photo_status
<? while ($d5=mysql_fetch_array($r5,MYSQL_ASSOC)){ ?>
<li style="background-color:<?=approved($d5[photo_status])?>"><?=$d5[photo_status]?></li>
<? }?></div>
</center>
<?
include 'footer.php';
?>