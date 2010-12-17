<?
include("cost.linemaker.php");
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


$year=$_GET[year];
$attid=$_GET[attid];
$month=0;

$mainPaidIn=0;
$mainOwed=0;
$mainPaidOut=0;
$mainLiveMargin=0;
$mainMargin=0;

	/*$patrick = 5166; // salary - no overtime
	$zach = 3120 + 540; // 18 hr + overtime
	$danny = 2600; // 15 hr + overtime
	$alex = 2253; // 13 / hr
	
	$salary = $patrick + $zach + $danny + $alex; 
	$salary2 = $salary * .09; // 9% of monthly total
	$health = 2500; // staff health insurance
	$courier = 3000; // better aim high...*/
	//monthly burn rate (not including postage, which is calculated within Service-Web-Service/cost.php
	$burn=45186.24;

while($month < 12){
	$month++;
	$count = $month;
	$month = leading_zeros($month, '2')	;
		
	$csv = getPage("http://data.mdwestserve.com/cost.php?month=$month&year=$year&attid=$attid", 'MDWS GRAPH', '5', '');	
	$value = explode(',',$csv);
	
	$stat[1][$count] = $value[0];
	$mainPaidIn = $mainPaidIn + $value[0];
	
	$stat[2][$count] = $value[1];
	$mainOwed = $mainOwed + $value[1];
	
	$stat[3][$count] = $value[2];
	$mainPaidOut = $mainPaidOut + $value[2];

	$stat[4][$count] = $value[3];
	$mainLiveMargin = $mainLiveMargin + $value[3];

	$stat[5][$count] = $value[4];
	$mainMargin = $mainMargin + $value[4];
	
	if ($value[4] != 0){
		$stat[11][$count] = $value[4] - $burn ;
	}
	}
$year2=$_GET[year]+1;
$month=0;
while($month < 12){
	$month++;
	$count = $month;
	$month = leading_zeros($month, '2')	;

	$csv = getPage("http://data.mdwestserve.com/cost.php?month=$month&year=$year2&attid=$attid", 'MDWS GRAPH', '5', '');		
	$value = explode(',',$csv);
	
	$stat[6][$count] = $value[0];
	$mainPaidIn = $mainPaidIn + $value[0];
	
	$stat[7][$count] = $value[1];
	$mainOwed = $mainOwed + $value[1];
	
	$stat[8][$count] = $value[2];
	$mainPaidOut = $mainPaidOut + $value[2];

	$stat[9][$count] = $value[3];
	$mainLiveMargin = $mainLiveMargin + $value[3];

	$stat[10][$count] = $value[4];
	$mainMargin = $mainMargin + $value[4];

	if ($value[4] != 0){
		$stat[12][$count] = $value[4] - $burn ;
	}
	}




$l = new Line();
$l->SetTitleColor(0, 0, 0);
$l->SetTitle("$year / $year2 $table $id $field");
$l->AddValue("January ".$stat[11][1], array($stat[1][1], $stat[2][1],$stat[3][1], $stat[4][1],$stat[5][1]));
$l->AddValue("Febuary ".$stat[11][2], array($stat[1][2], $stat[2][2],$stat[3][2], $stat[4][2],$stat[5][2]));
$l->AddValue("March ".$stat[11][3], array($stat[1][3], $stat[2][3],$stat[3][3], $stat[4][3],$stat[5][3]));
$l->AddValue("April ".$stat[11][4], array($stat[1][4], $stat[2][4],$stat[3][4], $stat[4][4],$stat[5][4]));
$l->AddValue("May ".$stat[11][5], array($stat[1][5], $stat[2][5],$stat[3][5], $stat[4][5],$stat[5][5]));
$l->AddValue("June ".$stat[11][6], array($stat[1][6], $stat[2][6],$stat[3][6], $stat[4][6],$stat[5][6]));
$l->AddValue("July ".$stat[11][7], array($stat[1][7], $stat[2][7],$stat[3][7], $stat[4][7],$stat[5][7]));
$l->AddValue("August ".$stat[11][8], array($stat[1][8], $stat[2][8],$stat[3][8], $stat[4][8],$stat[5][8]));
$l->AddValue("September ".$stat[11][9], array($stat[1][9], $stat[2][9],$stat[3][9], $stat[4][9],$stat[5][9]));
$l->AddValue("October ".$stat[11][10], array($stat[1][10], $stat[2][10],$stat[3][10], $stat[4][10],$stat[5][10]));
$l->AddValue("November ".$stat[11][11], array($stat[1][11], $stat[2][11],$stat[3][11], $stat[4][11],$stat[5][11]));
$l->AddValue("December ".$stat[11][12], array($stat[1][12], $stat[2][12],$stat[3][12], $stat[4][12],$stat[5][12]));
$l->AddValue("January ".$stat[12][1], array($stat[6][1], $stat[7][1],$stat[8][1], $stat[9][1],$stat[10][1]));
$l->AddValue("Febuary ".$stat[12][2], array($stat[6][2], $stat[7][2],$stat[8][2], $stat[9][2],$stat[10][2]));
$l->AddValue("March ".$stat[12][3], array($stat[6][3], $stat[7][3],$stat[8][3], $stat[9][3],$stat[10][3]));
$l->AddValue("April ".$stat[12][4], array($stat[6][4], $stat[7][4],$stat[8][4], $stat[9][4],$stat[10][4]));
$l->AddValue("May ".$stat[12][5], array($stat[6][5], $stat[7][5],$stat[8][5], $stat[9][5],$stat[10][5]));
$l->AddValue("June ".$stat[12][6], array($stat[6][6], $stat[7][6],$stat[8][6], $stat[9][6],$stat[10][6]));
$l->AddValue("July ".$stat[12][7], array($stat[6][7], $stat[7][7],$stat[8][7], $stat[9][7],$stat[10][7]));
$l->AddValue("August ".$stat[12][8], array($stat[6][8], $stat[7][8],$stat[8][8], $stat[9][8],$stat[10][8]));
$l->AddValue("September ".$stat[12][9], array($stat[6][9], $stat[7][9],$stat[8][9], $stat[9][9],$stat[10][9]));
$l->AddValue("October ".$stat[12][10], array($stat[6][10], $stat[7][10],$stat[8][10], $stat[9][10],$stat[10][10]));
$l->AddValue("November ".$stat[12][11], array($stat[6][11], $stat[7][11],$stat[8][11], $stat[9][11],$stat[10][11]));
$l->AddValue("December ".$stat[12][12], array($stat[6][12], $stat[7][12],$stat[8][12], $stat[9][12],$stat[10][12]));
$l->SetSeriesLabels(Array("Client Paid ".$mainPaidIn, "Balence Due ".$mainOwed,"Contractor Paid ".$mainPaidOut,"Live Margin ".$mainLiveMargin,"Est. Margin ".$mainMargin));

$l->spit("jpg");


?>

