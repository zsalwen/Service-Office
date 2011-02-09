<?
mysql_connect();
mysql_select_db('service');
function id2attorney($id){
	$q="SELECT display_name FROM attorneys WHERE attorneys_id = '$id'";
	$r=@mysql_query($q);
	$d=mysql_fetch_array($r, MYSQL_ASSOC);
return $d[display_name];
}
function valueData($key){
  $r=@mysql_query("select valueData from config where keyData = '$key'");
  $d=mysql_fetch_array($r,MYSQL_ASSOC);
  return $d[valueData];
}
?>
<script type="text/javascript" src="script.js"></script>
<div style="border: 1px solid #336699; padding:0px;width:325px;height:450px;">

<?      
    $useApplet=0;
    $user_agent =$_SERVER['HTTP_USER_AGENT'];
    
    if(stristr($user_agent,"konqueror") || stristr($user_agent,"macintosh") || stristr($user_agent,"opera"))
    {
        $useApplet=1;
        echo '<applet name="Rad Upload Plus"
                        archive="dndplus.jar"
                        code="com.radinks.dnd.DNDAppletPlus"
                        width="325" MAYSCRIPT="yes" id="rup"
                        height="450">';
    }
    else
	{
        if(strstr($user_agent,"MSIE")) {
                echo '<script language="javascript" src="embed.js" type="text/javascript"></script>';
                echo '<script>IELoader()</script>';
        } else {
            echo '<object type="application/x-java-applet;version=1.4.1"
                    width= "325" height= "450"  id="rup" name="rup">';
            echo '  <param name="archive" value="dndplus.jar">
                    <param name="code" value="com.radinks.dnd.DNDAppletPlus">
                    <param name="name" value="Rad Upload Plus">';
        }
	}
	$login="autostart";
	$password=valueData($login);
?>
    <!-- BEGIN APPLET CONFIGURATION PARAMETERS -->
    <param name="max_upload" value="20000000">
    <!-- Total file size in kilobytes  -->

     <param name = "message" value="<?=$_COOKIE[psdata][email];?>, Drag and Drop your PDF's here.<br>System Time <?=date('r')?>">
    <param name='url' value='ftp://<?=$login?>:<?=$password?>@mdwestserve.com'>
    
<?
        echo '<param name="MAYSCRIPT" value="true">';
        echo '<param name="scriptable" value="true">';
        
		if(isset($_SERVER['PHP_AUTH_USER']))
		{
			printf('<param name="chap" value="%s">',
				base64_encode($_SERVER['PHP_AUTH_USER'].":".$_SERVER['PHP_AUTH_PW']));
		}
		if($useApplet == 1)
		{
			echo '</applet>';
		}
		else
		{
            echo '</object>';
		}
?>
		</div>
		<form action="http://staff.mdwestserve.com/orderUpload.php" method="post">
		<input type="hidden" name="uploadEmail" value="<?=$_COOKIE[psdata][email]?>">
		<textarea name="attorneyNotes" rows="4" cols="50"></textarea><br>
		<select name="attorneysID">
		<option value="">SELECT ATTORNEY</option>
		<?
		$q="SELECT DISTINCT attorneys_id FROM ps_packets";
		$r=@mysql_query($q) or die(mysql_error());
		while($d=mysql_fetch_array($r,MYSQL_ASSOC)){
			echo "<option value='".$d[attorneys_id]."'>".id2attorney($d[attorneys_id])."</option>";
		}
		?>
		</select><select name="svcType"><option>OTD</option><option>MAIL ONLY</option><option>EV</option><option>S</option></select><input type="submit" name="submit" value="Process Orders"></form>