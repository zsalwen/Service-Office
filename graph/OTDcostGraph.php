<?
mysql_connect();
mysql_select_db('core');
include "functions.php";
$year=$_GET[year];
$attid=$_GET[attid];
$month=0;

$mainPaidIn=0;
$mainOwed=0;
$mainPaidOut=0;
$mainLiveMargin=0;
$mainMargin=0;

$patrick = 5166; // salary - no overtime
$zach = 3120 + 540; // 18 hr + overtime
$danny = 2600; // 15 hr + overtime
$alex = 2253; // 13 / hr

$salary = $patrick + $zach + $danny + $alex; 
$salary2 = $salary * .09; // 9% of monthly total
$health = 2500; // staff health insurance
$courier = 3000; // better aim high...
$month=0;
$year=2008;
$a=0;
$z=0;
$zi=0;
while($month < 12){$month++;$zi++;
	$count = $month;
	$month = leading_zeros($month, '2')	;

	$csv = getPage("http://data.mdwestserve.com/cost.php?month=$month&year=$year", 'MDWS GRAPH', '10', '');	
	$value = explode(',',$csv);
	//echo "<fieldset><legend>$month-$year</legend>$csv</fieldset>";
	if (!is_numeric($value[0])){$value[0]=0;}
	if (!is_numeric($value[1])){$value[1]=0;}
	if (!is_numeric($value[2])){$value[2]=0;}
	if (!is_numeric($value[3])){$value[3]=0;}
	if (!is_numeric($value[4])){$value[4]=0;}
	if($value[0] > $z){
		$z=$value[0];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '08";
		$zSet=0;
	}
	if($value[1] > $z){
		$z=$value[1];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '08";
		$zSet=1;
	}
	if($value[2] > $z){
		$z=$value[2];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '08";
		$zSet=2;
	}
	if($value[3] > $z){
		$z=$value[3];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '08";
		$zSet=3;
	}
	if($value[4] > $z){
		$z=$value[4];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '08";
		$zSet=4;
	}
	if($value[0] < $a){
		$a=$value[0];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '08";
		$aSet=0;
	}
	if($value[1] < $a){
		$a=$value[1];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '08";
		$aSet=1;
	}
	if($value[2] < $a){
		$a=$value[2];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '08";
		$aSet=2;
	}
	if($value[3] < $a){
		$a=$value[3];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '08";
		$aSet=3;
	}
	if($value[4] < $a){
		$a=$value[4];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '08";
		$aSet=4;
	}
	if ($clientPaid == ''){
		$clientPaid = $value[0];
	}else{
		$clientPaid .= ",".$value[0];
	}
	$mainPaidIn = $mainPaidIn + $value[0];
	if ($balanceDue == ''){
		$balanceDue = $value[1];
	}else{
		$balanceDue .= ",".$value[1];
	}
	$mainOwed = $mainOwed + $value[1];
	if ($contractorPaid == ''){
		$contractorPaid = $value[2];
	}else{
		$contractorPaid .= ",".$value[2];
	}
	$mainPaidOut = $mainPaidOut + $value[2];
	if ($liveMargin == ''){
		$liveMargin = $value[3];
	}else{
		$liveMargin .= ",".$value[3];
	}
	$mainLiveMargin = $mainLiveMargin + $value[3];
	if ($estMargin == ''){
		$estMargin = $value[4];
	}else{
		$estMargin .= ",".$value[4];
	}
	$mainMargin = $mainMargin + $value[4];
	
	$management = ($value[0] + $value[1]) * .10;
	$burn = $salary + $salary2 + $management + $health + $courier;
	//if ($value[4] != 0){
		$total08[$count] = $value[4] - $burn ;
	//}
	$labels .= "|".monthConvert($month)."-08";
}
$year2=$year+1;
$month=0;
while($month < 12){$month++;$zi++;
	$count = $month;
	$month = leading_zeros($month, '2')	;

	$csv = getPage("http://data.mdwestserve.com/cost.php?month=$month&year=$year2", 'MDWS GRAPH', '10', '');		
	$value = explode(',',$csv);
	//echo "<fieldset><legend>$month-$year2</legend>$csv</fieldset>";
	if (!is_numeric($value[0])){$value[0]=0;}
	if (!is_numeric($value[1])){$value[1]=0;}
	if (!is_numeric($value[2])){$value[2]=0;}
	if (!is_numeric($value[3])){$value[3]=0;}
	if (!is_numeric($value[4])){$value[4]=0;}
	if($value[0] > $z){
		$z=$value[0];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '09";
		$zSet=0;
	}
	if($value[1] > $z){
		$z=$value[1];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '09";
		$zSet=1;
	}
	if($value[2] > $z){
		$z=$value[2];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '09";
		$zSet=2;
	}
	if($value[3] > $z){
		$z=$value[3];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '09";
		$zSet=3;
	}
	if($value[4] > $z){
		$z=$value[4];
		$zPos=$zi-1;
		$zMonth="-".monthConvert($month)." '09";
		$zSet=4;
	}
	if($value[0] < $a){
		$a=$value[0];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '09";
		$aSet=0;
	}
	if($value[1] < $a){
		$a=$value[1];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '09";
		$aSet=1;
	}
	if($value[2] < $a){
		$a=$value[2];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '09";
		$aSet=2;
	}
	if($value[3] < $a){
		$a=$value[3];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '09";
		$aSet=3;
	}
	if($value[4] < $a){
		$a=$value[4];
		$aPos=$zi-1;
		$aMonth="-".monthConvert($month)." '09";
		$aSet=4;
	}
	if ($clientPaid == ''){
		$clientPaid = $value[0];
	}else{
		$clientPaid .= ",".$value[0];
	}
	$mainPaidIn = $mainPaidIn + $value[0];
	if ($balanceDue == ''){
		$balanceDue = $value[1];
	}else{
		$balanceDue .= ",".$value[1];
	}
	$mainOwed = $mainOwed + $value[1];
	if ($contractorPaid == ''){
		$contractorPaid = $value[2];
	}else{
		$contractorPaid .= ",".$value[2];
	}
	$mainPaidOut = $mainPaidOut + $value[2];
	if ($liveMargin == ''){
		$liveMargin = $value[3];
	}else{
		$liveMargin .= ",".$value[3];
	}
	$mainLiveMargin = $mainLiveMargin + $value[3];
	if ($estMargin == ''){
		$estMargin = $value[4];
	}else{
		$estMargin .= ",".$value[4];
	}
	$mainMargin = $mainMargin + $value[4];

	$management = ($value[0] + $value[1]) * .10;
	$burn = $salary + $salary2 + $management + $health + $courier;
	//if ($value[4] != 0){
		$total09[$count] = $value[4] - $burn ;
	//}
	$labels .= "|".monthConvert($month)."-09";
}
$counter=0;
while($counter < count($total08)){$counter++;
	$total08[$counter]=number_format($total08[$counter],0);
}
$counter=0;
while($counter < count($total09)){$counter++;
	$total09[$counter]=number_format($total09[$counter],0);
}
$total="|".implode('|',$total08)."|".implode('|',$total09);
/*echo "<table border='1' style='border-collapse:collapse;'><tr>";
echo "<td></td>".str_replace('|','</td><td>',$labels).'</td></tr>';
echo "<td>CLIENT PAID:</td><td>".str_replace(',','</td><td>',$clientPaid).'</td></tr>';
echo "<tr><td>BALANCE DUE:</td><td>".str_replace(',','</td><td>',$balanceDue).'</td></tr>';
echo "<tr><td>CONTRACTOR PAID:</td><td>".str_replace(',','</td><td>',$contractorPaid).'</td></tr>';
echo "<tr><td>LIVE MARGIN:</td><td>".str_replace(',','</td><td>',$liveMargin).'</td></tr>';
echo "<tr><td>EST. MARGIN:</td><td>".str_replace(',','</td><td>',$estMargin).'</td></tr>';
echo "<tr><td>TOTAL:</td><td>".str_replace(',','</td><td>',$total).'</td></tr>';
echo "</table>";*/
$za=(($a*-1)+$z)/5;
//$zb is the vertical percentage where the zero marker should go on the y-axis
$zb=(($a*-100)+$z)/($z+($a*-1));
$zb2=$zb/100;
$z1=$a+$za;
$z2=$z1+$za;
$z3=$z2+$za;
$z4=$z3+$za;
$src="http://chart.apis.google.com/chart?cht=lc&chs=900x333&chd=t:".$clientPaid."|".$balanceDue."|".$contractorPaid."|".$liveMargin."|".$estMargin."&chxl=0:".$labels."|1:|$a|0|$z1|$z2|$z3|$z4|$z|2:|$total&chtt=Costs: 2008-2009|&chdl=Client Paid|Balance Due|Contractor Paid|Live Margin|Est. Margin&chco=FF0000,00FF00,0000FF,800080,FF8040&chxt=x,y,x&chds=$a,$z&chxtc=0,10|1,-980&chxp=1,0,$zb,20,40,60,80,100&chxs=1,000000,10|0,000000,8|2,000000,8&chls=0.5,1,0|0.5,1,0|0.5,1,0|0.5,1,0|0.5,1,0&chm=h,CCBB00,0,$zb2,1&chm=f$z$zMonth,000000,$zSet,$zPos,12|f$a$aMonth,000000,$aSet,$aPos,12";
//$rest="&chxt=x,y&chds=0,".$z."&chxtc=0,10|1,-980&chxs=0,000000,10|1,000000,10,-1,lt,333333&chm=f$z,000000,0,$zPos,15";
?>
<img src="<?=$src?>" width="100%">