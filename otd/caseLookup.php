<? 
include 'lock.php';
include 'common.php';
include 'nameParser.php';

function wdLink($packet){
	$link="<div style='display:inline; position:relative; left:90%;'><a target='_blank' href='http://staff.mdwestserve.com/otd/watchAdd.php?wdLink=$packet&close=1'>[+]</a></div>";
	return $link;
}

function wdCounty($county){
	if($county == 'PRINCE GEORGES'){
		$return="PRINCE GEORGE'S COUNTY";
	}elseif($county == 'QUEEN ANNES'){
		$return="QUEEN ANNE'S COUNTY";
	}elseif($county=='BALTIMORE CITY'){
		$return=$county;
	}else{
		$return=$county." COUNTY";
	}
	return $return;
}

function wdMarker($packet,$def){
	$q1="SELECT status, packetID from watchDog where packetID='$packet' AND defID='$def' LIMIT 0,1";
	$r1=@mysql_query($q1);
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	if ($d1[packetID] && $d1[status] == 'New Case Found'){
		return "<div style='color: green; display:inline;'>*</div>";
	}elseif ($d1[packetID] && $d1[status] != 'Search Complete'){
		return "<div style='color: red; display:inline;'>*</div>";
	}
}

function companyBtn($name,$county){
	$start=date('Y');
	$start="01/01/".($start-1);
	$end=date('m/d/Y');
	$link = "<form style='display:inline;' name='$packet-$def' action='http://casesearch.courts.state.md.us/inquiry/inquiryByCompany.jis' target='preview'>";
	$link .= "<input type='hidden' name='filingStart' value='$start'>";
	$link .= "<input type='hidden' name='filingEnd' value='$end'>";
	$link .= "<input type='hidden' name='lastName' value='$name'>";
	$link .= "<input type='hidden' name='filingDate' value=''>";
	$link .= "<input type='hidden' name='disclaimer' value='Y'>";
	$link .= "<input type='hidden' name='company' value='Y'>";
	$link .= "<input type='hidden' name='middleName' value=''>";
	$link .= "<input type='hidden' name='partytype' value=''>";
	$link .= "<input type='hidden' name='site' value='CIVIL'>";
	$link .= "<input type='hidden' name='courtSystem' value='C'>";
	$link .= "<input type='hidden' name='countyName' value='".addslashes($county)."'>";
	$link .= "<input type='hidden' name='action' value='Search'>";
	$link .= "<input type='submit' style='color: orange; background-color: orange; display:inline; width:15; height:15;' value='*'>";
	$link .= "</form>";
	return $link;
}

function searchBtn(){
	$start=date('Y');
	$start="01/01/".($start-1);
	$end=date('m/d/Y');
	$link = "<form style='display:inline;' name='$packet-$def' action='http://casesearch.courts.state.md.us/inquiry/processDisclaimer.jis' target='preview'>";
	$link .= "<input type='hidden' name='filingStart' value='$start'>";
	$link .= "<input type='hidden' name='filingEnd' value='$end'>";
	$link .= "<input type='hidden' name='filingDate' value=''>";
	$link .= "<input type='hidden' name='disclaimer' value='Y'>";
	$link .= "<input type='hidden' name='company' value='N'>";
	$link .= "<input type='submit' style='color: orange; background-color: orange; display:inline; width:15; height:15;' value='*'>";
	$link .= "</form>";
	return $link;
}

function searchForm2($packet,$def,$firstName,$lastName,$county){
	$start=date('Y');
	$start="01/01/".($start-1);
	$end=date('m/d/Y');
	$link = "<form style='display:inline;' name='$packet-$def' action='http://casesearch.courts.state.md.us/inquiry/inquirySearch.jis' target='preview'>";
	$link .= "<input type='hidden' name='disclaimer' value='Y'>";
	$link .= "<input type='hidden' name='lastName' value='$lastName'>";
	$link .= "<input type='hidden' name='firstName' value='$firstName'>";
	$link .= "<input type='hidden' name='middleName' value=''>";
	$link .= "<input type='hidden' name='partytype' value=''>";
	$link .= "<input type='hidden' name='site' value='CIVIL'>";
	$link .= "<input type='hidden' name='courtSystem' value='C'>";
	$link .= "<input type='hidden' name='countyName' value='".addslashes($county)."'>";
	$link .= "<input type='hidden' name='filingStart' value='$start'>";
	$link .= "<input type='hidden' name='filingEnd' value='$end'>";
	$link .= "<input type='hidden' name='filingDate' value=''>";
	$link .= "<input type='hidden' name='company' value='N'>";
	$link .= "<input type='hidden' name='action' value='Search'>";
	$link .= "<input type='submit' style='color: orange; background-color: orange; display:inline; width:15; height:15;' value='*'>";
	$link .= "</form>";
	return $link;
}

function watchLink($packet,$def,$name,$court){
	$county=wdCounty($court);
	$dN=' '.strtoupper($name).' ';
	if (strpos($dN,'LLC') || strpos($dN,' INC ') || strpos($dN,' INC.') || strpos($dN,' CO ') || strpos($dN,' CO ')){
		$link .= companyBtn(strtoupper($name),$county);
	}elseif(strpos($dN,'ESTATE')){
		$link .= searchBtn();
	}elseif($name != ''){
		//check name for aka, fka, nka
		if (strpos(strtoupper($name),'AKA')){
			$var=explode('AKA',strtoupper($name));
			$i=-1;
			//process each part of separated array
			while ($i < count($var)){$i++;
				if (trim($var["$i"]) != ''){
					$AKA=split_full_name(trim($var["$i"]));
					//cycle through $AKA array, analyze and make entry for each name
					$fname='';
					$lname='';
					//search first name for spaces
					if(strpos($AKA['fname'],' ')){
						$fname=explode(' ',$AKA['fname']);
						$fname=$fname[0];
					}
					//search first name for hyphen
					if(strpos($AKA['fname'],'-')){
						$fname=explode('-',$AKA['fname']);
						$fname=$fname[0];
					}
					//search last name for hyphen
					if(strpos($AKA['lname'],'-')){
						$lname=explode('-',$AKA['lname']);
						$lname=$lname[0];
					}
					//make separate watchDog entry for each permutation of name
					if (($fname != '') && ($lname != '')){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($fname)),addslashes(strtoupper($lname)),$county);
					}elseif($fname != ''){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($fname)),addslashes(strtoupper($AKA['lname'])),$county);
					}elseif($lname != ''){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($AKA['fname'])),addslashes(strtoupper($lname)),$county);
					}else{
						$link .= searchForm2($packet,$def,addslashes(strtoupper($AKA['fname'])),addslashes(strtoupper($AKA['lname'])),$county);
					}
				}
			}
		}elseif (strpos(strtoupper($name),'FKA')){
			$var=explode('FKA',strtoupper($name));
			$i=-1;
			//process each part of separated array
			while ($i < count($var)){$i++;
				if (trim($var["$i"]) != ''){
					$FKA=split_full_name(trim($var["$i"]));
					//cycle through $FKA array, analyze and make entry for each name
					$fname='';
					$lname='';
					//search first name for spaces
					if(strpos($FKA['fname'],' ')){
						$fname=explode(' ',$FKA['fname']);
						$fname=$fname[0];
					}
					//search first name for hyphen
					if(strpos($FKA['fname'],'-')){
						$fname=explode('-',$FKA['fname']);
						$fname=$fname[0];
					}
					//search last name for hyphen
					if(strpos($FKA['lname'],'-')){
						$lname=explode('-',$FKA['lname']);
						$lname=$lname[0];
					}
					//make separate watchDog entry for each permutation of name
					if (($fname != '') && ($lname != '')){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($fname)),addslashes(strtoupper($lname)),$county);
					}elseif($fname != ''){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($fname)),addslashes(strtoupper($FKA['lname'])),$county);
					}elseif($lname != ''){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($FKA['fname'])),addslashes(strtoupper($lname)),$county);
					}else{
						$link .= searchForm2($packet,$def,addslashes(strtoupper($FKA['fname'])),addslashes(strtoupper($FKA['lname'])),$county);
					}
				}
			}
		}elseif (strpos(strtoupper($name),'NKA')){
			$var=explode('NKA',strtoupper($name));
			$i=-1;
			//process each part of separated array
			while ($i < count($var)){$i++;
				if (trim($var["$i"]) != ''){
					$NKA=split_full_name(trim($var["$i"]));
					//cycle through $NKA array, analyze and make entry for each name
					$fname='';
					$lname='';
					//search first name for spaces
					if(strpos($NKA['fname'],' ')){
						$fname=explode(' ',$NKA['fname']);
						$fname=$fname[0];
					}
					//search first name for hyphen
					if(strpos($NKA['fname'],'-')){
						$fname=explode('-',$NKA['fname']);
						$fname=$fname[0];
					}
					//search last name for hyphen
					if(strpos($NKA['lname'],'-')){
						$lname=explode('-',$NKA['lname']);
						$lname=$lname[0];
					}
					//make separate watchDog entry for each permutation of name
					if (($fname != '') && ($lname != '')){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($fname)),addslashes(strtoupper($lname)),$county);
					}elseif($fname != ''){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($fname)),addslashes(strtoupper($NKA['lname'])),$county);
					}elseif($lname != ''){
						$link .= searchForm2($packet,$def,addslashes(strtoupper($NKA['fname'])),addslashes(strtoupper($lname)),$county);
					}else{
						$link .= searchForm2($packet,$def,addslashes(strtoupper($NKA['fname'])),addslashes(strtoupper($NKA['lname'])),$county);
					}
				}
			}
		}else{
			$nameArray=split_full_name(strtoupper($name));
			$fname='';
			$lname='';
			//search first name for spaces
			if(strpos($nameArray['fname'],' ')){
				$fname=explode(' ',trim($nameArray['fname']));
				$fname=$fname[0];
			}
			//search first name for hyphen
			if(strpos($nameArray['fname'],'-')){
				$fname=explode('-',$nameArray['fname']);
				$fname=$fname[0];
			}
			//search last name for hyphen
			if(strpos($nameArray['lname'],'-')){
				$lname=explode('-',$nameArray['lname']);
				$lname=$lname[0];
			}
			//make separate watchDog entry for each permutation of name
			if (($fname != '') && ($lname != '')){
				$link .= searchForm2($packet,$def,addslashes(strtoupper($fname)),addslashes(strtoupper($lname)),$county);
			}elseif($fname != ''){
				$link .= searchForm2($packet,$def,addslashes(strtoupper($fname)),addslashes(strtoupper($nameArray['lname'])),$county);
			}elseif($lname != ''){
				$link .= searchForm2($packet,$def,addslashes(strtoupper($nameArray['fname'])),addslashes(strtoupper($lname)),$county);
			}else{
				$link .= searchForm2($packet,$def,addslashes(strtoupper($nameArray['fname'])),addslashes(strtoupper($nameArray['lname'])),$county);
			}
		}
	}
	return $link;
}

function searchForm($packet,$def,$name,$court){
	$start=date('Y');
	$start="01/01/".($start-1);
	$end=date('m/d/Y');
	$q1="SELECT status, packetID, firstName, lastName, county, company from watchDog where packetID='$packet' AND defID='$def' LIMIT 0,1";
	$r1=@mysql_query($q1);
	$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
	if ($d1[packetID] != ''){
		$link = "<form style='display:inline;' name='$packet-$def' action='http://casesearch.courts.state.md.us/inquiry/inquirySearch.jis' target='preview'>";
		$link .= "<input type='hidden' name='disclaimer' value='Y'>";
		$link .= "<input type='hidden' name='lastName' value='".$d1[lastName]."'>";
		$link .= "<input type='hidden' name='firstName' value='".$d1[firstName]."'>";
		$link .= "<input type='hidden' name='middleName' value=''>";
		$link .= "<input type='hidden' name='partytype' value=''>";
		$link .= "<input type='hidden' name='site' value='CIVIL'>";
		$link .= "<input type='hidden' name='courtSystem' value='C'>";
		$link .= "<input type='hidden' name='countyName' value='".$d1[county]."'>";
		$link .= "<input type='hidden' name='filingStart' value='$start'>";
		$link .= "<input type='hidden' name='filingEnd' value='$end'>";
		$link .= "<input type='hidden' name='filingDate' value=''>";
		$link .= "<input type='hidden' name='company' value='".$d1[company]."'>";
		$link .= "<input type='hidden' name='action' value='Search'>";
		if ($d1[packetID] && $d1[status] == 'New Case Found'){
			$link .= "<input type='submit' style='color: green; background-color: green; display:inline; width:10; height:10;' value='*'>";
		}elseif ($d1[packetID] && $d1[status] != 'Search Complete'){
			$link .= "<input type='submit' style='color: red; background-color: red; display:inline; width:10; height:10;' value='*'>";
		}
		$link .= "</form>";
	}else{
		$link = watchLink($packet,$def,$name,$court);
	}
	return $link;
}

$i=0;
if ($_GET[mark]){
	psActivity("caseNumber");
}
?>
<style>
hr, ol, table{padding: 0px !important;}
li{font-size:10px; padding: 0px !important;}
body{background-color:#999999; padding: 0px !important;}
small{font-size:8px;}
</style>
<script>
function scrnHeight(field){
	var div1=document.getElementById(field);
	div1.style.height=screen.height-235;
}
</script>
<body onload="scrnHeight('div1')">
<table width="100%" height="100%" border="0" style="border-collapse:collapse; position:absolute;left:0px; top:0px;">
	<tr>
    	<td valign="top" bgcolor="#CCFFFF" width="15%" height="100%">
        <div id="div1" style="overflow:auto; width:100%; height:100%;">
		<a style="font-size:10px; padding: 0px !important;" href="http://casesearch.courts.state.md.us/inquiry/processDisclaimer.jis" target="preview">Load Case Lookup</a><hr>
<ol>
<?
$q="select process_status, client_file, packet_id, name1, name2, name3, name4, name5, name6, lossMit from ps_packets where case_no = '' and caseLookupFlag = '0' and filing_status <> 'FILED WITH COURT' and filing_status <> 'SEND TO CLIENT' and process_status <> 'DUPLICATE' and process_status <> 'DAMAGED PDF' AND attorneys_id <> '70' and (status = 'RECEIVED' or status = 'RECIEVED') ORDER BY packet_id";
$r=@mysql_query($q);
while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){$i++;
$FC = trim(getPage("http://data.mdwestserve.com/findFC.php?clientFile=".$d[client_file], 'MDWS File Copy for Packet'.$d[packet_id], '5', ''));
if ($FC != '' && $FC != '1'){
	$link=$FC;
}else{
	$link='';
}
$q1="SELECT status, packetID from watchDog where packetID='".$d[packet_id]."' LIMIT 0,1";
$r1=@mysql_query($q1);
$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
if($d[process_status] === 'READY'){
	echo "<div style='border:1px solid orange; background-color: orange;'>";
}elseif ($d1[packetID] && $d1[status] == 'New Case Found'){
	echo "<div style='border:1px solid green; background-color: #33FF00;'>";
}elseif ($d1[packetID] && $d1[status] != 'Search Complete'){
	echo "<div style='border:1px solid red;'>";
}
echo "<li><a name='$d[packet_id]'></a>".wdLink($d[packet_id])."<a href='order.php?packet=$d[packet_id]#case_no' target='order'>".strtoupper($d[packet_id]).'</a> <a href="?mark=1">DONE</a> '.$link.' ('.$d[lossMit].') <div>'.strtoupper(stripslashes($d[name1])).searchForm($d[packet_id],1,$d[name1],$d[circuit_court]).'</div><div>'.strtoupper(stripslashes($d[name2])).searchForm($d[packet_id],2,$d[name2],$d[circuit_court]).'</div><div>'.strtoupper(stripslashes($d[name3])).searchForm($d[packet_id],3,$d[name3],$d[circuit_court]).'</div><div>'.strtoupper(stripslashes($d[name4])).searchForm($d[packet_id],4,$d[name4],$d[circuit_court]).'</div><div>'.strtoupper(stripslashes($d[name5])).searchForm($d[packet_id],5,$d[name5],$d[circuit_court]).'</div><div>'.strtoupper(stripslashes($d[name6])).searchForm($d[packet_id],6,$d[name6],$d[circuit_court]).'</div></li>';
if (($d1[packetID] && $d1[status] != 'Search Complete') || ($d[process_status] == 'READY')){
	echo "</div>";
}
echo '<hr>';
        }
?>
</ol>
</div>
<center><div style='display:inline; font-size:10px; font-weight:bolder;'><a target='_blank' href='http://staff.mdwestserve.com/otd/watchAdd.php?all=1'>[ADD ALL NEW FILES]</a></div></center>
</td>
        <td valign="top">
        <table width="100%" height="100%" style="border-collapse:collapse;"><tr><td width="100%" height="50%">
        
        
        <iframe name="preview" id="preview" width="100%" height="100%" frameborder="0" src="http://casesearch.courts.state.md.us/inquiry/processDisclaimer.jis"></iframe>        
		</td></tr><tr><td width="100%" height="50%">
        <iframe id="order" name="order" width="100%" height="100%" frameborder="0" src=""></iframe>

        
        </tr></table>
        </td>

        
        
        
</tr>
</table>
</body>
<script>document.title='<?=$i?> Case Numbers Left to Look Up'</script>
<small><? include 'footer.php'; ?></small>