<?
include("linemaker.php");
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

function benchmark($a,$b){
	if ($b != "0000-00-00" ){
		$received=strtotime($a);
		$deadline=strtotime($b.' 12:00:00');
		$days=number_format(($deadline-$received)/86400,0);
		$_SESSION[items]=$_SESSION[items] + 1;
		$_SESSION[total]=$_SESSION[total] + $days;
	//return "$days days";
	return $days;
	}
}
function returnBenchmark($id){
	$r=@mysql_query("SELECT * from ps_packets where packet_id = '$id'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if ($d[fileDate] != "0000-00-00"){ $end = $d[fileDate]; } else { $end =$d[estFileDate] ;} 
	if ($d[reopenDate] != "0000-00-00"){ $start = $d[reopenDate].' 12
}

:00:00'; } else { $start =$d[date_received] ;}
	return benchmark($start,$end);
}


if ($_GET[type] != 'intake'){
	$field = "fileDate";
}else{
	$field = "date_received";
}
if ($_GET[src] == 'eviction'){
	$table = "evictionPackets";
	$id = "eviction_id";
}elseif($_GET[src] == 'debug'){
	$table = "ps_export";
	$id = "packet_id";
}else{
	$table = "ps_packets";
	$id = "packet_id";
}
$year=$_GET[year];
$month=0;

$mainBurson=0;
$mainWhite=0;
$mainDraper=0;
$mainOthers=0;



while($month < 12){
	$month++;
	$count = $month;
	$month = leading_zeros($month, '2')	;
		
	$r=@mysql_query("select $id from $table where $field like '$year-$month-%' and attorneys_id = '1'");
	$counter=mysql_num_rows($r);
	$stat[1][$count] = $counter;
	$mainBurson = $mainBurson + $counter;
	
	$r=@mysql_query("select $id from $table where $field like '$year-$month-%' and attorneys_id = '3'");
	$counter=mysql_num_rows($r);
	$stat[2][$count] = $counter;
	$mainWhite = $mainWhite + $counter;
	
	$r=@mysql_query("select $id from $table where $field like '$year-$month-%' and attorneys_id = '21'");
	$counter=mysql_num_rows($r);
	$stat[3][$count] = $counter;
	$mainDraper = $mainDraper + $counter;

	$r=@mysql_query("select $id from $table where $field like '$year-$month-%' and attorneys_id <> '1' and attorneys_id <> '3' and attorneys_id <> '21'");
	$counter=mysql_num_rows($r);
	$stat[4][$count] = $counter;
	$mainOthers = $mainOthers + $counter;

	
	$stat[5][$count] = $stat[4][$count]+$stat[3][$count]+$stat[2][$count]+$stat[1][$count];





	}
$year2=$_GET[year]+1;
$month=0;
while($month < 12){
	$month++;
	$count = $month;
	$month = leading_zeros($month, '2')	;
		
	$r=@mysql_query("select $id from $table where $field like '$year2-$month-%' and attorneys_id = '1'");
	$counter=mysql_num_rows($r);
	$stat[6][$count] = $counter;
	$mainBurson = $mainBurson + $counter;

	$r=@mysql_query("select $id from $table where $field like '$year2-$month-%' and attorneys_id = '3'");
	$counter=mysql_num_rows($r);
	$stat[7][$count] = $counter;
	$mainWhite = $mainWhite + $counter;

	$r=@mysql_query("select $id from $table where $field like '$year2-$month-%' and attorneys_id = '21'");
	$counter=mysql_num_rows($r);
	$stat[8][$count] = $counter;
	$mainDraper = $mainDraper + $counter;

	$r=@mysql_query("select $id from $table where $field like '$year2-$month-%' and attorneys_id <> '1' and attorneys_id <> '3' and attorneys_id <> '21'");
	$counter=mysql_num_rows($r);
	$stat[9][$count] = $counter;
	$mainOthers = $mainOthers + $counter;

	$stat[10][$count] = $stat[9][$count]+$stat[8][$count]+$stat[7][$count]+$stat[6][$count];





	}




$l = new Line();
$l->SetTitleColor(0, 0, 0);
$l->SetTitle("$year / $year2 $table $id $field");
$l->AddValue("January ".$stat[5][1], array($stat[1][1], $stat[2][1],$stat[3][1], $stat[4][1]));
$l->AddValue("Febuary ".$stat[5][2], array($stat[1][2], $stat[2][2],$stat[3][2], $stat[4][2]));
$l->AddValue("March ".$stat[5][3], array($stat[1][3], $stat[2][3],$stat[3][3], $stat[4][3]));
$l->AddValue("April ".$stat[5][4], array($stat[1][4], $stat[2][4],$stat[3][4], $stat[4][4]));
$l->AddValue("May ".$stat[5][5], array($stat[1][5], $stat[2][5],$stat[3][5], $stat[4][5]));
$l->AddValue("June ".$stat[5][6], array($stat[1][6], $stat[2][6],$stat[3][6], $stat[4][6]));
$l->AddValue("July ".$stat[5][7], array($stat[1][7], $stat[2][7],$stat[3][7], $stat[4][7]));
$l->AddValue("August ".$stat[5][8], array($stat[1][8], $stat[2][8],$stat[3][8], $stat[4][8]));
$l->AddValue("September ".$stat[5][9], array($stat[1][9], $stat[2][9],$stat[3][9], $stat[4][9]));
$l->AddValue("October ".$stat[5][10], array($stat[1][10], $stat[2][10],$stat[3][10], $stat[4][10]));
$l->AddValue("November ".$stat[5][11], array($stat[1][11], $stat[2][11],$stat[3][11], $stat[4][11]));
$l->AddValue("December ".$stat[5][12], array($stat[1][12], $stat[2][12],$stat[3][12], $stat[4][12]));
$l->AddValue("January ".$stat[10][1], array($stat[6][1], $stat[7][1],$stat[8][1], $stat[9][1]));
$l->AddValue("Febuary ".$stat[10][2], array($stat[6][2], $stat[7][2],$stat[8][2], $stat[9][2]));
$l->AddValue("March ".$stat[10][3], array($stat[6][3], $stat[7][3],$stat[8][3], $stat[9][3]));
$l->AddValue("April ".$stat[10][4], array($stat[6][4], $stat[7][4],$stat[8][4], $stat[9][4]));
$l->AddValue("May ".$stat[10][5], array($stat[6][5], $stat[7][5],$stat[8][5], $stat[9][5]));
$l->AddValue("June ".$stat[10][6], array($stat[6][6], $stat[7][6],$stat[8][6], $stat[9][6]));
$l->AddValue("July ".$stat[10][7], array($stat[6][7], $stat[7][7],$stat[8][7], $stat[9][7]));
$l->AddValue("August ".$stat[10][8], array($stat[6][8], $stat[7][8],$stat[8][8], $stat[9][8]));
$l->AddValue("September ".$stat[10][9], array($stat[6][9], $stat[7][9],$stat[8][9], $stat[9][9]));
$l->AddValue("October ".$stat[10][10], array($stat[6][10], $stat[7][10],$stat[8][10], $stat[9][10]));
$l->AddValue("November ".$stat[10][11], array($stat[6][11], $stat[7][11],$stat[8][11], $stat[9][11]));
$l->AddValue("December ".$stat[10][12], array($stat[6][12], $stat[7][12],$stat[8][12], $stat[9][12]));
$l->SetSeriesLabels(Array("Burson ".$mainBurson, "White ".$mainWhite,"Draper ".$mainDraper,"Others ".$mainOthers));

$l->spit("jpg");


?>

