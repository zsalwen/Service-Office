<?PHP
$month = date('m',mktime(0, 0, 0, date("m"), date("d"),  date("Y")));
$year = date('Y',mktime(0, 0, 0, date("m"), date("d"),  date("Y")));
$difference = date('w',mktime(0,0,0,$month,1,$year));
//---------------( compile array )----------------------------------
$block = array(
                'lastmonth' => date('F',mktime(0,0,0,$month,$day,$year)),
                'month' => date('F',mktime(0,0,0,$month+1,$day,$year)),
                'nextmonth' => date('F',mktime(0,0,0,$month+2,$day,$year)),
                '1' => fillblock('1', $difference, $month, $year,$user,date('t')),
                '2' => fillblock('2', $difference, $month, $year,$user,date('t')),
                '3' => fillblock('3', $difference, $month, $year,$user,date('t')),
                '4' => fillblock('4', $difference, $month, $year,$user,date('t')),
                '5' => fillblock('5', $difference, $month, $year,$user,date('t')),
                '6' => fillblock('6', $difference, $month, $year,$user,date('t')),
                '7' => fillblock('7', $difference, $month, $year,$user,date('t')),
                '8' => fillblock('8', $difference, $month, $year,$user,date('t')),
                '9' => fillblock('9', $difference, $month, $year,$user,date('t')),
                '10' => fillblock('10', $difference, $month, $year,$user,date('t')),
                '11' => fillblock('11', $difference, $month, $year,$user,date('t')),
                '12' => fillblock('12', $difference, $month, $year,$user,date('t')),
                '13' => fillblock('13', $difference, $month, $year,$user,date('t')),
                '14' => fillblock('14', $difference, $month, $year,$user,date('t')),
                '15' => fillblock('15', $difference, $month, $year,$user,date('t')),
                '16' => fillblock('16', $difference, $month, $year,$user,date('t')),
                '17' => fillblock('17', $difference, $month, $year,$user,date('t')),
                '18' => fillblock('18', $difference, $month, $year,$user,date('t')),
                '19' => fillblock('19', $difference, $month, $year,$user,date('t')),
                '20' => fillblock('20', $difference, $month, $year,$user,date('t')),
                '21' => fillblock('21', $difference, $month, $year,$user,date('t')),
                '22' => fillblock('22', $difference, $month, $year,$user,date('t')),
                '23' => fillblock('23', $difference, $month, $year,$user,date('t')),
                '24' => fillblock('24', $difference, $month, $year,$user,date('t')),
                '25' => fillblock('25', $difference, $month, $year,$user,date('t')),
                '26' => fillblock('26', $difference, $month, $year,$user,date('t')),
                '27' => fillblock('27', $difference, $month, $year,$user,date('t')),
                '28' => fillblock('28', $difference, $month, $year,$user,date('t')),
                '29' => fillblock('29', $difference, $month, $year,$user,date('t')),
                '30' => fillblock('30', $difference, $month, $year,$user,date('t')),
                '31' => fillblock('31', $difference, $month, $year,$user,date('t')),
                '32' => fillblock('32', $difference, $month, $year,$user,date('t')),
                '33' => fillblock('33', $difference, $month, $year,$user,date('t')),
                '34' => fillblock('34', $difference, $month, $year,$user,date('t')),
                '35' => fillblock('35', $difference, $month, $year,$user,date('t')),
                '36' => fillblock('36', $difference, $month, $year,$user,date('t')),
                '37' => fillblock('37', $difference, $month, $year,$user,date('t')),
                '38' => fillblock('38', $difference, $month, $year,$user,date('t')),
                '39' => fillblock('39', $difference, $month, $year,$user,date('t')),
                '40' => fillblock('40', $difference, $month, $year,$user,date('t')),
                '41' => fillblock('41', $difference, $month, $year,$user,date('t')),
                '42' => fillblock('42', $difference, $month, $year,$user,date('t')),
                );
//---------------( write html )----------------------------------
?>
<style>
.weekday {background-color:#99CCFF;}
.weekend {background-color:#999999;}
.today {background-color:#FFFFFF;}

</style>
<table width="10px" cellpadding="2" style="border-collapse:collapse" border="1" bgcolor="#FFFFFF">
	<tr>
		<td colspan="7" class="weekend">
				<strong><?=date("F Y");?></strong></div></td>
		</td>
	</tr>
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