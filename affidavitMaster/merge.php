<?
// this is the dual layer merge system
if ($_GET[packet]){ $packet = $_GET[packet]; }else{ die("Missing packet id '?packet='"); }
if ($_GET[template]){ $template = $_GET[template]; }else{ die("Missing template id '&template='"); }
if ($_GET[affidavit]){ $affidavit = $_GET[affidavit]; }else{ die("Missing affidavit id '&affidavit='"); }
mysql_connect();
mysql_select_db('service');
// Pull the template
$q = "SELECT * FROM template WHERE id = '$template'";
$r = @mysql_query ($q) or die(mysql_error());
$d = mysql_fetch_array($r, MYSQL_ASSOC);
$base = stripslashes($d[html]); // we are done with $d, good to reuse
if (!$base){ die('Missing template html "$body" '); }
// Pull the data we need. (this is where we join!)
$q = "SELECT * FROM packet WHERE id = '$template'";
$r = @mysql_query ($q) or die(mysql_error());
$d = mysql_fetch_array($r, MYSQL_ASSOC);
if (!$d[id]){ die('Missing packet data "$d[id]" '); }
// merge the data
$base = str_replace('[ID]', $d[id], $base);







// Put the final affidavit
@mysql_query("update affidavit set html =' ".addslashes($base)." ' where id = '$affidavit' ");
mysql_close();
?>