<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Rad Upload Plus</title>
</head>
<script type="text/javascript" src="script.js"></script>

        <body>
         <table border="0" valign="middle" align="center" cellpadding="15">
          <tr>
   	   <td width="290" valign="top">
		   <h3 align="center">Rad Upload</h3>
		   <p>Thank you for downloading Rad Upload Plus</p>
		   
		   		   <p>The plus edition has all the features of Rad Upload standard edition and also includes
		   		   support for recursive folder upload and a configurable upload limit.</p>
		   
		   		   <p>Folders can be uploaded over HTTP as well as FTP, please refer to the online
                                   documentation to make full use of these features.
                                   </p>
                   
                                   <p>Please visit <a href="http://www.radinks.com/upload/?dn">Rad Upload</a> on the web for more information.
                   </p>
           </td>
           <td><div style="border: 1px solid #336699; padding:0px">

<?      
    $useApplet=0;
    $user_agent =$_SERVER['HTTP_USER_AGENT'];
    
    if(stristr($user_agent,"konqueror") || stristr($user_agent,"macintosh") || stristr($user_agent,"opera"))
    {
        $useApplet=1;
        echo '<applet name="Rad Upload Plus"
                        archive="dndplus.jar"
                        code="com.radinks.dnd.DNDAppletPlus"
                        width="290" MAYSCRIPT="yes" id="rup"
                        height="290">';
    }
    else
	{
        if(strstr($user_agent,"MSIE")) {
                echo '<script language="javascript" src="embed.js" type="text/javascript"></script>';
                echo '<script>IELoader()</script>';
        } else {
            echo '<object type="application/x-java-applet;version=1.4.1"
                    width= "290" height= "290"  id="rup" name="rup">';
            echo '  <param name="archive" value="dndplus.jar">
                    <param name="code" value="com.radinks.dnd.DNDAppletPlus">
                    <param name="name" value="Rad Upload Plus">';
        }
	}
?>
    <!-- BEGIN APPLET CONFIGURATION PARAMETERS -->
    <param name="max_upload" value="20000">
    <!-- Total file size in kilobytes  -->

     <param name = "message" value="Drop your files here. This message can be changed by editing the applet.php file. Check for the message property.">
    
    <!-- edit the above line to customize the welcome message displayed. example
    value='http://www.radinks.com/upload/init.html' -->
    <param name='url' value='http://67.131.250.110/upload.php'>

    <!-- you can pass additional parameters by adding them to the url-->

    <!-- to upload to an ftp server instead of a web server, please specify a url
         in the following format:
         
            ftp://username:password@ftp.myserver.com
            
         while replacing username, password and ftp.myserver.com with corresponding entries for your site -->
    <!-- END APPLET CONFIGURATION PARAMETERS -->
    
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
   	  </td>
   </tr>
  </table>
  <p>&nbsp;</p>
  <p align="center">A product of <a href="http://www.radinks.com/?dn">Rad Inks</a></p>
 </body>
</html>

