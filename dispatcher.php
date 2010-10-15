<?
include 'common.php';
include 'lock.php';
require_once('zipcode.class.php');      // zip code class
?>
<style>
body { padding:0px; margin:0px; }


</style>
<table width="100%" height="60%" cellspacing="0"><tr bgcolor="#66FF66"><td valign="top" width="33%" style="border-right:solid 1px;">
<form>
<div style="border:double 4px #999999; background-color:#FFFF99; width:500; height:160px; font-size:13px; padding:4px; text-align:justify">
<strong>Welcome to the Network Dispatcher</strong><br>
We are going to show the world how it will be done. Think of this as a smart phonebook. The first this you will do is enter the address you wish to serve. It is best to start with 5 miles. When you start the search our first safeguard is a check with the USPS database, here we get the current zip code. Now we pull all zip codes in a 5 mile radus. Scan thousands of cases for prior local service. Finally below you see the Recommended MDWestServe Network Servers. Click on each server to view quick details. When dealing out of state dont hesitate to look at 100/200 miles out!</div>     
 
<table>
	<tr>
    	<td>Apartment or Suite</td>
        <td><input name="aptsut" value="<?=strtoupper($_GET[aptste])?>"></td>
    </tr>
    <tr>
    	<td>Street Address</td>
        <td><input name="address" size="60" value="<?=strtoupper($_GET[address])?>"></td>
    </tr>
    <tr>
    	<td>City, State</td>
        <td><input name="city" value="<?=strtoupper($_GET[city])?>">, <input name="state" size="3" value="<?=strtoupper($_GET[state])?>"></td>
    </tr>
    <tr>
    	<td><select name="miles">
        	<option selected value="5">5 Miles</option>
            <option value="10">10 Miles</option>
            <option value="25">25 Miles</option>
            <option value="50">50 Miles</option>
            <option value="75">75 Miles</option>
            <option value="100">100 Miles</option>
            <option value="125">125 Miles</option>
            <option value="150">150 Miles</option>
            <option value="175">175 Miles</option>
            <option value="200">200 Miles</option>
            <option value="225">225 Miles</option>
            <option value="250">250 Miles</option>
            <option value="275">275 Miles</option>
            </select></td><td><input type="submit" value="Search the MDWestServe Network"></td></tr>
</table>
       
</form>   
</td><td width="33%" align="center">
<?
if ($_GET[address]&&$_GET[city]&&$_GET[state]){        
	include 'usps.php';
	$targetZip = $_SESSION[dropZIP];
}
?><br><iframe width="300" height="210" name="peek" frameborder="0" id="peek"></iframe></td><td style="border-left:solid 1px; padding:2px;" valign="top" width="33%"><div style="overflow:auto; height:300px;">
<? if($targetZip){
$z = new zipcode_class;
 
$zips = $z->get_zips_in_range($targetZip, $_GET[miles], _ZIPS_SORT_BY_DISTANCE_ASC, true); 
		echo "<center><strong>All ZIP codes in a $_GET[miles] mile radius of $targetZip</strong></center><ol>";
   foreach ($zips as $key => $value) {
      echo "<li>Zip code <b>$key</b> is <b>$value</b> miles away.</li>";
   }


 }?></ol></div>
</td>


</tr>
</table>

<table border="1" width="100%" height="30%">
<? 


function testZIP($test){
	  $r=@mysql_query("select packet_id from ps_packets where zip1 = '$test' or zip1a = '$test' or zip1b = '$test' or zip1c = '$test' or zip1d = '$test' or zip1e = '$test'");
	  $d=mysql_fetch_array($r,MYSQL_ASSOC);
	  if ($d[packet_id]){
	  return "1";
	  }
}

$i1-0;
if ($zips){
   foreach ($zips as $key => $value) {
      if (testZIP($key)){$i1++;
	  echo "<td align='center' valign='top' bgcolor='".row_color($i1,'FFFF99','FFFFFF')."'><center><b> $value MILES<br>$key</b></center>";
	  // find all matching pervious serves in this zipcode
	  $r=@mysql_query("select distinct server_id, zip1 from ps_packets where server_id <> '' and zip1 = '$key'");
	  while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	   echo "<a target='peek' href='dispatcher.peek.php?server=$d[server_id]&zip=$key'>".id2name($d[server_id])."</a><br>";
	  }
	  $r=@mysql_query("select distinct server_ida, zip1a from ps_packets where server_ida <> '' and zip1a = '$key'");
	  while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	   echo "<a target='peek' href='dispatcher.peek.php?server=$d[server_ida]&zip=$key'>".id2name($d[server_ida])."</a><br>";
	  }
	  $r=@mysql_query("select distinct server_idb, zip1b from ps_packets where server_idb <> '' and zip1b = '$key'");
	  while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	   echo "<a target='peek' href='dispatcher.peek.php?server=$d[server_idb]&zip=$key'>".id2name($d[server_idb])."</a></br>";
	  }
	  $r=@mysql_query("select distinct server_idc, zip1c from ps_packets where server_idc <> '' and zip1c = '$key'");
	  while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	   echo "<a target='peek' href='dispatcher.peek.php?server=$d[server_idc]&zip=$key'>".id2name($d[server_idc])."</a></br>";
	  }
	  $r=@mysql_query("select distinct server_idd, zip1d from ps_packets where server_idd <> '' and zip1d = '$key'");
	  while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	   echo "<a target='peek' href='dispatcher.peek.php?server=$d[server_idd]&zip=$key'>".id2name($d[server_idd])."</a></br>";
	  }
	  $r=@mysql_query("select distinct server_ide, zip1e from ps_packets where server_ide <> '' and zip1e = '$key'");
	  while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	   echo "<a target='peek' href='dispatcher.peek.php?server=$d[server_ide]&zip=$key'>".id2name($d[server_ide])."</a><br>";
	  }
	  
	 	$r=@mysql_query("select * from ps_users where level='gold member' and zip = '$key'");
	  while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
	   echo "<a style='background-color:#FFFF00' target='peek' href='dispatcher.peek.php?server=$d[id]&zip=$key'>$d[name]</a><br>";
	  }

	  
	  echo "</td>";
	  }
   }
   }
?>
</table>



<center>&copy; 2008 MDWestServe.com</center>

