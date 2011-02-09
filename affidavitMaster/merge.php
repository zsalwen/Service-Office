<?
// this is the dual layer merge system
if ($_GET[packet]){ $packet = $_GET[packet]; }else{ die("Missing packet id '?packet='"); }
if ($_GET[template]){ $template = $_GET[template]; }else{ die("Missing template id '&template='"); }
if ($_GET[affidavit]){ $affidavit = $_GET[affidavit]; }else{ die("Missing affidavit id '&affidavit='"); }
mysql_connect();
mysql_select_db('service');


$q = "SELECT * FROM template WHERE id = '$template'";
$r = @mysql_query ($q) or die(mysql_error());
$d = mysql_fetch_array($r, MYSQL_ASSOC);
$base = stripslashes($d[html]); 
if (!$base){ die('Missing template html "$body" '); }
echo "<li>service.template Loaded</li>";

$q = "SELECT * FROM affidavit WHERE id = '$affidavit'";
$r = @mysql_query ($q) or die(mysql_error());
$affidavit = mysql_fetch_array($r, MYSQL_ASSOC);
if (!$affidavit[id]){ die('Missing affidavit table information "$affidavit[id]" '); }
echo "<li>service.affidavit Loaded</li>";


$q = "SELECT * FROM packet WHERE id = '$packet'";
$r = @mysql_query ($q) or die(mysql_error());
$packet = mysql_fetch_array($r, MYSQL_ASSOC);
if (!$packet[id]){ die('Missing packet table data "$packet[id]" '); }
echo "<li>service.packet Loaded</li>";


$q = "SELECT * FROM server WHERE id = '$affidavit[server_id]' "; 
$r = @mysql_query ($q) or die(mysql_error());
$server = mysql_fetch_array($r, MYSQL_ASSOC);
if (!$server[id]){ die('Missing server table data "$server[id]" '); }
echo "<li>service.server Loaded</li>";

// merge the data
$base = str_replace('[ID]', $packet[id], $base); 

// attribute manager (per table)
$r=@mysql_query("select * from attribute where table_name = 'server'");
while($attribute = mysql_fetch_array($r,MYSQL_ASSOC)){
$field = $attribute[field_name];
echo "<li>merge server[$field] (".$server[$field].") into ".$attribute[merge_name]."</li>";
$base = str_replace($attribute[merge_name], $server[$field], $base); //hardcode
}

$r=@mysql_query("select * from attribute where table_name = 'packet'");
while($attribute = mysql_fetch_array($r,MYSQL_ASSOC)){
$field = $attribute[field_name];
echo "<li>merge packet[$field] (".$packet[$field].") into ".$attribute[merge_name]."</li>";
$base = str_replace($attribute[merge_name], $packet[$field], $base); //hardcode
}


// Put the final affidavit
@mysql_query("update affidavit set html =' ".addslashes($base)." ' where id = '$_GET[affidavit]' ");
echo "<li>Affidavit merged and recorded.</li>";
mysql_close();
?>