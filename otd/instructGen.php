<?
mysql_connect();
mysql_select_db('core');
include 'common.php';
$packet = $_GET[packet];
if ($_GET[def] != 'ALL'){
	$i=$_GET[def];
}else{
	$i=1;
}
$query="SELECT packet_id, date_received, server_notes, name1, name2, name3, name4, name5, name6, server_id, server_ida, server_idb, server_idc, server_idd, server_ide, address1, address1a, address1b, address1c, address1d, address1e, city1, city1a, city1b, city1c, city1d, city1e, state1, state1a, state1b, state1c, state1d, state1e, zip1, zip1a, zip1b, zip1c, zip1d, zip1e, address2, address2a, address2b, address2c, address2d, address2e, city2, city2a, city2b, city2c, city2d, city2e, state2, state2a, state2b, state2c, state2d, state2e, zip2, zip2a, zip2b, zip2c, zip2d, zip2e, address3, address3a, address3b, address3c, address3d, address3e, city3, city3a, city3b, city3c, city3d, city3e, state3, state3a, state3b, state3c, state3d, state3e, zip3, zip3a, zip3b, zip3c, zip3d, zip3e, address4, address4a, address4b, address4c, address4d, address4e, city4, city4a, city4b, city4c, city4d, city4e, state4, state4a, state4b, state4c, state4d, state4e, zip4, zip4a, zip4b, zip4c, zip4d, zip4e, address5, address5a, address5b, address5c, address5d, address5e, city5, city5a, city5b, city5c, city5d, city5e, state5, state5a, state5b, state5c, state5d, state5e, zip5, zip5a, zip5b, zip5c, zip5d, zip5e, address6, address6a, address6b, address6c, address6d, address6e, city6, city6a, city6b, city6c, city6d, city6e, state6, state6a, state6b, state6c, state6d, state6e, zip6, zip6a, zip6b, zip6c, zip6d, zip6e FROM ps_packets WHERE packet_id = '$packet'";
$result=@mysql_query($query) or die ("Query: $query<br>".mysql_error());
$data=mysql_fetch_array($result,MYSQL_ASSOC);
if ($_GET[type] == 'A'){
	$ver=$i."a";
	$verb=$i."b";
	$verc=$i."c";
	$verd=$i."d";
	$vere=$i."e";
	if ($_GET[def] != 'ALL'){
		$name=ucwords($data["name$i"]);
	}else{
		$name="[NAME]";
	}
	$add1ex=$data["address$vere"].' '.$data["city$vere"].', '.$data["state$vere"].' '.$data["zip$vere"];
	$add1dx=$data["address$verd"].' '.$data["city$verd"].', '.$data["state$verd"].' '.$data["zip$verd"];
	$add1cx=$data["address$verc"].' '.$data["city$verc"].', '.$data["state$verc"].' '.$data["zip$verc"];
	$add1bx=$data["address$verb"].' '.$data["city$verb"].', '.$data["state$verb"].' '.$data["zip$verb"];
	$add1ax=$data["address$ver"].' '.$data["city$ver"].', '.$data["state$ver"].' '.$data["zip$ver"];
	$add1x = $data["address$i"].' '.$data["city$i"].', '.$data["state$i"].' '.$data["zip$i"];
	 if ($data[server_ida]){ ?>
		<? if ($data[server_idb]){ ?>
			<ol><li>
			<? if ($data[server_ide]){ ?>
				<?=id2name($data[server_ide])?> is to make 1 service attempt on <?=$name?> at <?=$add1ex?>.</li><li>
			<? } ?>
			<? if ($data[server_idd]){ ?>
				<?=id2name($data[server_idd])?> is to make 1 service attempt on <?=$name?> at <?=$add1dx?>.</li><li>
			<? } ?>
			<? if ($data[server_idc]){ ?>
				<?=id2name($data[server_idc])?> is to make 1 service attempt on <?=$name?> at <?=$add1cx?>.</li><li>
			<? } ?>
			<?=id2name($data[server_idb])?> is to make 1 service attempt on <?=$name?> at <?=$add1bx?>.</li><li>
			<?=id2name($data[server_ida])?> is to make 1 service attempt on <?=$name?> at <?=$add1ax?>.</li><li> 
			After all other attempts have proven unsuccessful, 
			If <?=id2name($data[server_ida])?> or <?=id2name($data[server_idb])?> is unable to serve <?=$name?>:<br />
			<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>
		<? }elseif($data[address1b]){ ?>
			<ol><li><?=id2name($data[server_ida])?> is to make 1 service attempt on <?=$name?> at <?=$add1bx?>.</li><li>
			<?=id2name($data[server_id])?> is to make 1 service attempt on <?=$name?> at <?=$add1ax?>.</li><li> 
			After all other attempts have proven unsuccessful, 
			If <?=id2name($data[server_ida])?> or <?=id2name($data[server_id])?> is unable to serve <?=$name?>:<br />
			<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>
		<? }else{?>
	<ol><li><?=id2name($data[server_ida])?> is to make 2 service attempts on <?=$name?> at <?=$add1ax?> on different days.</li><li> 
	After all other attempts have proven unsuccessful, 
	If <?=id2name($data[server_ida])?> is unable to serve <?=$name?>:<br />
	<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>
	<?		}
	 }elseif($data[address1a]){?>
		<? if ($data[address1b]){?>
	<ol><li><?=id2name($data[server_id])?> is to make 1 service attempt on <?=$name?> at <?=$add1bx?>.</li><li>
	<?=id2name($data[server_id])?> is to make 1 service attempt on <?=$name?> at <?=$add1ax?>.</li><li>
	After all other attempts have proven unsuccessful, 
	If <?=id2name($data[server_id])?> is unable to serve <?=$name?>:<br />
	<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>        	
		<? }else{?>
	<ol><li><?=id2name($data[server_id])?> is to make 2 service attempts on <?=$name?> at <?=$add1ax?> on different days.</li><li>
	After all other attempts have proven unsuccessful, 
	If <?=id2name($data[server_id])?> is unable to serve <?=$name?>:<br />
	<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>
	<? }
	 }else{?>
	<ol><li><?=id2name($data[server_id])?> is to make 2 service attempts on <?=$name?> at <?=$add1x?> on different days.</li><li>
	After all other attempts have proven unsuccessful, 
	If <?=id2name($data[server_id])?> is unable to serve <?=$name?>:<br />
	<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li></ol>
	<? }?></ol></ol>
<? }elseif($_GET[type] == 'B'){ 
	$ver=$i."a";
	$verb=$i."b";
	$verc=$i."c";
	$verd=$i."d";
	$vere=$i."e";
	if ($_GET[def] != 'ALL'){
		$name=ucwords($data["name$i"]);
	}else{
		$name="[NAME]";
	}
	if ($data["address$vere"]){
	$add1ex=$data["address$vere"].', '.$data["city$vere"].', '.$data["state$vere"].' '.$data["zip$vere"];
	}
	if ($data["address$verd"]){
	$add1dx=$data["address$verd"].', '.$data["city$verd"].', '.$data["state$verd"].' '.$data["zip$verd"];
	}
	if ($data["address$verc"]){
	$add1cx=$data["address$verc"].', '.$data["city$verc"].', '.$data["state$verc"].' '.$data["zip$verc"];
	}
	if ($data["address$verb"]){
	$add1bx=$data["address$verb"].', '.$data["city$verb"].', '.$data["state$verb"].' '.$data["zip$verb"];
	}
	if ($data["address$ver"]){
	$add1ax=$data["address$ver"].', '.$data["city$ver"].', '.$data["state$ver"].' '.$data["zip$ver"];
	}
	$add1x = $data["address$i"].', '.$data["city$i"].', '.$data["state$i"].' '.$data["zip$i"];
	$s=id2name($data[server_id]);
	if ($data[server_ida] && $data[address1a]){
		$sa=id2name($data[server_ida]);
	}else{
		$sa=id2name($data[server_id]);
	}
	if ($data[server_idb] && $data[address1b]){
		$sb=id2name($data[server_idb]);
	}else{
		$sb=id2name($data[server_id]);
	}
	if ($data[server_idc] && $data[address1c]){
		$sc=id2name($data[server_idc]);
	}else{
		$sc=id2name($data[server_id]);
	}
	if ($data[server_idd] && $data[address1d]){
		$sd=id2name($data[server_idd]);
	}else{
		$sd=id2name($data[server_id]);
	}
	if ($data[server_ide] && $data[address1e]){
		$se=id2name($data[server_ide]);
	}else{
		$se=id2name($data[server_id]);
	}
	?>
	<? if ($data[address1a]){?>
		<div style='padding-left: 25px; font-weight: bold;'>Before any subsequent attempts are made, ensure that two service attempts have been made at <?=$add1x?> by <?=$s?>.<br></div>
		<input type='checkbox'> <b><?=$sa?></b>: service attempt at <?=$add1ax?>.<br>
		<input type='checkbox'> <b><?=$sa?></b>: service attempt at <?=$add1ax?>.<br>
		<? if ($data[address1b]){ ?>
			<input type='checkbox'> <b><?=$sb?></b>: service attempt at <?=$add1bx?>.<br>
			<input type='checkbox'> <b><?=$sb?></b>: service attempt at <?=$add1bx?>.<br>
			<? if ($data[address1c]){ ?>
				<input type='checkbox'> <b><?=$sc?></b>: service attempt at <?=$add1cx?>.<br>
				<input type='checkbox'> <b><?=$sc?></b>: service attempt at <?=$add1cx?>.<br>
				<? if ($data[address1d]){ ?>
					<input type='checkbox'> <b><?=$sd?></b>: service attempt at <?=$add1dx?>.<br>
					<input type='checkbox'> <b><?=$sd?></b>: service attempt at <?=$add1dx?>.<br>
					<? if ($data[address1e]){ ?>
						<input type='checkbox'> <b><?=$se?></b>: service attempt at <?=$add1ex?>.<br>
						<input type='checkbox'> <b><?=$se?></b>: service attempt at <?=$add1ex?>.<br>
					<? } ?>
				<? } ?>
			<? } ?>
		<? } ?>
	<? } ?>
	<input type='checkbox'> <b><?=$s?></b>: service attempt at <?=$add1x?>.<br>
	<input type='checkbox'> <b><?=$s?></b>: service attempt at <?=$add1x?>.<br>
	<input type='checkbox'> After all other attempts have proven unsuccessful, <b><?=$s?></b> is to post <?=$add1x?>.<br>
	<input type='checkbox'> <b>MDWestServe</b> is to mail.
<? }elseif($_GET[type] == 'C'){
	$ver=$i."a";
	$verb=$i."b";
	$verc=$i."c";
	$verd=$i."d";
	$vere=$i."e";
	if ($_GET[def] != 'ALL'){
		$name=ucwords($data["name$i"]);
	}else{
		$name="[NAME]";
	}
	$add1ex=$data["address$vere"].' '.$data["city$vere"].', '.$data["state$vere"].' '.$data["zip$vere"];
	$add1dx=$data["address$verd"].' '.$data["city$verd"].', '.$data["state$verd"].' '.$data["zip$verd"];
	$add1cx=$data["address$verc"].' '.$data["city$verc"].', '.$data["state$verc"].' '.$data["zip$verc"];
	$add1bx=$data["address$verb"].' '.$data["city$verb"].', '.$data["state$verb"].' '.$data["zip$verb"];
	$add1ax=$data["address$ver"].' '.$data["city$ver"].', '.$data["state$ver"].' '.$data["zip$ver"];
	$add1x = $data["address$i"].' '.$data["city$i"].', '.$data["state$i"].' '.$data["zip$i"];
	?>
	<ul><li><b>PERFORM ALL ATTEMPTS ON SEPARATE DAYS.</b></li></ul>
<? if ($data[server_ida]){ ?>
	<? if ($data[server_idb]){ ?>
		<ol><li>
		<? if ($data[server_ide]){ ?>
			<?=id2name($data[server_ide])?> is to make 1 service attempt on <?=$name?> at <?=$add1ex?>.</li><li>
		<? } ?>
		<? if ($data[server_idd]){ ?>
			<?=id2name($data[server_idd])?> is to make 1 service attempt on <?=$name?> at <?=$add1dx?>.</li><li>
		<? } ?>
		<? if ($data[server_idc]){ ?>
			<?=id2name($data[server_idc])?> is to make 1 service attempt on <?=$name?> at <?=$add1cx?>.</li><li>
		<? } ?>
		<?=id2name($data[server_idb])?> is to make 1 service attempt on <?=$name?> at <?=$add1bx?>.</li><li>
		<?=id2name($data[server_ida])?> is to make 1 service attempt on <?=$name?> at <?=$add1ax?>.</li><li> 
		After all other attempts have proven unsuccessful, <b>and on a separate day from any other attempts,</b><br>
		If <?=id2name($data[server_ida])?> or <?=id2name($data[server_idb])?> is unable to serve <?=$name?>:<br />
		<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>
	<? }elseif($data[address1b]){ ?>
		<ol><li><?=id2name($data[server_ida])?> is to make 1 service attempt on <?=$name?> at <?=$add1bx?>.</li><li>
		<?=id2name($data[server_id])?> is to make 1 service attempt on <?=$name?> at <?=$add1ax?>.</li><li> 
		After all other attempts have proven unsuccessful, <b>and on a separate day from any other attempts,</b><br>
		If <?=id2name($data[server_ida])?> or <?=id2name($data[server_id])?> is unable to serve <?=$name?>:<br />
		<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>
	<? }else{?>
	<ol><li><?=id2name($data[server_ida])?> is to make 2 service attempts on <?=$name?> at <?=$add1ax?> on different days.</li><li> 
	After all other attempts have proven unsuccessful, <b>and on a separate day from any other attempts,</b><br>
	If <?=id2name($data[server_ida])?> is unable to serve <?=$name?>:<br />
	<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>
	<?		}
	 }elseif($data[address1a]){?>
		<? if ($data[address1b]){?>
	<ol><li><?=id2name($data[server_id])?> is to make 1 service attempt on <?=$name?> at <?=$add1bx?>.</li><li>
	<?=id2name($data[server_id])?> is to make 1 service attempt on <?=$name?> at <?=$add1ax?>.</li><li>
	After all other attempts have proven unsuccessful, <b>and on a separate day from any other attempts,</b><br>
	If <?=id2name($data[server_id])?> is unable to serve <?=$name?>:<br />
	<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>        	
		<? }else{?>
	<ol><li><?=id2name($data[server_id])?> is to make 2 service attempts on <?=$name?> at <?=$add1ax?> on different days.</li><li>
	After all other attempts have proven unsuccessful, <b>and on a separate day from any other attempts,</b><br>
	If <?=id2name($data[server_id])?> is unable to serve <?=$name?>:<br />
	<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li>
	<? }
	 }else{?>
	<ol><li><?=id2name($data[server_id])?> is to make 2 service attempts on <?=$name?> at <?=$add1x?> on different days.</li><li>
	After all other attempts have proven unsuccessful, <b>and on a separate day from any other attempts,</b><br>
	If <?=id2name($data[server_id])?> is unable to serve <?=$name?>:<br />
	<?=id2name($data[server_id])?> is to post <?=$add1x?>.</li></ol>
	<? }?></ol></ol>	
<? }else{
	echo "NO TYPE FOUND!";
} ?>