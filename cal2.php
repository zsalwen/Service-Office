<?PHP
if ($_POST[cal2month] && $_POST[cal2year]){
	$_SESSION[cal2month] = $_POST[cal2month];
	$_SESSION[cal2year] = $_POST[cal2year];
}
$month = $_SESSION[cal2month];
$year = $_SESSION[cal2year];
$difference = date('w',mktime(0,0,0,$month,1,$year));

//---------------( compile array )----------------------------------
$block = array(
                'lastmonth' => date('F',mktime(0,0,0,$month,$day,$year)),
                'month' => date('F',mktime(0,0,0,$month+1,$day,$year)),
                'nextmonth' => date('F',mktime(0,0,0,$month+2,$day,$year)),
                '1' => fillblock('1', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '2' => fillblock('2', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '3' => fillblock('3', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '4' => fillblock('4', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '5' => fillblock('5', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '6' => fillblock('6', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '7' => fillblock('7', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '8' => fillblock('8', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '9' => fillblock('9', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '10' => fillblock('10', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '11' => fillblock('11', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '12' => fillblock('12', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '13' => fillblock('13', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '14' => fillblock('14', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '15' => fillblock('15', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '16' => fillblock('16', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '17' => fillblock('17', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '18' => fillblock('18', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '19' => fillblock('19', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '20' => fillblock('20', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '21' => fillblock('21', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '22' => fillblock('22', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '23' => fillblock('23', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '24' => fillblock('24', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '25' => fillblock('25', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '26' => fillblock('26', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '27' => fillblock('27', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '28' => fillblock('28', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '29' => fillblock('29', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '30' => fillblock('30', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '31' => fillblock('31', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '32' => fillblock('32', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '33' => fillblock('33', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '34' => fillblock('34', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '35' => fillblock('35', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '36' => fillblock('36', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '37' => fillblock('37', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '38' => fillblock('38', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '39' => fillblock('39', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '40' => fillblock('40', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '41' => fillblock('41', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                '42' => fillblock('42', $difference, $month, $year,$user,date('t',mktime(0,0,0,$month,$day,$year))),
                );
//---------------( write html )----------------------------------



//---------------------------------------------------------------
?>
<style>
.weekday {background-color:#99CCFF;}
.weekend {background-color:#999999;}
.today {background-color:#FFFFFF;}
</style>
<table width="10px" cellpadding="2" style="border-collapse:collapse" border="1" bgcolor="#FFFFFF"><form method="post">
	<tr>
		<td colspan="7" class="weekend">
        <strong><? echo date('F Y',mktime(0, 0, 0, $_SESSION[cal2month], date("d"),  $_SESSION[cal2year]));?></strong><br />
		<select name="cal2month"><?=mkmonth($_SESSION[cal2month]);?></select><select name="cal2year"><?=mkyear($_SESSION[cal2year]);?></select><input type="submit" value="GO"/>
		
		
		</td>
	</tr></form>
    <tr>
        <td align="center" bgcolor="#CC9900">Su</td>
        <td align="center" bgcolor="#FFCC00">Mo</td>
        <td align="center" bgcolor="#FFCC00">Tu</td>
        <td align="center" bgcolor="#FFCC00">We</td>
        <td align="center" bgcolor="#FFCC00">Th</td>
        <td align="center" bgcolor="#FFCC00">Fr</td>
        <td align="center" bgcolor="#CC9900">Sa</td>
    </tr>
    <tr>
        <td valign="top" class="weekend"><?PHP echo $block['1'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['2'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['3'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['4'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['5'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['6'];?></td>
        <td valign="top" class="weekend"><?PHP echo $block['7'];?></td>
    </tr>
    <tr>
        <td valign="top" class="weekend"><?PHP echo $block['8'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['9'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['10'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['11'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['12'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['13'];?></td>
        <td valign="top" class="weekend"><?PHP echo $block['14'];?></td>
    </tr>
    <tr>
        <td valign="top" class="weekend"><?PHP echo $block['15'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['16'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['17'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['18'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['19'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['20'];?></td>
        <td valign="top" class="weekend"><?PHP echo $block['21'];?></td>
    </tr>
    <tr>
        <td valign="top" class="weekend"><?PHP echo $block['22'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['23'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['24'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['25'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['26'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['27'];?></td>
        <td valign="top" class="weekend"><?PHP echo $block['28'];?></td>
    </tr>
    <tr>
        <td valign="top" class="weekend"><?PHP echo $block['29'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['30'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['31'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['32'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['33'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['34'];?></td>
        <td valign="top" class="weekend"><?PHP echo $block['35'];?></td>
    </tr>
<?PHP
//--------( if a day exists in block 36 to 42 we must display this row )-----------------
if ($block['36']){
?>
    <tr>
        <td valign="top" class="weekend"><?PHP echo $block['36'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['37'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['38'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['39'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['40'];?></td>
        <td valign="top" class="weekday"><?PHP echo $block['41'];?></td>
        <td valign="top" class="weekend"><?PHP echo $block['42'];?></td>
    </tr>
<?PHP } ?>
</table>