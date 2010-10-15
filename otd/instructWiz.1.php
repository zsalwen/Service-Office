<style>
a {text-decoration:none; color:red;}
</style>
<table align="center">
	<tr>
		<td>
			<form method="post" name="form1">
			<input type="hidden" name="packet" value="<?=$packet?>">
			<input type="hidden" name="i" value="2" />
			<select name="def" onchange="this.form.submit();"><option value="">SELECT DEFENDANT</option><option value="ALL">ALL DEFENDANTS</option><?=defList($packet,$def)?></select>
			</form>
		</td>
	</tr>
	<tr>
		<td align="center">OR</td>
	</tr>
	<tr>
		<td align="center">
			<form method="post" name="form2">
			<input type="hidden" name="packet" value="<?=$packet?>">
			<input type="hidden" name="i" value="4" />
			<input type="button" onclick="this.form.submit();" value="Edit File Settings">
			</form>
		</td>
	</tr>
</table>