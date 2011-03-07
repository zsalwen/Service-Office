<!-- Start Service Timeline Toolbar -->
<table align="center" style="padding:0px;"><tr>
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
