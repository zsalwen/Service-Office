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
$base = stripslashes($d[html]); 
if (!$base){ die('Missing template html "$body" '); }

// Pull the data we need. (when we are done, pick the joins)
$q = "SELECT * FROM packet WHERE id = '$packet'";
$r = @mysql_query ($q) or die(mysql_error());
$packet = mysql_fetch_array($r, MYSQL_ASSOC);
if (!$packet[id]){ die('Missing packet table data "$packet[id]" '); }

/*
$q = "SELECT * FROM server WHERE id = '' "; // pull server information get id from ????
$r = @mysql_query ($q) or die(mysql_error());
$server = mysql_fetch_array($r, MYSQL_ASSOC);
if (!$server[id]){ die('Missing server table data "$server[id]" '); }
*/

// merge the data
$base = str_replace('[ID]', $packet[id], $base); 

// attribute manager (per table)
$r=@mysql_query("select * from attribute where table_name = 'server'");
while($attribute = mysql_fetch_array($r,MYSQL_ASSOC)){
$base = str_replace($attribute[merge_name]', $server[$attribute[field_name]], $base); //hardcode
}


// Put the final affidavit
@mysql_query("update affidavit set html =' ".addslashes($base)." ' where id = '$affidavit' ");
mysql_close();
?>