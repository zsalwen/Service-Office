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

echo '<table border="1">';
echo '<tr>';
echo '<td>Service contacts for '.$d[client_file].'</td> <td>Address</td> <td>City</td> <td>State</td> <td>Zip</td> <td>Status</td> <td>Status Date</td> ';
echo '</tr>';

while ($row = @mysql_fetch_array($result,MYSQL_ASSOC)) {


$address = $row['defendantaddress1'].' '.$row['defendantaddress2'];   


echo '<tr>';
echo '<td valign="top">
<table>
<tr>

<td>'.$row['defendantfullname'].'</td></tr></table></td> <td valign="top">
<table>
<tr>
<td>'.$row['defendantaddress1'].' '.$row['defendantaddress2'].'</td></tr></table></td>';
echo '<td valign="top">'.$row['defendantcity'].'</td> <td valign="top">'.$row['defendantstate'].'</td> <td valign="top">'.$row['defendantzip'].'</td>';
echo '<td valign="top">'.$row['status'].'</td> <td valign="top">'.$row['statusdate'].'</td> ';


echo '</tr>';

}

    }
echo "</table>";
?>




<? if (!$_GET[nameCount] && !$_GET[addressCount] ){ ?>
<!-- Step 1 -->
<form method="POST">
<div>Step 1</div>
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