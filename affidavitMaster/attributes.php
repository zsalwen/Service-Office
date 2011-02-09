<?
mysql_connect();
mysql_select_db('service');


if($_POST[table_name] && $_POST[field_name] && $_POST[merge_name]){
@mysql_query("insert into attribute (table_name,field_name,merge_name) values ('$_POST[table_name]','$_POST[field_name]','$_POST[merge_name]')");
}


?>

<form method="post">
<table>
<tr>
<td>Add New Attribute</td>
<td>New Value</td>
<td>Example</td>
</tr>
<tr>
<td>table_name</td>
<td><input name="table_name"></td>
<td>server</td>
</tr>
<tr>
<td>field_name</td>
<td><input name="field_name"></td>
<td>name</td>
</tr>
<tr>
<td>merge_name</td>
<td><input name="merge_name"></td>
<td>[SERVERNAME]</td>
</tr>
<tr>
<td colspan="3"><input type="submit" value="Save"></td>
</tr>
</table>