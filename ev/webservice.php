<?
mysql_connect();
mysql_select_db('core');
echo "<style>
td {padding:0px; font-size:14px;}
td.action {width:15px; height:10px; background-color:yellow}
td.action:hover {color:red; background-color:black;}
</style>";
$select_query = "Select create_id, create_date, update_id, update_date, filenumber,clientidentifier,defendantnumber, defendantfullname,defendantaddress1,defendantaddress2,defendantcity,defendantstate,defendantstateid, defendantzip, defendantrelationship,other,status,statusdate From defendants  Where filenumber = '$_GET[fileNumber]'";
$result = mysql_query($select_query);

// Bail out if the query or connection fails
if ($result == false) {
	echo $system_message[104];
	exit;
	}
else {
		 
	echo '<table border="1" style="border-collapse:collapse;" width="900px" >';
	echo '<tr style="padding:0px; margin:0px;">';
	echo '<td style="padding:0px; margin:0px;">Defendant Full Name</td> <td style="padding:0px; margin:0px;">Address 1</td> <td style="padding:0px; margin:0px;">Address 2</td> <td style="padding:0px; margin:0px;">City</td> <td style="padding:0px; margin:0px;">State</td> <td style="padding:0px; margin:0px;">Zip</td> <td style="padding:0px; margin:0px;">Status</td> <td style="padding:0px; margin:0px;">Status Date</td> ';
	echo '</tr>';

	while ($row = @mysql_fetch_array($result,MYSQL_ASSOC)) {

	echo '<tr>';
	echo '<td valign="top" style="padding:0px; margin:0px;"> 
	<table style="padding:0px; margin:0px;">
		<tr>
			<td class="action" style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name1\')"><b>1</b></td>
			<td class="action" style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name2\')"><b>2</b></td>
			<td class="action" style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name3\')"><b>3</b></td>
			<td class="action" style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name4\')"><b>4</b></td>
			<td class="action" style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name5\')"><b>5</b></td>
			<td class="action" style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name6\')"><b>6</b></td>
	<td style="padding:0px; margin:0px;">'.$row['defendantfullname'].'</td></tr></table></td> <td valign="top" style="padding:0px; margin:0px;">
	<table style="padding:0px; margin:0px;">
		<tr>
			<td class="action" style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress1(\''.$row['defendantaddress1'].' '.$row['defendantaddress2'].'\',\''.$row['defendantcity'].'\',\''.$row['defendantstate'].'\',\''.$row['defendantzip'].'\')"><b>1</b></td>
	<td style="padding:0px; margin:0px;">'.$row['defendantaddress1'].'</td></tr></table></td>';
	echo '<td valign="top" style="padding:0px; margin:0px;">'.$row['defendantaddress2'].'</td> <td valign="top">'.$row['defendantcity'].'</td> <td valign="top">'.$row['defendantstate'].'</td>  <td valign="top">'.$row['defendantzip'].'</td>';
	echo '<td valign="top" style="padding:0px; margin:0px;">'.$row['status'].'</td> <td valign="top">'.$row['statusdate'].'</td> ';
		
		
	echo '</tr>';

	}	

    }
echo "</table>";