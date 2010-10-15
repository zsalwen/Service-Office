<?
include 'common.php';

if ($_POST[submit]){
	$r1=@mysql_query("UPDATE ps_affidavits SET method='$_POST[method]' where affidavitID='$_POST[id]'");
	header("location: affidavitUpload.php?packet=$_GET[packet]");
}
$q="SELECT * from ps_affidavits WHERE affidavitID='$_GET[id]'";
$r=@mysql_query($q) or die("Query: $q<br>".mysql_error());
$d=mysql_fetch_array($r, MYSQL_ASSOC);

?>
<form method="post">
<table vspace="100%" style="border:solid 1px; border-color:#FFCCCC" bgcolor="#9999CC" align="center">
	<tr>
    	<td><input name="method" value="<?=$d[method]?>" size="60" /><br /><input type="submit" name="submit" value="Submit" /></td>
    </tr>
</table>
<input type="hidden" name="id" value="<?=$_GET[id]?>" />
<input type="hidden" name="packet" value="<?=$_GET[packet]?>" />
</form>
