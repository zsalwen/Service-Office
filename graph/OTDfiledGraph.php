<?
mysql_connect();
mysql_select_db('core');
include "functions.php";
//z will be the largest number encountered
$z=0;
$zi=0;
$year=2008;
$curYear=date('Y');

while ($year <= $curYear){
	$yr=substr($year,-2);
	if ($year != $curYear){
		$topMo=12;
	}else{
		$topMo=date('n');
	}
	$i=0;
	$received='';
	while ($i < $topMo){$i++;$zi++;
		if ($i < 10){
			$i2='0'.$i;
		}else{
			$i2=$i;
		}
		$r=mysql_query("SELECT packet_id FROM ps_packets WHERE fileDate LIKE '%$year-$i2%'");
		$received["$i"]=mysql_num_rows($r);
		if ($received["$i"] > 0){}else{
			$received["$i"]='0';
		}
		if ($received["$i"] > $z){
			$z=$received["$i"];
			$zz=($zi-1);
			$zzz="-".monthConvert($i2)." $yr";
		}
		if ($src == ''){
			$src = $received["$i"];
		}else{
			$src .= ','.$received["$i"];
		}
		$src2 .= '|'.monthConvert($i2)." $yr";
	}
	$year++;
}
//pull BURSON files
$year=2008;
while ($year <= $curYear){
	if ($year != $curYear){
		$topMo=12;
	}else{
		$topMo=date('n');
	}
	$i=0;
	while ($i < $topMo){$i++;
		if ($i < 10){
			$i2='0'.$i;
		}else{
			$i2=$i;
		}
		$r=mysql_query("SELECT packet_id FROM ps_packets WHERE fileDate LIKE '%$year-$i2%' AND attorneys_id='1'");
		$value=mysql_num_rows($r);
		if ($value > 0){}else{
			$value='0';
		}
		if ($burson == ''){
			$burson = $value;
		}else{
			$burson .= ','.$value;
		}
	}
	$year++;
}
//pull WHITE files
$year=2008;
while ($year <= $curYear){
	if ($year != $curYear){
		$topMo=12;
	}else{
		$topMo=date('n');
	}
	$i=0;
	while ($i < $topMo){$i++;
		if ($i < 10){
			$i2='0'.$i;
		}else{
			$i2=$i;
		}
		$r=mysql_query("SELECT packet_id FROM ps_packets WHERE fileDate LIKE '%$year-$i2%' AND attorneys_id='3'");
		$value=mysql_num_rows($r);
		if ($value > 0){}else{
			$value='0';
		}
		if ($white == ''){
			$white = $value;
		}else{
			$white .= ','.$value;
		}
	}
	$year++;
}
//pull BGW files
$year=2008;
while ($year <= $curYear){
	if ($year != $curYear){
		$topMo=12;
	}else{
		$topMo=date('n');
	}
	$i=0;
	while ($i < $topMo){$i++;
		if ($i < 10){
			$i2='0'.$i;
		}else{
			$i2=$i;
		}
		$r=mysql_query("SELECT packet_id FROM ps_packets WHERE fileDate LIKE '%$year-$i2%' AND attorneys_id='70'");
		$value=mysql_num_rows($r);
		if ($value > 0){}else{
			$value='0';
		}
		if ($bgw == ''){
			$bgw = $value;
		}else{
			$bgw .= ','.$value;
		}
	}
	$year++;
}
//pull OTHER files
$year=2008;
while ($year <= $curYear){
	if ($year != $curYear){
		$topMo=12;
	}else{
		$topMo=date('n');
	}
	$i=0;
	while ($i < $topMo){$i++;
		if ($i < 10){
			$i2='0'.$i;
		}else{
			$i2=$i;
		}
		$r=mysql_query("SELECT packet_id FROM ps_packets WHERE fileDate LIKE '%$year-$i2%' AND attorneys_id <> '1' AND attorneys_id <> '3' AND attorneys_id <> '70'");
		$value=mysql_num_rows($r);
		if ($value > 0){}else{
			$value='0';
		}
		if ($other == ''){
			$other = $value;
		}else{
			$other .= ','.$value;
		}
	}
	$year++;
}
$z1=number_format($z/5,0);
$z2=number_format($z1*2,0);
$z3=number_format($z1*3,0);
$z4=number_format($z1*4,0);
/*echo "<table><tr>";
echo "<td></td>".str_replace('|','</td><td>',$src2).'</td></tr>';
echo "<td>ALL:</td><td>".str_replace(',','</td><td>',$src).'</td></tr>';
echo "<tr><td>BURSON:</td><td>".str_replace(',','</td><td>',$burson).'</td></tr>';
echo "<tr><td>WHITE:</td><td>".str_replace(',','</td><td>',$white).'</td></tr>';
echo "<tr><td>BGW:</td><td>".str_replace(',','</td><td>',$bgw).'</td></tr>';
echo "<tr><td>OTHER:</td><td>".str_replace(',','</td><td>',$other).'</td>';
echo "</tr></table>";*/
$src="http://2.chart.apis.google.com/chart?cht=lc&chs=900x333&chd=t:".$src."|".$burson."|".$white."|".$bgw."|".$other."&chxl=0:".$src2."|1:|0|$z1|$z2|$z3|$z4|$z&chtt=Foreclosure File Dates 2008-$curYear&chdl=All Files|Burson|White|BGW|Others&chco=FF0000,00FF00,0000FF,6622AA,FF7700";
$markers="&chm=d,990000,0,-1,5|d,009900,1,-1,5|d,000099,2,-1,5|d,662266,3,-1,5|d,994400,4,-1,5|t$z$zzz,000000,0,$zz,10";
$rest="&chxt=x,y&chds=0,".$z."&chxtc=0,10|1,-980&chxs=0,000000,7|1,000000,10,-1,lt,333333";
?>
<img src="<?=$src.$rest.$markers?>" width="100%">