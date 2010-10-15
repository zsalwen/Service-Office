<? include 'common.php';

function wash($str){
	$str=strtoupper($str);
	$str=str_replace('&',' AND ',$str);
	$str=str_replace('#',' NO. ',$str);
	return $str;
}

?>
<style type="text/css">
    @media print {
      .noprint { display: none; }
    }
  </style> 
<div class='noprint'>
<form>
packet<input name="packet" value="<?=$_GET[packet]?>"> [id]<br>
defendant<input name="def" value="<?=$_GET[def]?>"> 1 / 2 / 3 / 4<br>
address<input name="add" value="<?=$_GET[add]?>"> [blank] / a / b / PO<br>
service type<select name="svc"><option value='OTD'>FORECLOSURE</option><option value="EV">EVICTION</option></select>
<hr>
OR
<hr>
name<input name="name" value="<?=$_GET['name']?>"><br>
line1<input name="line1" value="<?=$_GET['line1']?>"><br>
line2<input name="line2" value="<?=$_GET['line2']?>"><br>
city st zip<input name="csz" value="<?=$_GET['csz']?>">
<hr>
AND
<hr>
article no.<input name="art" size="50" value="<?=$_GET['art']?>"><br>
<select name="card"><option><?=$_GET[card]?></option><option value="green">Green - Front (Record ART#)</option><option value="address">Green Back</option><option value="white">White - Front</option><option value="mail">Label - Return</option><option value="return">Label - To</option><option value="envelope">Envelope Front</option></select>
<input type="submit" value="Load Lables"></form>
</div>
<? if ($_GET[card] == 'mail' || $_GET[card] == "return"){ ?>
<br>
<br>
<? }else{?>
<? }?>
<?
$card = $_GET[card];
$name = $_GET[name];
$line1 = $_GET[line1];
$line2 = $_GET[line2];
$csz = $_GET[csz];
$art = $_GET[art];

if ($_GET[packet]){
	if($_GET[svc] == 'EV'){
		$r=@mysql_query("select * from evictionPackets where eviction_id = '$_GET[packet]'");
	}else{
		$r=@mysql_query("select * from ps_packets where packet_id = '$_GET[packet]'");
	}
	$d=mysql_fetch_array($r, MYSQL_ASSOC) or die(mysql_error());
	
	if ($_GET[card] == "green" && $_GET[art]){
		if($_GET[svc] == 'EV'){
			@mysql_query("update evictionPackets set article$_GET[def]$_GET[add] = '$_GET[art]', gcStatus='PRINTED' where eviction_id = '$_GET[packet]'");
		}else{
			@mysql_query("update ps_packets set article$_GET[def]$_GET[add] = '$_GET[art]', gcStatus='PRINTED' where packet_id = '$_GET[packet]'");
		}
	}
	
	$card = $_GET[card];
	$name = wash($d["name$_GET[def]"]);
	if ($_GET[add] == 'PO'){
		$po = wash($d[pobox]);
		$line1 = wash($d[pobox]);
		$csz = wash($d[pocity].', '.$d[postate].' '.$d[pozip]);
	}elseif ($_GET[add] == 'PO2'){
		$po = wash($d[pobox2]);
		$line1 = wash($d[pobox2]);
		$csz = wash($d[pocity2].', '.$d[postate2].' '.$d[pozip2]);
	}else{
		$line1 = wash($d["address$_GET[def]$_GET[add]"]);
		$csz = wash($d["city$_GET[def]$_GET[add]"].', '.$d["state$_GET[def]$_GET[add]"].' '.$d["zip$_GET[def]$_GET[add]"]);
		$art = $_GET[art];
	}
}
$cord = "$_GET[packet]-$_GET[def]$_GET[add]";
if ($card == "envelope"){
	if ($_GET[line2] != ''){
		wash($line2=$_GET[line2]);
	}else{
		$line2=$d[client_file];
	}
?>
<div align="center">

<img src="http://staff.mdwestserve.com/barcode.php?barcode=<?=$_GET[svc]?><?=$_GET[packet];?>&width=400&height=40"><br>
<img src="http://staff.mdwestserve.com/envelopecard.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&csz=<?=strtoupper($csz)?>&cord=<?=$cord?>&svc=<?=$_GET[svc]?>">
</div>
<? }else{ ?>
<div align="center" 
	<? if($card =="green"){?> style="padding-right:150px;" <? }?>
	<? if($card =="mail"  || $_GET[card] == "return"){?> style="padding-left:50px;" <? }?>
	<? if($card =="white"){?> style="padding-right:120px; padding-top:180px" <? }?>
    ><img src="http://staff.mdwestserve.com/<?=$card?>card.jpg.php?name=<?=strtoupper($name)?>&line1=<?=strtoupper(str_replace('#','no. ',$line1))?>&line2=<?=strtoupper($line2)?>&csz=<?=strtoupper($csz)?>&art=<?=$art?>&cord=<?=$cord?>&case_no=<?=str_replace('0','&Oslash;',strtoupper($d[case_no]))?><? if ($_GET[noCost]){ echo "&noCost=1";} ?>"><? if($card=='mail'){echo "<img src='gfx/mail.logo.gif'>";}?></div>
	<? } ?>
