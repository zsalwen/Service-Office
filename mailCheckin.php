<? include 'common.php';

function mailCheckIn($art){
	$history = '<li>'.date('r').": Mail returned to sender.</li>";
	$r=@mysql_query("select status, history from usps where article = '$art'");
	$d=mysql_fetch_array($r,MYSQL_ASSOC);
	if($d[status]){
		$newHistory = $d[history].$history;
		@mysql_query("update usps set status='RETURNED TO SENDER', history = '$newHistory', processor='".$_COOKIE[psdata][name]."' where article = '$art'");
	}else{
		@mysql_query("insert into usps (article, status, history, processor) values ('$art','RETURNED TO SENDER','$history','".$_COOKIE[psdata][name]."') ");
	}

}

	//include 'menu.php';
if ($_GET[art]){
hardLog('Certified Mail Return CheckIn for '.$_GET[art],'user');
mailCheckIn($_GET[art]);
/*
	$q="update ps_packets set gcStatus='RETURNED' where article1 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update ps_packets set gcStatus='RETURNED' where article2 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update ps_packets set gcStatus='RETURNED' where article3 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update ps_packets set gcStatus='RETURNED' where article4 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update ps_packets set gcStatus='RETURNED' where article5 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update ps_packets set gcStatus='RETURNED' where article6 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	
	$num=0;
	while($num < 6){$num++;
		foreach(range('a','e') as $letter){
			$q="update ps_packets set gcStatus='RETURNED' where article$num$letter = '$_GET[art]'";
			@mysql_query($q) or die("Query: $q<br>".mysql_error());
			echo  mysql_affected_rows()."<br>";
		}
	}
	
	//check for EVs
	$q="update evictionPackets set gcStatus='RETURNED' where article1 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update evictionPackets set gcStatus='RETURNED' where article2 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update evictionPackets set gcStatus='RETURNED' where article3 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update evictionPackets set gcStatus='RETURNED' where article4 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update evictionPackets set gcStatus='RETURNED' where article5 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	$q="update evictionPackets set gcStatus='RETURNED' where article6 = '$_GET[art]'";
	@mysql_query($q) or die("Query: $q<br>".mysql_error());
	echo  mysql_affected_rows()."<br>";
	
	$num=0;
	while($num < 6){$num++;
		foreach(range('a','e') as $letter){
			$q="update evictionPackets set gcStatus='RETURNED' where article$num$letter = '$_GET[art]'";
			@mysql_query($q) or die("Query: $q<br>".mysql_error());
			echo  mysql_affected_rows()."<br>";
		}
	}
	*/
	?>
	<script>window.location.href='mailCheckin.php';</script>
<?
}
$status ="MDWestServe Mail Terminal Loaded... Ready for Logic Number!";


?>

<script>

function getObject(obj) {
  var theObj;
  if(document.all) {
    if(typeof obj=="string") {
      return document.all(obj);
    } else {
      return obj.style;
    }
  }
  if(document.getElementById) {
    if(typeof obj=="string") {
      return document.getElementById(obj);
    } else {
      return obj.style;
    }
  }
  return null;
}

function toCheck(entrance) {
  var entranceObj=getObject(entrance);
  var mystring=entranceObj.value;

if (mystring.match(/X$/)) {
	//alert("match");
	form1.submit();
	}

  
;
}


function toCount(entrance,exit,text,characters) {
  var entranceObj=getObject(entrance);
  var exitObj=getObject(exit);
  var length=characters - entranceObj.value.length;
  
	toCheck(entrance);
 
 if(length == 80) {
	//alert('ping');
		var uri;
		uri ='?art='+document.form1.text2.value+'&logic2=<?=$_GET[logic]?>';

	  window.location.href=uri;	}
  
  if(length <= 0) {
    length=0;
    text='<span class="disable"> '+text+' </span>';
    entranceObj.value=entranceObj.value.substr(0,characters);
    }
  exitObj.innerHTML = text.replace("{CHAR}",length);
  
}

</script>

<script language="JavaScript">
  <!--
    string="";
    function app(cc) {
      string+=cc;
      document.form1.text1.value=string;
	  toCount('text1','sBann','{CHAR} characters left',100);
    }
    function clear() {
      string="";
      document.form1.text1.value=string;
    }
    function calc() {
      if(string.length > 0) {
        inp="out="+string;
        eval(inp);
      } else out="0";
      document.form1.text1.value=out;
      string=""+out;
	  
    }
    function upda() {
	  string=""+document.form1.text1.value; 
	  window.location.href='?logic='+document.form1.text1.value;
	}
    function upda2() {
	  string=""+document.form1.text1.value; 
	}
  //-->
  </script>
  <body onLoad="clear()">  
 <br><br><br><br><br><br> <br><br><br><br><br><br>
  <table style="padding-left:150px;">
	 <form action="terminal.php" name="form1" method="POST" onSubmit="{calc(); return false;}"><tr>
	<td colspan='6'>Scan Certified Mail Reciept to mark green card received.<div style="width:600px;height:100px"><input style="width:1000px; height:100px;font-size:75px;" name="text2" onKeyUp="toCount('text2','sBann','{CHAR} characters left',100);" id="text2" value="" onChange="upda2()" ></div></td>
	<script>form1.text2.focus()</script>
	</tr>
</table>
</form>
<center><a href='terminal.php'><h1>ReSeT</h1></a></center>
<div style="position:absolute; bottom:0px; right:0px; height:40px; font-size:30px; width:100%; background-color:999999; color:ff0000;">
<span id="sBann" class="minitext">100 characters left.</span> - <?=$status?></div>

</form>


