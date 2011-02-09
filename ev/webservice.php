<?
mysql_connect();
mysql_select_db('service');
$select_query = "Select create_id, create_date, update_id, update_date, filenumber,clientidentifier,defendantnumber, defendantfullname,defendantaddress1,defendantaddress2,defendantcity,defendantstate,defendantstateid, defendantzip, defendantrelationship,other,status,statusdate From defendants  Where filenumber = '$_GET[fileNumber]'";
$result = mysql_query($select_query);

// Bail out if the query or connection fails
if ($result == false) {
	echo $system_message[104];
	exit;
	}
else {
		 
	echo '<table border="1" style="border-collapse:collapse;" width="200%" >';
	echo '<tr>';
	echo '<td>Defendant Full Name</td> <td>Address 1</td> <td>Address 2</td> <td>City</td> <td>State</td> <td>Zip</td> <td>Status</td> <td>Status Date</td> ';
	echo '</tr>';

	while ($row = @mysql_fetch_array($result,MYSQL_ASSOC)) {

	echo '<tr>';
	echo '<td valign="top"> 
	<table>
		<tr>
			<td style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name1\')">1</td>
			<td style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name2\')">2</td>
			<td style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name3\')">3</td>
			<td style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name4\')">4</td>
			<td style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name5\')">5</td>
			<td style="cursor:pointer; padding:0px; margin:0px;" onclick="ChgText(\''.$row['defendantfullname'].'\',\'name6\')">6</td>
	<td>'.$row['defendantfullname'].'</td></tr></table></td> <td valign="top">
	<table>
		<tr>
			<td style="cursor:pointer; padding:0px; margin:0px;" onclick="setAddress1(\''.$row['defendantaddress1'].' '.$row['defendantaddress2'].'\',\''.$row['defendantcity'].'\',\''.$row['defendantstate'].'\',\''.$row['defendantzip'].'\')">1</td>
	<td>'.$row['defendantaddress1'].'</td></tr></table></td>';
	echo '<td valign="top">'.$row['defendantaddress2'].'</td> <td valign="top">'.$row['defendantcity'].'</td> <td valign="top">'.$row['defendantstate'].'</td>  <td valign="top">'.$row['defendantzip'].'</td>';
	echo '<td valign="top">'.$row['status'].'</td> <td valign="top">'.$row['statusdate'].'</td> ';
		
		
	echo '</tr>';

	}	

    }
echo "</table>";