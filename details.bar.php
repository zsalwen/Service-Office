<?
$packet = $_GET[packet];
?>
<table><tr><td>


<div style="background-color:#FFFFFF; padding:0px;" align="center">
<table width="100%"  style="padding:0px; font-size: 11px;"><tr><td align="center">
<? if (!$d[uspsVerify]){?><a href="supernova.php?packet=<?=$d[id]?>" target="preview">!!!Verify Addresses!!!</a><? }else{ ?><img src="http://www.usps.com/common/images/v2header/usps_hm_ci_logo2-159x36x8.gif" ><br>Verified by <? echo $d[uspsVerify]; } ?>
<?
// $deadline needs to be dynamic at some point
$received=strtotime($d[date_received]);
$deadline=$received+432000;
$deadline=date('F jS Y',$deadline);
$days=number_format((time()-$received)/86400,0);
$hours=number_format((time()-$received)/3600,0);
?>
 </td><td align="center">
<? if(!$d[caseVerify]){ ?> <a href="validateCase.php?case=<?=$d[case_no]?>&packet=<?=$d[id]?>&county=<?=$d[circuit_court]?>" target="preview">!!!Verify Case Number!!!</a><? }else{ ?><img src="http://www.courts.state.md.us/newlogosm.gif"><br>Verified by <? echo $d[caseVerify]; }?>
</td><td align="center">
<? if(!$d[qualityControl]){ ?> <a href="entryVerify.php?packet=<?=$d[id]?><? if ($d[service_status] == 'MAIL ONLY'){ echo '&matrix=1';} ?>&frame=no" target="preview">!!!Verify Data Entry!!!</a><? }else{ ?><img src="http://staff.mdwestserve.com/small.logo.gif" height="41" width="41"><br>Verified by <? echo $d[qualityControl]; }?>
</td><td align="center"><div style="font-size:15pt" ><?=$hours?> Hours || <?=$days?> Days<br>Deadline: <?=$deadline?><div></td></tr></table>
</div>

</td><td>

<!-- Start Service Timeline Toolbar -->
<table align="center" ><tr>
<? $test1 = getTime($packet,'Data Entry');?>
<td><div class="<?=$test1[css];?>"><?=$test1[event];?><br><?=$test1[eDate];?></div></td>
<? $test2 = getTime($packet,'Dispatched');?>
<td><div class="<?=$test2[css];?>"><?=$test2[event];?><br><?=$test2[eDate];?></div></td>
<? $test3 = getTime($packet,'Completing Service');?>
<? if (!$test3[eDate] && $test2[eDate]){ ?>
<td><div class="active">Service In Progress<br><?=date('m/d/y');?></div></td>
<? } else{ ?>
<td><div class="pending">Service In Progress<br></div></td>
<? } ?>
<td><div class="<?=$test3[css];?>"><?=$test3[event];?><br><?=$test3[eDate];?></div></td>
<? $test4 = getTime($packet,'Confirmed Filing');?>
<? if (!$test4[eDate] && $test3[eDate]){ ?>
<td><div class="active">Post-Service<br><?=date('m/d/y');?></div></td>
<? } else{ ?>
<td><div class="pending">Post-Service<br></div></td>
<? } ?>
<? if($test4[eDate]){ ?>
<td><div class="<?=$test4[css];?>"><?=$test4[event];?><br><?=$test4[eDate];?></div></td>
<? }else{ ?>
<td><div class="alert">Estimated Close<br><?=getClose($packet);?></div></td>
<? }?>
<td><div class="alert"style="font-size:10px;"><a href="?packet=<?=$packet?>&rescan='<?=time();?>'">RESCAN</a><hr><?=$rescanStatus;?></div></td>
<td><div class="alert"style="font-size:10px;"><a href="?packet=<?=$packet?>&export='<?=time();?>'">EXPORT</a><hr><?=$exportStatus;?></div></td>
</tr></table>
<!-- End Service Timeline Toolbar -->


</td></tr></table>