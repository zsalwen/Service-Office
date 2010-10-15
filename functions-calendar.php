<?
// These functions will prevent error dates by removing key input
function mkmonth($keep){
	//if (!$keep){$keep = date('M');}
	$opt = "<option selected value='$keep'>$keep</option>";
	$opt .= "<option value='01'>1</option>";
	$opt .= "<option value='02'>2</option>";
	$opt .= "<option value='03'>3</option>";
	$opt .= "<option value='04'>4</option>";
	$opt .= "<option value='05'>5</option>";
	$opt .= "<option value='06'>6</option>";
	$opt .= "<option value='07'>7</option>";
	$opt .= "<option value='08'>8</option>";
	$opt .= "<option value='09'>9</option>";
	$opt .= "<option value='10'>10</option>";
	$opt .= "<option value='11'>11</option>";
	$opt .= "<option value='12'>12</option>";
	return $opt;
}
function mkday($keep){
	$opt = "<option selected value='$keep'>$keep</option>";
	$opt .= "<option value='01'>1</option>";
	$opt .= "<option value='02'>2</option>";
	$opt .= "<option value='03'>3</option>";
	$opt .= "<option value='04'>4</option>";
	$opt .= "<option value='05'>5</option>";
	$opt .= "<option value='06'>6</option>";
	$opt .= "<option value='07'>7</option>";
	$opt .= "<option value='08'>8</option>";
	$opt .= "<option value='09'>9</option>";
	$opt .= "<option value='10'>10</option>";
	$opt .= "<option value='11'>11</option>";
	$opt .= "<option value='12'>12</option>";
	$opt .= "<option value='13'>13</option>";
	$opt .= "<option value='14'>14</option>";
	$opt .= "<option value='15'>15</option>";
	$opt .= "<option value='16'>16</option>";
	$opt .= "<option value='17'>17</option>";
	$opt .= "<option value='18'>18</option>";
	$opt .= "<option value='19'>19</option>";
	$opt .= "<option value='20'>20</option>";
	$opt .= "<option value='21'>21</option>";
	$opt .= "<option value='22'>22</option>";
	$opt .= "<option value='23'>23</option>";
	$opt .= "<option value='24'>24</option>";
	$opt .= "<option value='25'>25</option>";
	$opt .= "<option value='26'>26</option>";
	$opt .= "<option value='27'>27</option>";
	$opt .= "<option value='28'>28</option>";
	$opt .= "<option value='29'>29</option>";
	$opt .= "<option value='30'>30</option>";
	$opt .= "<option value='31'>31</option>";
	return $opt;
}
function mkyear($keep){
	$opt = "<option selected value='$keep'>$keep</option>";
	$opt .= "<option value='2006'>2006</option>";
	$opt .= "<option value='2007'>2007</option>";
	$opt .= "<option value='2008'>2008</option>";
	$opt .= "<option value='2009'>2009</option>";
	$opt .= "<option value='2010'>2010</option>";
	$opt .= "<option value='2011'>2011</option>";
	return $opt;
}
function fillblock($block, $difference, $month, $year, $user, $end){ // cal1, cal2, cal3
    $day = $block - $difference;
    if ($day < 10){ $day = "0".$day; }
    $date1 = $year."-".$month."-".$day;
    //$end = date('t'); // the end of the month
    if ($day >= "1" && $day <= $end){ // limit low to the first, limit high to the end of the month
    $date2 = $_SESSION[displaydate];
	if ($date1 == $date2){
	$content .= "<div style='background-color:#ffff00; font-weight:bold;text-decoration: underline overline;'>";
	}
	if ($day == date('d') && $month == date('m') && $year == date('Y')){
        $content .= "<a style='color:#990000;text-decoration:underline overline; font-weight:bolder;' href='?date=$year-$month-$day'>$day</a>";
    } else {
        $content .= "<a style='color:#000000;text-decoration:none;' href='?date=$year-$month-$day'>$day</a>";
    }
	//$content .= "</div>";
        return $content;
    } else {
        return ;
    }
}
function fillblock2($block, $difference, $month, $year, $user, $end){ // run1, run2
    $day = $block - $difference;
    if ($day < 10){ $day = "0".$day; }
    $duetoday = $year.$month.$day;
    $duetoday2 = $year."-".$month."-".$day;
    //$end = date('t'); // the end of the month
    if ($day >= "1" && $day <= $end){ // limit low to the first, limit high to the end of the month
		$content = "<a font-weight:bolder;' href='?date=$year-$month-$day'>$day</a><br>";
	$q = "SELECT * FROM schedule_items WHERE sale_date = '$duetoday2' AND item_status='ON SCHEDULE' ORDER BY sort_time";		
	$r = @mysql_query ($q) or die(mysql_error());
	while($d = mysql_fetch_array($r, MYSQL_ASSOC)){
	$content .= "<table width=100% bgcolor='".$_SESSION[$d[court]]."'><tr><td width='66px' style='text-align:left' nowrap>$d[sale_time]</td><td style='text-align:left' nowrap><a target='_blank' href='auctioneer.php?id=$d[schedule_id]'>$d[court]-$d[auctioneer]</a></td></tr></table>";
		}
        return $content;
    } else {
        return ;
    }
}
function fillblock3($block, $difference, $month, $year, $user, $end){ // run1, run2
    $day = $block - $difference;
    if ($day < 10){ $day = "0".$day; }
    $duetoday = $month."-".$day."-".$year;
    //$end = date('t'); // the end of the month
    if ($day >= "1" && $day <= $end){ // limit low to the first, limit high to the end of the month
		$content = "$day<br>";
	$q = "SELECT * FROM reserve WHERE reserve_date = '$duetoday'";		
	$r = @mysql_query ($q) or die(mysql_error());
	while($d = mysql_fetch_array($r, MYSQL_ASSOC)){
	$content .= "<table width=100%><tr><td style='text-align:left' nowrap>$d[auctioneer]</td></tr></table>";
		}
        return $content;
    } else {
        return ;
    }
}
function fillblock4($block, $difference, $month, $year, $user, $end){ // cal1, cal2, cal3
    $day = $block - $difference;
    if ($day < 10){ $day = "0".$day; }
    $date1 = $year."-".$month."-".$day;
    //$end = date('t'); // the end of the month
    if ($day >= "1" && $day <= $end){ // limit low to the first, limit high to the end of the month
    $date2 = $_SESSION[displaydate];
	if ($date1 == $date2){
	$content .= "<div>";
	}
	if ($day == date('d') && $month == date('m') && $year == date('Y')){
        $content .= "<div style='color:#990000;text-decoration:underline overline; font-weight:bolder;' href='?date=$year-$month-$day'>$day</div>";
    } else {
        $content .= "<div style='color:#000000;text-decoration:none;' href='?date=$year-$month-$day'>$day</div>";
    }

	
	//$content .= $date1;
	$content .="<table cellpadding='0' cellspacing='0'>";
	$q = "SELECT * FROM activity_log WHERE user_id='5' AND action_on like '$date1%'";
	$r = @mysql_query($q); 
	$count = mysql_num_rows($r);
	$content .= "<tr><td style='text-align:left'><a href='daily_report.php?id=5&date=$date1'>Roxanne:</a></td><td>$count</td></tr>";
	$q = "SELECT * FROM activity_log WHERE user_id='6' AND action_on like '$date1%'";
	$r = @mysql_query($q); 
	$count = mysql_num_rows($r);
	$content .= "<tr><td style='text-align:left'><a href='daily_report.php?id=6&date=$date1'>Tracy:</a></td><td>$count</td></tr>";
	$q = "SELECT * FROM activity_log WHERE user_id='17' AND action_on like '$date1%'";
	$r = @mysql_query($q); 
	$count = mysql_num_rows($r);
	$content .= "<tr><td style='text-align:left'><a href='daily_report.php?id=17&date=$date1'>Shamata:</a></td><td>$count</td></tr>";
	$q = "SELECT * FROM activity_log WHERE user_id='14' AND action_on like '$date1%'";
	$r = @mysql_query($q); 
	$count = mysql_num_rows($r);
	$content .= "<tr><td style='text-align:left'><a href='daily_report.php?id=14&date=$date1'>Laurie:</a></td><td>$count</td></tr>";
	$content .= "</table></div>";
        return $content;
    } else {
        return ;
    }
}
function fillblackberry($block, $difference, $month, $year, $user, $end){ // cal1, cal2, cal3
    $day = $block - $difference;
    if ($day < 10){ $day = "0".$day; }
    $date1 = $year."-".$month."-".$day;
    //$end = date('t'); // the end of the month
    if ($day >= "1" && $day <= $end){ // limit low to the first, limit high to the end of the month
    $date2 = $_SESSION[displaydate];
	if ($date1 == $date2){
	$content .= "<div style='background-color:#ffff00; font-weight:bold;text-decoration: underline overline;'>";
	}
	
$r1 = mysql_query("SELECT * FROM schedule_items WHERE item_status = 'SALE CANCELLED' and sale_date = '$date1'");
$r2 = mysql_query("SELECT * FROM schedule_items WHERE item_status= 'ON SCHEDULE' and sale_date = '$date1'");
$stat[1] = mysql_num_rows($r1);
$stat[2] = mysql_num_rows($r2);
	
	if ($day == date('d') && $month == date('m') && $year == date('Y')){
        $day5 = date('l, F j, Y',mktime(0,0,0,$month,$day,$year));
		$content .= "<a style='color:#990000;text-decoration:underline overline; font-weight:bolder;' href='?date=$year-$month-$day'>$day5</a> [On: $stat[2] Off: $stat[1]]";
    } else {
        $day5 = date('l, F j, Y',mktime(0,0,0,$month,$day,$year));
        $content .= "<a style='color:#000000;text-decoration:none;' href='?date=$year-$month-$day'>$day5</a> [On: $stat[2] Off: $stat[1]]";
    }
	//$content .= "</div>";
        return $content;
    } else {
        return ;
    }
}

function onlineStaff(){
	$now = time();
	$id = $_COOKIE['userdata']['user_id'];
	@mysql_query("UPDATE users SET online_now='$now' WHERE user_id='$id'");
}
function onlinePortal($id){
	$now = time();
	@mysql_query("UPDATE contacts SET online_now='$now' WHERE contact_id='$id'");
}
function onlineAuctioneer($id){
	$now = time();
	@mysql_query("UPDATE auctioneers SET online_now='$now' WHERE auctioneer_id='$id'");
}



function checkValid($start,$sale,$type){
hardLog('checkValid '.$start.' '.$sale.' '.$type,'user'); 

	$sale = explode('/',$sale);
	if ($sale){
		$sale = date('z',mktime(0, 0, 0, $sale[0], $sale[1],  $sale[2]));
	}
	if ($type=='md1'){
	$pub = explode('/',$start);
	$end = date('z',mktime(0, 0, 0, $pub[0], $pub[1]+14,  $pub[2]));
	$diff = $sale - $end;
	if ($diff > 0 && $diff < 7 ){
		return "Pass";
	}
	return "Fail";
	}
	
	if ($type=='dc1'){
	$pub = explode('/',$start);
	$end = date('z',mktime(0, 0, 0, $pub[0], $pub[1]+6,  $pub[2]));
	$diff = $sale - $end;
	if ($diff == "1"  ){
		return "Pass";
	}
	return "Fail";
	}
	
	if ($type=='dc2'){
	$pub = explode('/',$start);
	$end = date('z',mktime(0, 0, 0, $pub[0], $pub[1]+12,  $pub[2]));
	$diff = $sale - $end;
	if ($diff == "3"  ){
		return "Pass";
	}
	return "Fail";
	}
	
	if ($type=='dc3'){
	$pub = explode('/',$start);
	$end = date('z',mktime(0, 0, 0, $pub[0], $pub[1],  $pub[2]));
	$diff = $sale - $end;
	if ($diff > 0){
		return "Pass";
	}
	return "Fail";
	}
}

function fillblock5($block, $difference, $month, $year, $user, $end){ // cal1, cal2, cal3
    $day = $block - $difference;
    if ($day < 10){ $day = "0".$day; }
    $date1 = $year."-".$month."-".$day;
    //$end = date('t'); // the end of the month
    if ($day >= "1" && $day <= $end){ // limit low to the first, limit high to the end of the month
    $date2 = $_SESSION[displaydate];
	if ($date1 == $date2){
	$content .= "<div>";
	}
	if ($day == date('d') && $month == date('m') && $year == date('Y')){
        $content .= "<div style='color:#990000;text-decoration:underline overline; font-weight:bolder;' href='?date=$year-$month-$day'>$day</div>";
    } else {
        $content .= "<div style='color:#000000;text-decoration:none;' href='?date=$year-$month-$day'>$day</div>";
    }


	$count = mysql_query("SELECT * FROM schedule_items WHERE item_date ='$date1'");
	$count = mysql_num_rows($count);


	$_SESSION[hitz] = $_SESSION[hitz] + $count;



	
	$content .= "<font size='+3'>$count Auctions</font>";
        return $content;
    } else {
        return ;
    }
}



?>