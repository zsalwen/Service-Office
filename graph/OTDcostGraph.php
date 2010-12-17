<?
mysql_connect();
mysql_select_db('core');
include "functions.php";
ini_set("memory_limit","24M");
$year=$_GET[year];
$attid=$_GET[attid];
$month=0;

$mainPaidIn=0;
$mainOwed=0;
$mainPaidOut=0;
$mainLiveMargin=0;
//$mainMargin=0;
/*
$patrick = 5166; // salary - no overtime
$zach = 3120 + 540; // 18 hr + overtime
$danny = 2600; // 15 hr + overtime
$alex = 2253; // 13 / hr

$salary = $patrick + $zach + $danny + $alex;
$salary2 = $salary * .09; // 9% of monthly total
$health = 2500; // staff health insurance
$courier = 3000; // better aim high...*/
//monthly burn rate (not including postage, which is calculated within Service-Web-Service/cost.php
$burn=45186.24;
$curYear=date('Y');
$year=2008;
$a=0;
$z=0;
$zi=0;
$month=0;
while ($year <= $curYear){
	$yr=substr($year,-2);
	if ($year != $curYear){
		$topMo=12;
	}else{
		$topMo=date('n');
	}
	while($month < $topMo){$month++;$zi++;
		$count = $month;
		$month = leading_zeros($month, '2') ;

		$csv = getPage("http://data.mdwestserve.com/cost.php?month=$month&year=$year", 'MDWS GRAPH', '10', '');
		$value = explode(',',$csv);
		//echo "<fieldset><legend>$month-$year</legend>$csv</fieldset>";
		$i=0;
		while ($i < 4){
			if (!is_numeric($value["$i"])){$value["$i"]=0;}
			$i++;
		}
		$i=0;
		while ($i < 4){	
			if($value["$i"] > $z){
				$z=$value["$i"];
				$zPos=$zi-1;
				$zMonth="-".monthConvert($month)." $yr";
				$zSet=0;
			}
			$i++;
		}
		$i=0;
		while ($i < 4){	
			if($value["$i"] < $a){
				$a=$value["$i"];
				$aPos=$zi-1;
				$aMonth="-".monthConvert($month)." $yr";
				$aSet=0;
			}
			$i++;
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
		//$mainMargin = $mainMargin + $value[4];

		//if ($value[4] != 0){
		$total["$count"] = $value[4] - $burn ;
		//}
		$labels .= "|".monthConvert($month)."-$yr";
	}
	$counter=0;
	while($counter < count($total)){$counter++;
		$total["$counter"]=number_format($total["$counter"],0);
	}
	$totalList .= "|".implode('|',$total);
	if ($clientPaidList != ''){
		$clientPaidList .= ",".$clientPaid;
	}else{
		$clientPaidList .= $clientPaid;
	}
	if ($balanceDueList != ''){
		$balanceDueList .= ",".$balanceDue;
	}else{
		$balanceDueList .= $balanceDue;
	}
	if ($contractorPaidList != ''){
		$contractorPaidList .= ",".$contractorPaid;
	}else{
		$contractorPaidList .= $contractorPaid;
	}
	if ($liveMarginList != ''){
		$liveMarginList .= ",".$liveMargin;
	}else{
		$liveMarginList .= $liveMargin;
	}
	if ($estMarginList != ''){
		$estMarginList .= ",".$estMargin;
	}else{
		$estMarginList .= $estMargin;
	}
	$labelsList .= "|".$labels;
	$year++;
}

echo "<table border='1' style='border-collapse:collapse;'><tr>";
echo "<td></td>".str_replace('|','</td><td>',$labelsList).'</td></tr>';
echo "<td>CLIENT PAID:</td><td>".str_replace(',','</td><td>',$clientPaidList).'</td></tr>';
echo "<tr><td>BALANCE DUE:</td><td>".str_replace(',','</td><td>',$balanceDueList).'</td></tr>';
echo "<tr><td>CONTRACTOR PAID:</td><td>".str_replace(',','</td><td>',$contractorPaidList).'</td></tr>';
echo "<tr><td>LIVE MARGIN:</td><td>".str_replace(',','</td><td>',$liveMarginList).'</td></tr>';
echo "<tr><td>EST. MARGIN:</td><td>".str_replace(',','</td><td>',$estMarginList).'</td></tr>';
echo "<tr><td>TOTAL:</td><td>".str_replace('|','</td><td>',$totalList).'</td></tr>';
echo "</table>";
$za=(($a*-1)+$z)/5;
//$zb is the vertical percentage where the zero marker should go on the y-axis
$zb=(($a*-100)+$z)/($z+($a*-1));
$zb2=$zb/100;
$z1=$a+$za;
$z2=$z1+$za;
$z3=$z2+$za;
$z4=$z3+$za;
$src="http://0.chart.apis.google.com/chart?cht=lc&chs=900x333&chd=t:".$clientPaidList."|".$balanceDueList."|".$contractorPaidList."|".$liveMarginList."|".$estMarginList."&chxl=0:".$labelsList."|1:|$a|0|$z1|$z2|$z3|$z4|$z|2:|$totalList&chtt=Costs: 2008-$curYear|&chdl=Client Paid|Balance Due|Contractor Paid|Live Margin|Est. Margin&chco=FF0000,00FF00,0000FF,800080,FF8040&chxt=x,y,x&chds=$a,$z&chxtc=0,10|1,-980&chxp=1,0,$zb,20,40,60,80,100&chxs=1,000000,7|0,000000,8|2,000000,8&chls=0.5,1,0|0.5,1,0|0.5,1,0|0.5,1,0|0.5,1,0&chm=h,CCBB00,0,$zb2,1&chm=f$z$zMonth,000000,$zSet,$zPos,12|f$a$aMonth,000000,$aSet,$aPos,12";
//$rest="&chxt=x,y&chds=0,".$z."&chxtc=0,10|1,-980&chxs=0,000000,10|1,000000,10,-1,lt,333333&chm=f$z,000000,0,$zPos,15";
?>
<img src="<?=$src?>" width="100%">