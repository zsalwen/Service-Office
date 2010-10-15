<?
mysql_connect();
mysql_select_db('core');
function leading_zeros($value, $places){
    if(is_numeric($value)){
        for($x = 1; $x <= $places; $x++){
            $ceiling = pow(10, $x);
            if($value < $ceiling){
                $zeros = $places - $x;
                for($y = 1; $y <= $zeros; $y++){
                    $leading .= "0";
                }
            $x = $places + 1;
            }
        }
        $output = $leading . $value;
    }
    else{
        $output = $value;
    }
    return $output;
}
function getPage($url, $referer, $timeout, $header){
	if(!isset($timeout))
        $timeout=30;
    $curl = curl_init();
    if(strstr($referer,"://")){
        curl_setopt ($curl, CURLOPT_REFERER, $referer);
    }
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
    curl_setopt ($curl, CURLOPT_HEADER, (int)$header);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $html = curl_exec ($curl);
    curl_close ($curl);
    return $html;
}
function monthConvert($month){
	if ($month == '01'){ return 'Jan'; }
	if ($month == '02'){ return 'Feb'; }
	if ($month == '03'){ return 'Mar'; }
	if ($month == '04'){ return 'Apr'; }
	if ($month == '05'){ return 'May'; }
	if ($month == '06'){ return 'Jun'; }
	if ($month == '07'){ return 'Jul'; }
	if ($month == '08'){ return 'Aug'; }
	if ($month == '09'){ return 'Sep'; }
	if ($month == '10'){ return 'Oct'; }
	if ($month == '11'){ return 'Nov'; }
	if ($month == '12'){ return 'Dec'; }
}
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
while($month < 12){$month++;
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

	$management = ($value[0] + $value[1]) * .10;
	$burn = $salary + $salary2 + $management + $health + $courier;
	
	$total[$count] = $value[4] - $burn ;
	if (!is_numeric($total[$count])){
		$total[$count]=0;
	}
	if($total[$count] > $z){
		$z=$total[$count];
	}
	if($total[$count] < $a){
		$a=$total[$count];
	}
	$labels .= "|".monthConvert($month)."-08";
}
$year2=$year+1;
$month=0;
while($month < 12){$month++;
	$count = $month+12;
	$month = leading_zeros($month, '2')	;

	$csv = getPage("http://data.mdwestserve.com/cost.php?month=$month&year=$year2", 'MDWS GRAPH', '10', '');		
	$value = explode(',',$csv);
	//echo "<fieldset><legend>$month-$year2</legend>$csv</fieldset>";
	if (!is_numeric($value[0])){$value[0]=0;}
	if (!is_numeric($value[1])){$value[1]=0;}
	if (!is_numeric($value[2])){$value[2]=0;}
	if (!is_numeric($value[3])){$value[3]=0;}
	if (!is_numeric($value[4])){$value[4]=0;}

	$management = ($value[0] + $value[1]) * .10;
	$burn = $salary + $salary2 + $management + $health + $courier;
	
	$total[$count] = $value[4] - $burn ;
	if (!is_numeric($total[$count])){
		$total[$count]=0;
	}
	if($total[$count] > $z){
		$z=$total[$count];
	}
	if($total[$count] < $a){
		$a=$total[$count];
	}
	$labels .= "|".monthConvert($month)."-09";
}
$total=implode(',',$total);
$total2=implode(',',$total2);
/*echo "<table border='1' style='border-collapse:collapse;'><tr>";
echo "<td></td>".str_replace('|','</td><td>',$labels).'</td></tr>';
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
$src="http://chart.apis.google.com/chart?cht=lc&chs=900x333&chd=t:".$total."&chxl=0:".$labels."|1:|$a|0|$z1|$z2|$z3|$z4|$z|2:&chtt=Profit/Loss Margin 2008-2009&chxt=x,y&chds=$a,$z&chxtc=0,-300|1,-980&chxp=1,0,$zb,20,40,60,80,100&chxs=1,000000,8|0,000000,8&chls=1,1,0&chm=h,FF0000,0,$zb2,0.5";
//$rest="&chxt=x,y&chds=0,".$z."&chxtc=0,10|1,-980&chxs=0,000000,10|1,000000,10,-1,lt,333333&chls=1,1,0|1,1,0|1,1,0|1,1,0|1,1,0";
?>
<img src="<?=$src?>" width="100%">