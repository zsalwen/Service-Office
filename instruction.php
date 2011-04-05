<style>
div{ font-size:25px; }
td{ font-size:20px; }
</style>


<div>Wizard style</div>
<? if ($_GET[nameCount] && $_GET[addressCount] ){  ?>
<script>
function ChgText(myResponse,myInput)
{
var MyElement = document.getElementById(myInput);
MyElement.value = myResponse;
return true;
}
<?
if ($_GET[nameCount] && $_GET[addressCount] ){ 
$rows = $_GET[nameCount]*$_GET[addressCount] ;
$i=0;
}
?>
<? while($i<$rows){ ?>
function setAddress<?=$i;?>(street,city,state,zip)
{
alert('test<?=$i;?>');
ChgText(street,'address<?=$i;?>');
ChgText(city,'city<?=$i;?>');
ChgText(state,'state<?=$i;?>');
ChgText(zip,'zip<?=$i;?>');
}
<? $i++;} ?>
</script>
<? } ?>


<?
mysql_connect();
mysql_select_db('core');

$r=@mysql_query("select client_file from packet where id = '$_GET[packet]' ");
$d=mysql_fetch_array($r,MYSQL_ASSOC);

$select_query = "Select create_id, create_date, update_id, update_date, filenumber,clientidentifier,defendantnumber, defendantfullname,defendantaddress1,defendantaddress2,defendantcity,defendantstate,defendantstateid, defendantzip, defendantrelationship,other,status,statusdate From defendants Where filenumber = '$d[client_file]'";
$result = mysql_query($select_query);

// Bail out if the query or connection fails
if ($result == false) {
echo $system_message[104];
exit;
}
else {

echo '<table border="1" style="border-collapse:collapse; padding:0px; font-size:12px;" width="100%" >';
echo '<tr>';
echo '<td>Defendant Name</td> <td>Address1</td> <td>Address2</td> <td>City</td> <td>State</td> <td>Zip</td> <td>Status</td> <td>Date</td> ';
echo '</tr>';

while ($row = @mysql_fetch_array($result,MYSQL_ASSOC)) {

echo '<tr>';
echo '<td valign="top">';
?>
<table>
<?
if ($_GET[nameCount] && $_GET[addressCount] ){ 
$rows = $_GET[nameCount]*$_GET[addressCount] ;
$i=0;
}
?>
<tr>
<? while($i<$rows){ ?>
<td style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText('<?=$row['defendantfullname'];?>','name<?=$i;?>')"><?=$i;?></td>
<? $i++;}?>

<td><?=$row['defendantfullname'];?></td></tr></table></td> <td valign="top">
<table>
<tr>
<?
if ($_GET[nameCount] && $_GET[addressCount] ){ 
$rows = $_GET[nameCount]*$_GET[addressCount] ;
$i=0;
}
?>

<? while($i<$rows){ ?>

<td style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress<?=$i;?>('<?=$row['defendantaddress1'];?> <?=$row['defendantaddress2'];?>','<?=$row['defendantcity'];?>','<?=$row['defendantstate'];?>','<?=$row['defendantzip'];?>')"><?=$i;?></td>

<? $i++;}?>


<?
echo '<td>'.$row['defendantaddress1'].'</td></tr></table></td>';
echo '<td valign="top">'.$row['defendantaddress2'].'</td> <td valign="top">'.$row['defendantcity'].'</td> <td valign="top">'.$row['defendantstate'].'</td> <td valign="top">'.$row['defendantzip'].'</td>';
echo '<td valign="top">'.$row['status'].'</td> <td valign="top">'.$row['statusdate'].'</td> ';


echo '</tr>';

}

    }
echo "</table>";
?>




<? if (!$_GET[nameCount] && !$_GET[addressCount] ){ ?>
<!-- Step 1 -->
<form method="GET">
<input type="hidden" name="packet" value="<?=$_GET[packet]?>">
<div>Step 1 - Set Matrix Size</div>
<table>
<tr>
<td>How many names would you like to enter?</td>
<td><input name="nameCount"></td>
</tr><tr>
<td>How many addresses would you like to enter?</td>
<td><input name="addressCount"></td>
</tr>
</table>
<input type="submit" value="Next">
</form>
<? } ?>



<? if ($_GET[nameCount] && $_GET[addressCount] ){ 
$rows = $_GET[nameCount]*$_GET[addressCount] ;
$i=0;
?>
<!-- Step 2 -->
<div>Step 2 - Fill Instruction Matrix</div>
<form method="POST">
<table>
<tr>
<td>Position</td>
<td>Serve: Name</td>
<td>At: Address</td>
</tr>
<? while($i<$rows){ ?>
<tr>
<td><?=$i;?></td>
<td><input size="50" id="name<?=$i;?>"  name="name<?=$i;?>" ></td>
<td><input size="50" id="address<?=$i;?>" name="address<?=$i;?>"><input size="50" id="city<?=$i;?>" name="city<?=$i;?>"><input size="50" id="state<?=$i;?>" name="state<?=$i;?>"><input size="50" id="zip<?=$i;?>" name="zip<?=$i;?>"></td>
</tr>
<? $i++; }?>
</table>
<input type="submit" value="Next">
</form>
<? } ?>