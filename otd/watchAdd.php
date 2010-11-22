<? include 'common.php';
include 'nameParser.php';

function wdCounty($county){
	if($county == 'PRINCE GEORGES'){
		$return="PRINCE GEORGE&#39;S COUNTY";
	}elseif($county == 'QUEEN ANNES'){
		$return="QUEEN ANNE&#39;S COUNTY";
	}elseif($county=='BALTIMORE CITY'){
		$return=$county;
	}else{
		$return=$county." COUNTY";
	}
	return $return;
}

function wdAdd($packet){
	$q2="SELECT name1, name2, name3, name4, name5, name6, circuit_court from ps_packets WHERE packet_id='$packet'";
	$r2=@mysql_query($q2);
	$d2=mysql_fetch_array($r2,MYSQL_ASSOC);
	$county=wdCounty($d2[circuit_court]);
	$def=0;
	while($def < 6){$def++;
		$entries=0;
		$dN=' '.strtoupper($d2["name$def"]).' ';
		if (strpos($dN,'LLC')){
			echo "<i><b>$packet: ENTRY SKIPPED</b> - ".strtoupper($d2["name$def"])."</i>";
		}elseif(strpos($dN,' INC ')){
			echo "<i><b>$packet: ENTRY SKIPPED</b> - ".strtoupper($d2["name$def"])."</i>";
		}elseif(strpos($dN,' INC.')){
			echo "<i><b>$packet: ENTRY SKIPPED</b> - ".strtoupper($d2["name$def"])."</i>";
		}elseif(strpos($dN,' CO ')){
			echo "<i><b>$packet: ENTRY SKIPPED</b> - ".strtoupper($d2["name$def"])."</i>";
		}elseif(strpos($dN,' CO.')){
			echo "<i><b>$packet: ENTRY SKIPPED</b> - ".strtoupper($d2["name$def"])."</i>";
		}elseif(strpos($dN,'ESTATE')){
			echo "<i><b>$packet: ENTRY SKIPPED</b> - ".strtoupper($d2["name$def"])."</i>";
		}elseif($d2["name$def"] != ''){
			$entries=0;
			//check name for aka, fka, nka
			if (strpos(strtoupper($d2["name$def"]),'AKA')){
				$var=explode('AKA',strtoupper($d2["name$def"]));
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
						if (($fname != '') && ($lname != '')){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($fname))."', '".addslashes(strtoupper($lname))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($fname))." <small>LAST NAME:</small> ".addslashes(strtoupper($lname))."<br>";
						}elseif($fname != ''){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($fname))."', '".addslashes(strtoupper($AKA['lname']))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($fname))." <small>LAST NAME:</small> ".addslashes(strtoupper($AKA['lname']))."<br>";
						}elseif($lname != ''){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($AKA['fname']))."', '".addslashes(strtoupper($lname))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($AKA['fname']))." <small>LAST NAME:</small> ".addslashes(strtoupper($lname))."<br>";
						}else{
							$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($AKA['fname']))."', '".addslashes(strtoupper($AKA['lname']))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($AKA['fname']))." <small>LAST NAME:</small> ".addslashes(strtoupper($AKA['lname']))."<br>";
						}
					}
				}
			}elseif (strpos(strtoupper($d2["name$def"]),'FKA')){
				$var=explode('FKA',strtoupper($d2["name$def"]));
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
						if (($fname != '') && ($lname != '')){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($fname))."', '".addslashes(strtoupper($lname))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($fname))." <small>LAST NAME:</small> ".addslashes(strtoupper($lname))."<br>";
						}elseif($fname != ''){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($fname))."', '".addslashes(strtoupper($FKA['lname']))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($fname))." <small>LAST NAME:</small> ".addslashes(strtoupper($FKA['lname']))."<br>";
						}elseif($lname != ''){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($FKA['fname']))."', '".addslashes(strtoupper($lname))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($FKA['fname']))." <small>LAST NAME:</small> ".addslashes(strtoupper($lname))."<br>";
						}else{
							$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($FKA['fname']))."', '".addslashes(strtoupper($FKA['lname']))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($FKA['fname']))." <small>LAST NAME:</small> ".addslashes(strtoupper($FKA['lname']))."<br>";
						}
					}
				}
			}elseif (strpos(strtoupper($d2["name$def"]),'NKA')){
				$var=explode('NKA',strtoupper($d2["name$def"]));
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
						if (($fname != '') && ($lname != '')){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($fname))."', '".addslashes(strtoupper($lname))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($fname))." <small>LAST NAME:</small> ".addslashes(strtoupper($lname))."<br>";
						}elseif($fname != ''){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($fname))."', '".addslashes(strtoupper($NKA['lname']))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($fname))." <small>LAST NAME:</small> ".addslashes(strtoupper($NKA['lname']))."<br>";
						}elseif($lname != ''){$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($NKA['fname']))."', '".addslashes(strtoupper($lname))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($NKA['fname']))." <small>LAST NAME:</small> ".addslashes(strtoupper($lname))."<br>";
						}else{
							$entries++;
							$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($NKA['fname']))."', '".addslashes(strtoupper($NKA['lname']))."', '".addslashes($county)."', 'N')";
							@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
							echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($NKA['fname']))." <small>LAST NAME:</small> ".addslashes(strtoupper($NKA['lname']))."<br>";
						}
					}
				}
			}else{
				$nameArray=split_full_name(strtoupper($d2["name$def"]));
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
				if (($fname != '') && ($lname != '')){$entries++;
					$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($fname))."', '".addslashes(strtoupper($lname))."', '".addslashes($county)."', 'N')";
					@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
					echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($fname))." <small>LAST NAME:</small> ".addslashes(strtoupper($lname))."<br>";
				}elseif($fname != ''){$entries++;
					$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($fname))."', '".addslashes(strtoupper($nameArray['lname']))."', '".addslashes($county)."', 'N')";
					@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
					echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($fname))." <small>LAST NAME:</small> ".addslashes(strtoupper($nameArray['lname']))."<br>";
				}elseif($lname != ''){$entries++;
					$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($nameArray['fname']))."', '".addslashes(strtoupper($lname))."', '".addslashes($county)."', 'N')";
					@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
					echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($nameArray['fname']))." <small>LAST NAME:</small> ".addslashes(strtoupper($lname))."<br>";
				}else{
					$entries++;
					$q3="insert into watchDog (packetID, defID, firstName, lastName, county, company) values ('$packet', '$def', '".addslashes(strtoupper($nameArray['fname']))."', '".addslashes(strtoupper($nameArray['lname']))."', '".addslashes($county)."', 'N')";
					@mysql_query($q3) or die ("Query: $q3<br>".mysql_error());
					echo "<b>$packet:</b> <small>FIRST NAME:</small> ".addslashes(strtoupper($nameArray['fname']))." <small>LAST NAME:</small> ".addslashes(strtoupper($nameArray['lname']))."<br>";
				}
			}
			if($entries == 1){
				echo "<div>$entries entry made for ".$d2["name$def"]."</div>";
			}else{
				echo "<div>$entries entries made for ".$d2["name$def"]."</div>";
			}
		}
	}
}

if ($_GET[all]){
	$q="select packet_id from ps_packets where case_no = '' and caseLookupFlag = '0' and filing_status <> 'FILED WITH COURT' and filing_status <> 'SEND TO CLIENT' and process_status <> 'DUPLICATE' and process_status <> 'DAMAGED PDF' and (status = 'RECEIVED' or status = 'RECIEVED') AND attorneys_id <> '70' ORDER BY packet_id";
	$r=@mysql_query($q) or die ("Query: $q<br>".mysql_error());
	while ($d=mysql_fetch_array($r,MYSQL_ASSOC)){
		$q1="SELECT packetID from watchDog where packetID='".$d[packet_id]."'";
		$r1=@mysql_query($q1) or die ("Query: $q<br>".mysql_error());
		$d1=mysql_fetch_array($r1,MYSQL_ASSOC);
		if (!$d1[packetID]){
			wdAdd($d[packet_id]);
			//echo "<div>wdAdd $d[packet_id]</div>";
		}
	}
}
if ($_GET[wdLink]){
	$packet=$_GET[wdLink];
	wdAdd($packet);
}
if ($_GET[test]){
	$test=split_full_name($_GET[test]);
	echo "SALUTATION: ".$test['salutation']."<br>";
    echo "FIRST NAME: ".$test['fname']."<br>";
    echo "INITIALS: ".$test['initials']."<br>";
    echo "LAST NAME: ".$test['lname']."<br>";
    echo "SUFFIX: ".$test['suffix']."<br>";
}
if($_GET[close]){
	echo "<script>self.close();</script>";
}
?>