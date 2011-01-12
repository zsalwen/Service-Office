<?
mysql_connect();
mysql_select_db('core');
include 'common.php';
// open this directory 
$myDirectory = opendir("/home/webuploads/");

// get each entry
while($entryName = readdir($myDirectory)) {
	$dirArray[] = $entryName;
}

// close directory
closedir($myDirectory);

//	count elements in array
$indexCount	= count($dirArray);
//Print ("$indexCount  files<br>\n");

// sort 'em
sort($dirArray);

// print 'em
print("User: ".$_COOKIE[psdata][name]);
print("<TABLE border=1 cellpadding=5 cellspacing=0 class=whitelinks>\n");
print("<TR><td>from</td><td>to</td><td>packet</td><td>defendant</td><td>description</td><td>status</td></TR>\n");
// loop through the array of files and print them all
for($index=0; $index < $indexCount; $index++) {
        if (substr("$dirArray[$index]", 0, 1) != "."){ // don't list hidden files
		$from = "/home/webuploads/$dirArray[$index]";
		$main =	explode('-',$dirArray[$index]);
		$sub =	explode('.pdf',$main[2]);
		$to="/data/service/scans/$main[0].$main[1].".time().$index.".pdf";
		$link="http://mdwestserve.com/affidavits/$main[0].$main[1].".time().$index.".pdf";
		print("<TR><td>$from</td>");
		print("<td>$to</td>");
		print("<td>$main[0]</td>");
		print("<td>$main[1]</td>");
		print("<td>$sub[0]</td>");
		print("<td>");
		if (!copy($from, $to)) {
			print("Failed Copy");
		}else{
			unlink($from);
			print("Passed Copy, Link Recorded");
			@mysql_query( "INSERT into ps_affidavits (packetID, defendantID, affidavit, userID, method, uploadDate) VALUES ('$main[0]','$main[1]','$link','".$_COOKIE[psdata][user_id]."','$sub[0]', NOW())");
		}
		print("</td>");
		print("</TR>\n");
	
	

	
	
	
	
	}
}
print("</TABLE>		<a href='http://staff.mdwestserve.com/transporter/'>Drag and drop uploads over [HERE].</a>
\n");



?>