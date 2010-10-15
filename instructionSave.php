<?
$packet=$_GET[packet];
$unique = '/data/service/unknown/Service Instructions For Packet '.$packet.'.PDF';
if (file_exists($unique)) {
		header('Content-Disposition: attachment; filename="Service Instructions For Packet '.$packet.'.PDF"');
		readfile($unique);
		echo "<script>self.close();</script>";
	}else{
		echo "<center>".$unique." does not exist.</center><br>";
	}
?>