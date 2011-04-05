<style>
div{ font-size:25px; }
td{ font-size:20px; }
</style>


<div>Wizard style</div>



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
echo '<td valign="top">
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
<? }?>


<td>'.$row['defendantfullname'].'</td></tr></table></td> <td valign="top">
<table>
<tr>
<td style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress1(\''.$row['defendantaddress1'].' '.$row['defendantaddress2'].'\',\''.$row['defendantcity'].'\',\''.$row['defendantstate'].'\',\''.$row['defendantzip'].'\')">1</td>
<td style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress2(\''.$row['defendantaddress1'].' '.$row['defendantaddress2'].'\',\''.$row['defendantcity'].'\',\''.$row['defendantstate'].'\',\''.$row['defendantzip'].'\')">2</td>
<td style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress3(\''.$row['defendantaddress1'].' '.$row['defendantaddress2'].'\',\''.$row['defendantcity'].'\',\''.$row['defendantstate'].'\',\''.$row['defendantzip'].'\')">3</td>
<td style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress4(\''.$row['defendantaddress1'].' '.$row['defendantaddress2'].'\',\''.$row['defendantcity'].'\',\''.$row['defendantstate'].'\',\''.$row['defendantzip'].'\')">4</td>
<td style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress5(\''.$row['defendantaddress1'].' '.$row['defendantaddress2'].'\',\''.$row['defendantcity'].'\',\''.$row['defendantstate'].'\',\''.$row['defendantzip'].'\')">5</td>
<td style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress6(\''.$row['defendantaddress1'].' '.$row['defendantaddress2'].'\',\''.$row['defendantcity'].'\',\''.$row['defendantstate'].'\',\''.$row['defendantzip'].'\')">6</td>
<td>'.$row['defendantaddress1'].'</td></tr></table></td>';
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
<td>Serve: Name</td>
<td>At: Address</td>
</tr>
<? while($i<$rows){ ?>
<tr>
<td><input size="50" name="name<?=$i;?>"></td>
<td><input size="50" address="address<?=$i;?>"></td>
</tr>
<? $i++; }?>
</table>
<input type="submit" value="Next">
</form>
<? } ?>