<html>
<head>
<?
if($_COOKIE[psdata][name]){
mysql_connect();
mysql_select_db('core');
$path = '/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j').'/';
if (!file_exists('/data/service/scans/'.date('Y'))){
mkdir ('/data/service/scans/'.date('Y'),0777);
}
if (!file_exists('/data/service/scans/'.date('Y').'/'.date('F'))){
mkdir ('/data/service/scans/'.date('Y').'/'.date('F'),0777);
}
if (!file_exists('/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j'))){
mkdir ('/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j'),0777);
}
$i=0;
$max=20;
while($i<$max){
$name = $_FILES["file_$i"][name];
if ($name){
echo "<li>Processing Upload: $name</li>";
$target_path = $path.$name;  
 if(move_uploaded_file($_FILES["file_$i"]['tmp_name'], $target_path)) {
$finalPATH = $target_path;
$finalURL = "http://mdwestserve.com/affidavits/".date('Y')."/".date('F')."/".date('j')."/".$name;
$finalURL2 = "http://".$_SERVER['HTTP_HOST']."/affidavits/".date('Y')."/".date('F')."/".date('j')."/".$name;

echo "<li>$name ready and listed as unclaimed.</li>";
@mysql_query("insert into attachment (server_id, processed, url, path, absolute_url) values ('".$_COOKIE[psdata][user_id]."', NOW(), '$finalURL','$finalPATH','$finalURL2')");
}else{
echo "<li>$name failed</li>";
}
}
$i++;
}
?>
<script src="multifile_compressed.js"></script>
</head>
<body>
<form enctype="multipart/form-data" action="upload.php" method = "post">
	<input id="my_file_element" type="file" name="file_1" >
	<input type="submit" value="Upload to unclaimed">
</form>
Files (20max):
<div id="files_list"></div>
<script>
	var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 20 );
	multi_selector.addElement( document.getElementById( 'my_file_element' ) );
</script>

<hr>
Your unclaimed uploads
<table>
 <tr>
  <td>processed</td>
  <td>a href</td>
  <td>js</td>
 </tr>
<? 
$r=@mysql_query("select * from attachment where server_id = '".$_COOKIE[psdata][user_id]."' and status = 'unclaimed' ");
while($d=mysql_fetch_array($r,MYSQL_ASSOC)){ ?>
 <tr>
  <td><?=$d[processed];?></td>
  <td><a href='<?=$d[url];?>' target='_Blank'><?=$d[url];?></a></td>
  <td onClick="parent.frames['pane2'].location.href = '<?=$d[url];?>'; "> <?=$d[url];?></td> <?=$d[url];?>
 </tr>
<? }?>
</table>


<? } ?>
</body>
</html>