<style>
div{ font-size:25px; }
td{ font-size:20px; }
</style>


<div><b>Instructions for Packet</b></div>
<? if (($_GET[nameCount] && $_GET[addressCount]) || $_GET[totalCount] ){  ?>
<script>
function ChgText(myResponse,myInput)
{
var MyElement = document.getElementById(myInput);
MyElement.value = myResponse;
return true;
}
<?
if (($_GET[nameCount] && $_GET[addressCount]) || $_GET[totalCount] ){ 
if($_GET[totalCount]){
$rows=$_GET[totalCount];
}else{
$rows = $_GET[nameCount]*$_GET[addressCount] ;
}
$i=0;
}
?>
<? while($i<$rows){ ?>
function setAddress<?=$i;?>(street,city,state,zip)
{
ChgText(street,'address<?=$i;?>');
ChgText(city,'city<?=$i;?>');
ChgText(state,'state<?=$i;?>');
ChgText(zip,'zip<?=$i;?>');
}
function copyDown<?=$i;?>()
{
var street = document.getElementById('address<?=$i;?>').value;
var city = document.getElementById('city<?=$i;?>').value;
var state = document.getElementById('state<?=$i;?>').value;
var zip = document.getElementById('zip<?=$i;?>').value;
ChgText(street,'address<?=$i+1;?>');
ChgText(city,'city<?=$i+1;?>');
ChgText(state,'state<?=$i+1;?>');
ChgText(zip,'zip<?=$i+1;?>');
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
if (($_GET[nameCount] && $_GET[addressCount]) || $_GET[totalCount] ){ 
if($_GET[totalCount]){
$rows=$_GET[totalCount];
}else{
$rows = $_GET[nameCount]*$_GET[addressCount] ;
}
$i=0;
}
?>
<tr><td>
<? while($i<$rows){ ?>
<b style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText('<?=$row['defendantfullname'];?>','name<?=$i;?>')">#<?=$i;?></b>
<? $i++;}?>

<td><?=$row['defendantfullname'];?></td></tr></table></td> <td valign="top">
<table>
<tr><td>
<?
if (($_GET[nameCount] && $_GET[addressCount]) || $_GET[totalCount] ){ 
if($_GET[totalCount]){
$rows=$_GET[totalCount];
}else{
$rows = $_GET[nameCount]*$_GET[addressCount] ;
}
$i=0;
}
?>

<? while($i<$rows){ ?>

<b style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress<?=$i;?>('<?=$row['defendantaddress1'];?> <?=$row['defendantaddress2'];?>','<?=$row['defendantcity'];?>','<?=$row['defendantstate'];?>','<?=$row['defendantzip'];?>')">#<?=$i;?></b>

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




<? if (!$_GET[nameCount] && !$_GET[addressCount] && !$_GET[totalCount] ){ ?>
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
</tr><tr>
<td>-OR-</td>
<td></td>
</tr><tr>
<td>How many instructions would you like to enter?</td>
<td><input name="totalCount"></td>
</tr>
</table>
<input type="submit" value="Next">
</form>
<? } ?>



<? if (($_GET[nameCount] && $_GET[addressCount]) || $_GET[totalCount] ){ 
if($_GET[totalCount]){
$rows=$_GET[totalCount];
}else{
$rows = $_GET[nameCount]*$_GET[addressCount] ;
}
$i=0;
?>
<!-- Step 2 -->
<div>Step 2 - Fill Instruction Matrix</div>
<form method="POST">
<table>
<tr>
<td>Position</td>
<td>Copy</td>
<td>Serve: Name</td>
<td>At: Address</td>
<td>Copy</td>
</tr>
<? while($i<$rows){ ?>
<tr>
<td><b>#<?=$i;?></b></td>
<td>&darr;</td>
<td><input size="50" id="name<?=$i;?>"  name="name<?=$i;?>" ></td>
<td><input size="25" id="address<?=$i;?>" name="address<?=$i;?>"><input size="10" id="city<?=$i;?>" name="city<?=$i;?>"><input size="3" id="state<?=$i;?>" name="state<?=$i;?>"><input size="5" id="zip<?=$i;?>" name="zip<?=$i;?>"></td>
<td><b onclick="copyDown<?=$i;?>()">&darr;</b></td>
</tr>
<? $i++; }?>
</table>
<input type="submit" value="Next">
</form>
<? } ?>







<fieldset>
<legend>Current Instructions</legend>
<?
$rSSA=@mysql_query("select * from instruction where packet_id = '$packet'");
while($dSSA=mysql_fetch_array($rSSA,MYSQL_ASSOC)){
if ($dSSA[server_id]){ 
echo "<li><input type='checkbox'>".serverID($dSSA[server_id])." on ".nameID($dSSA[name_id])." at ".addressID($dSSA[address_id])."</li>";
}else{
echo "<li><input type='checkbox'>Awaiting Dispatch on ".nameID($dSSA[name_id])." at ".addressID($dSSA[address_id])."</li>";
}
}
?>
</fieldset>