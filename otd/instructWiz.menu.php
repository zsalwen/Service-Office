<style>
.btn { 
	color:#000; 
	font-family: arial; 
	font-size:11px;
	background-color:#ccffff; 
	border: 1px solid; 
	border-color: #696 #363 #363 #696;  
	} 
select, option { 
	color:blue;
	font-size:10px;
	font-family:arial;
	background-color:#CCFFFF;
	border: 1px solid; 
	}
</style>
<table valign='middle' style='border:1px solid; border-collapse:collapse;' bgcolor='#CCCCFF' border='1' height='150px' width='100%'>
	<tr valign="middle">
		<td valign="middle">
		<form method='post' name='form1'>
			<input type='hidden' name='packet' value='<?=$packet?>'>
			<input type='hidden' name='i' value='2' />
			<select name='def' onchange='this.form.submit();' <? if (isset($_POST[def]) && $_POST[def] != 'ALL' && $_POST[i] != 3){ echo "style='background-color:red;'";}?>><? if (!$_POST[def] || $_POST[def] == 'ALL'){ echo "<option value=''>EDIT SINGLE DEFENDANT</option>";}?><? if ($_POST[i] == 3){ echo defList($packet,'');}else{ echo defList($packet,$def);}?></select>
		</form>
		</td>
	</tr>
	<tr valign="middle">
		<td valign="middle">
		<form method='post' name='form2'>
			<input type='hidden' name='packet' value='<?=$packet?>'>
			<input type='hidden' name='i' value='2' />
			<input type='hidden' name='def' value='ALL' />
			<input type='button' class='btn' name='edit' onclick='this.form.submit();' value='Edit All Defendants'>
		</form>
		</td>
	</tr>
	<tr valign="middle">
		<td valign="middle">
		<form method='post' name='form3'>
			<input type='hidden' name='packet' value='<?=$packet?>'>
			<input type='hidden' name='i' value='4' />
			<input type='button' class='btn' name='settings' onclick='this.form.submit();' value='Edit File Settings'>
		</form>
		</td>
	</tr>
</table>